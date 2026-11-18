<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransService
{
    protected string $serverKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->serverKey = (string) config('services.midtrans.server_key', '');
        $isProduction = (bool) config('services.midtrans.is_production', false);
        $this->baseUrl = $isProduction
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    /**
     * Buat transaksi Virtual Account melalui Midtrans Core API (/v2/charge)
     */
    public function chargeVirtualAccount(Transaction $transaction, string $bank): array
    {
        $this->ensureServerKeyConfigured();

        $bank = strtolower($bank);
        $transaction->loadMissing(['user', 'items']);

        $grossAmount = (int) round((float) $transaction->total_price);

        $itemDetails = $transaction->items->map(function ($item) {
            return [
                'id' => (string) ($item->product_id ?? $item->id),
                'price' => (int) round((float) $item->price),
                'quantity' => (int) $item->quantity,
                'name' => mb_substr($item->product_name, 0, 50),
            ];
        })->values()->all();

        // Pastikan total item_details sama persis dengan gross_amount (syarat ketat Midtrans)
        $itemsSum = array_reduce($itemDetails, fn ($sum, $i) => $sum + ($i['price'] * $i['quantity']), 0);
        if ($itemsSum !== $grossAmount) {
            $itemDetails = [[
                'id' => $transaction->invoice_code,
                'price' => $grossAmount,
                'quantity' => 1,
                'name' => 'Pembayaran ' . $transaction->invoice_code,
            ]];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $transaction->invoice_code,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $transaction->customer_name ?: ($transaction->user->name ?? 'Pelanggan'),
                'email' => $transaction->user->email ?? 'customer@mytokogue.test',
                'phone' => $transaction->user->phone ?? '081234567890',
            ],
            'item_details' => $itemDetails,
        ];

        if ($bank === 'mandiri') {
            $payload['payment_type'] = 'echannel';
            $payload['echannel'] = [
                'bill_info1' => 'Payment:',
                'bill_info2' => 'MyTokoGue POS',
            ];
        } elseif ($bank === 'permata') {
            $payload['payment_type'] = 'permata';
        } else {
            // bca, bni, bri
            $payload['payment_type'] = 'bank_transfer';
            $payload['bank_transfer'] = [
                'bank' => $bank,
            ];
        }

        $response = $this->client(20)
            ->post("{$this->baseUrl}/v2/charge", $payload);

        $data = $response->json() ?? [];
        $statusCode = (string) ($data['status_code'] ?? $response->status());

        if (!$response->successful() || !in_array($statusCode, ['200', '201'], true)) {
            $msg = $data['status_message'] ?? 'Gagal menghubungi Midtrans Sandbox.';
            $validationErrors = !empty($data['validation_messages'])
                ? ' (' . implode(', ', (array) $data['validation_messages']) . ')'
                : '';
            throw new RuntimeException("Midtrans Error [{$statusCode}]: {$msg}{$validationErrors}");
        }

        // Ekstrak nomor Virtual Account berdasarkan bank
        $vaNumber = null;
        $vaBillerCode = null;

        if ($bank === 'mandiri') {
            $vaBillerCode = $data['biller_code'] ?? null;
            $vaNumber = $data['bill_key'] ?? null;
        } elseif ($bank === 'permata') {
            $vaNumber = $data['permata_va_number'] ?? ($data['va_numbers'][0]['va_number'] ?? null);
        } else {
            $vaNumber = $data['va_numbers'][0]['va_number'] ?? null;
        }

        if (empty($vaNumber)) {
            throw new RuntimeException('Midtrans tidak mengembalikan nomor Virtual Account. Periksa aktivasi channel bank di dashboard Midtrans Sandbox.');
        }

        $expiryTime = !empty($data['expiry_time'])
            ? Carbon::parse($data['expiry_time'])
            : now()->addHours(24);

        return [
            'gateway_transaction_id' => $data['transaction_id'] ?? null,
            'transaction_status' => $data['transaction_status'] ?? 'pending',
            'payment_status' => $this->mapStatus((string) ($data['transaction_status'] ?? 'pending')),
            'va_bank' => $bank,
            'va_number' => $vaNumber,
            'va_biller_code' => $vaBillerCode,
            'payment_expired_at' => $expiryTime,
            'raw' => $data,
        ];
    }

    /**
     * Cek status transaksi langsung ke Midtrans Core API (/v2/{order_id}/status)
     */
    public function getTransactionStatus(string $orderId): array
    {
        $this->ensureServerKeyConfigured();

        $response = $this->client(15)
            ->get("{$this->baseUrl}/v2/" . urlencode($orderId) . '/status');

        $data = $response->json() ?? [];
        $statusCode = (string) ($data['status_code'] ?? $response->status());

        if (!$response->successful() || in_array($statusCode, ['401', '404', '500'], true)) {
            $msg = $data['status_message'] ?? 'Gagal mengecek status transaksi di Midtrans.';
            throw new RuntimeException("Midtrans Status Error [{$statusCode}]: {$msg}");
        }

        $midtransStatus = (string) ($data['transaction_status'] ?? 'pending');

        return [
            'gateway_transaction_id' => $data['transaction_id'] ?? null,
            'transaction_status' => $midtransStatus,
            'payment_status' => $this->mapStatus($midtransStatus),
            'raw' => $data,
        ];
    }

    /**
     * Verifikasi SHA-512 signature webhook notifikasi dari Midtrans
     */
    public function verifyWebhookSignature(array $payload): bool
    {
        if (empty($this->serverKey)) {
            return false;
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') {
            return false;
        }

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return hash_equals($expected, $signatureKey);
    }

    /**
     * Mapping status transaksi Midtrans ke status internal aplikasi
     */
    public function mapStatus(string $midtransStatus): string
    {
        return match (strtolower($midtransStatus)) {
            'settlement', 'capture' => 'paid',
            'pending' => 'pending',
            'expire' => 'expired',
            'cancel', 'deny', 'failure' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Sinkronisasi status transaksi ke database secara atomik (termasuk pengembalian stok jika expire/cancel)
     */
    public function syncTransactionStatus(Transaction $transaction, string $midtransStatus, ?string $gatewayTransactionId = null): Transaction
    {
        $mappedStatus = $this->mapStatus($midtransStatus);

        return DB::transaction(function () use ($transaction, $mappedStatus, $gatewayTransactionId) {
            /** @var Transaction $lockedTrx */
            $lockedTrx = Transaction::with('items')->lockForUpdate()->findOrFail($transaction->id);
            $previousStatus = $lockedTrx->payment_status;

            // Jika sebelumnya pending dan sekarang expired/failed, kembalikan stok produk
            if ($previousStatus === 'pending' && in_array($mappedStatus, ['expired', 'failed'], true)) {
                foreach ($lockedTrx->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }

            $updates = [
                'payment_status' => $mappedStatus,
            ];

            if ($gatewayTransactionId && empty($lockedTrx->gateway_transaction_id)) {
                $updates['gateway_transaction_id'] = $gatewayTransactionId;
            }

            if ($mappedStatus === 'paid' && empty($lockedTrx->paid_at)) {
                $updates['paid_at'] = now();
            }

            $lockedTrx->update($updates);

            return $lockedTrx->fresh(['items', 'user']);
        });
    }

    /**
     * URL Simulator Pembayaran Resmi Midtrans Sandbox berdasarkan Bank VA
     */
    public static function simulatorUrl(?string $bank): string
    {
        return match (strtolower((string) $bank)) {
            'bca' => 'https://simulator.sandbox.midtrans.com/bca/va/index',
            'bni' => 'https://simulator.sandbox.midtrans.com/bni/va/index',
            'bri' => 'https://simulator.sandbox.midtrans.com/bri/va/index',
            'mandiri' => 'https://simulator.sandbox.midtrans.com/mandiri/bill/index',
            'permata' => 'https://simulator.sandbox.midtrans.com/permata/va/index',
            default => 'https://simulator.sandbox.midtrans.com/v2/va/index',
        };
    }

    /**
     * HTTP Client terkonfigurasi aman untuk Windows/XAMPP (mencegah cURL error 35 TLS/IPv6/HTTP2 reset)
     */
    protected function client(int $timeout = 20): PendingRequest
    {
        $curlOptions = [
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
        ];

        return Http::withBasicAuth($this->serverKey, '')
            ->withHeaders(['Expect' => ''])
            ->acceptJson()
            ->asJson()
            ->connectTimeout(10)
            ->timeout($timeout)
            ->withOptions([
                'version' => 1.1,
                'force_ip_resolve' => 'v4',
                'curl' => $curlOptions,
            ])
            ->retry(3, 300, function (\Exception $exception): bool {
                // Retry otomatis hanya jika terjadi gangguan koneksi/TLS reset (bukan error 4xx/5xx API)
                return $exception instanceof ConnectionException;
            }, false);
    }

    protected function ensureServerKeyConfigured(): void
    {
        if (empty($this->serverKey)) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi pada file .env.');
        }
    }
}
