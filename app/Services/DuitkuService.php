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

class DuitkuService
{
    protected string $merchantCode;
    protected string $apiKey;
    protected string $baseUrl;
    protected bool $isProduction;

    /**
     * Peta kode channel pembayaran Virtual Account Duitku
     */
    public const BANK_CHANNELS = [
        'bca' => 'BC',
        'bni' => 'I1',
        'bri' => 'BR',
        'mandiri' => 'M2',
        'permata' => 'BT',
    ];

    public function __construct()
    {
        $this->merchantCode = (string) config('services.duitku.merchant_code', env('DUITKU_MERCHANT_CODE', ''));
        $this->apiKey = (string) config('services.duitku.api_key', env('DUITKU_API_KEY', ''));
        $this->isProduction = (bool) config('services.duitku.is_production', false);

        $this->baseUrl = $this->isProduction
            ? 'https://passport.duitku.com/webapi'
            : 'https://sandbox.duitku.com/webapi';
    }

    /**
     * Dapatkan kode channel Duitku dari nama bank
     */
    public function resolvePaymentMethod(string $bank): string
    {
        $normalized = strtolower(trim($bank));

        if (isset(self::BANK_CHANNELS[$normalized])) {
            return self::BANK_CHANNELS[$normalized];
        }

        // Jika sudah berupa kode channel Duitku resmi (misal BC, I1, BR, M2, BT)
        $upper = strtoupper(trim($bank));
        if (in_array($upper, self::BANK_CHANNELS, true)) {
            return $upper;
        }

        // Default ke BNI jika tidak dikenali
        return 'I1';
    }

    /**
     * Buat transaksi Virtual Account via Duitku Direct API (/v2/inquiry)
     */
    public function chargeVirtualAccount(Transaction $transaction, string $bank): array
    {
        $this->ensureConfigured();

        $transaction->loadMissing(['user', 'items']);
        $channelCode = $this->resolvePaymentMethod($bank);
        $amount = (int) round((float) $transaction->total_price);
        $orderId = $transaction->invoice_code;

        $signature = md5($this->merchantCode . $orderId . $amount . $this->apiKey);

        $customerName = $transaction->customer_name ?: ($transaction->user->name ?? 'Pelanggan');
        $customerEmail = $transaction->user->email ?? 'customer@mytokogue.test';
        $customerPhone = $transaction->user->phone ?? '081234567890';

        $payload = [
            'merchantCode' => $this->merchantCode,
            'paymentAmount' => $amount,
            'paymentMethod' => $channelCode,
            'merchantOrderId' => $orderId,
            'productDetails' => 'Pembayaran Belanja ' . $orderId,
            'email' => $customerEmail,
            'phoneNumber' => $customerPhone,
            'customerVaName' => mb_substr($customerName, 0, 30),
            'callbackUrl' => url('/webhooks/duitku'),
            'returnUrl' => route('user.transactions.index'),
            'expiryPeriod' => 1440, // 24 jam dalam menit
            'signature' => $signature,
        ];

        $response = $this->client(20)
            ->post("{$this->baseUrl}/api/merchant/v2/inquiry", $payload);

        $data = $response->json() ?? [];
        $statusCode = (string) ($data['statusCode'] ?? $response->status());

        if (!$response->successful() || $statusCode !== '00') {
            $msg = $data['statusMessage'] ?? 'Gagal membuat nomor Virtual Account di Duitku.';
            throw new RuntimeException("Duitku Error [{$statusCode}]: {$msg}");
        }

        $vaNumber = (string) ($data['vaNumber'] ?? '');
        if (empty($vaNumber)) {
            throw new RuntimeException('Duitku tidak mengembalikan nomor Virtual Account. Periksa konfigurasi proyek Duitku.');
        }

        $paymentUrl = $data['paymentUrl'] ?? null;
        $expiryTime = now()->addMinutes(1440);

        return [
            'gateway_transaction_id' => $data['reference'] ?? null,
            'transaction_status' => 'pending',
            'payment_status' => 'pending',
            'va_bank' => strtolower($bank),
            'va_channel_code' => $channelCode,
            'va_number' => $vaNumber,
            'va_biller_code' => null, // Duitku Mandiri sudah berupa VA tunggal
            'payment_url' => $paymentUrl,
            'payment_expired_at' => $expiryTime,
            'raw' => $data,
        ];
    }

    /**
     * Buat transaksi QRIS via Duitku Direct API (/v2/inquiry)
     */
    public function chargeQris(Transaction $transaction): array
    {
        $this->ensureConfigured();

        $transaction->loadMissing(['user', 'items']);
        $channelCode = 'SP'; // ShopeePay QRIS (didukung luas pada Duitku Sandbox)
        $amount = (int) round((float) $transaction->total_price);
        $orderId = $transaction->invoice_code;

        $signature = md5($this->merchantCode . $orderId . $amount . $this->apiKey);

        $customerName = $transaction->customer_name ?: ($transaction->user->name ?? 'Pelanggan');
        $customerEmail = $transaction->user->email ?? 'customer@mytokogue.test';
        $customerPhone = $transaction->user->phone ?? '081234567890';

        $payload = [
            'merchantCode' => $this->merchantCode,
            'paymentAmount' => $amount,
            'paymentMethod' => $channelCode,
            'merchantOrderId' => $orderId,
            'productDetails' => 'Pembayaran QRIS ' . $orderId,
            'email' => $customerEmail,
            'phoneNumber' => $customerPhone,
            'customerVaName' => mb_substr($customerName, 0, 30),
            'callbackUrl' => url('/webhooks/duitku'),
            'returnUrl' => route('user.transactions.index'),
            'expiryPeriod' => 60, // 60 menit
            'signature' => $signature,
        ];

        $response = $this->client(20)
            ->post("{$this->baseUrl}/api/merchant/v2/inquiry", $payload);

        $data = $response->json() ?? [];
        $statusCode = (string) ($data['statusCode'] ?? $response->status());

        if (!$response->successful() || $statusCode !== '00') {
            $msg = $data['statusMessage'] ?? 'Gagal membuat QRIS di Duitku.';
            throw new RuntimeException("Duitku Error [{$statusCode}]: {$msg}");
        }

        $qrString = (string) ($data['qrString'] ?? '');
        $paymentUrl = $data['paymentUrl'] ?? null;
        $expiryTime = now()->addMinutes(60);

        return [
            'gateway_transaction_id' => $data['reference'] ?? null,
            'transaction_status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'qris',
            'channel_code' => $channelCode,
            'qr_string' => $qrString,
            'payment_url' => $paymentUrl,
            'payment_expired_at' => $expiryTime,
            'raw' => $data,
        ];
    }

    /**
     * Cek status transaksi langsung ke Duitku (/api/merchant/transactionStatus)
     */
    public function getTransactionStatus(string $orderId): array
    {
        $this->ensureConfigured();

        $signature = md5($this->merchantCode . $orderId . $this->apiKey);

        $payload = [
            'merchantCode' => $this->merchantCode,
            'merchantOrderId' => $orderId,
            'signature' => $signature,
        ];

        $response = $this->client(15)
            ->post("{$this->baseUrl}/api/merchant/transactionStatus", $payload);

        $data = $response->json() ?? [];
        $statusCode = (string) ($data['statusCode'] ?? $response->status());

        if (!$response->successful() && !isset($data['statusCode'])) {
            $msg = $data['statusMessage'] ?? 'Gagal mengecek status transaksi di Duitku.';
            throw new RuntimeException("Duitku Status Error [{$statusCode}]: {$msg}");
        }

        return [
            'gateway_transaction_id' => $data['reference'] ?? null,
            'duitku_status_code' => $statusCode,
            'duitku_status_message' => $data['statusMessage'] ?? '',
            'payment_status' => $this->mapStatus($statusCode),
            'raw' => $data,
        ];
    }

    /**
     * Verifikasi MD5 signature webhook/callback notifikasi dari Duitku
     */
    public function verifyWebhookSignature(array $payload): bool
    {
        if (empty($this->apiKey) || empty($this->merchantCode)) {
            return false;
        }

        $merchantCode = (string) ($payload['merchantCode'] ?? '');
        $amount = (string) ($payload['amount'] ?? '');
        $orderId = (string) ($payload['merchantOrderId'] ?? '');
        $signature = (string) ($payload['signature'] ?? '');

        if ($merchantCode === '' || $amount === '' || $orderId === '' || $signature === '') {
            return false;
        }

        // Validasi kode merchant harus sesuai dengan merchant kita
        if ($merchantCode !== $this->merchantCode) {
            return false;
        }

        // Hitung signature MD5 standar Duitku
        $expected = md5($this->merchantCode . $amount . $orderId . $this->apiKey);

        if (hash_equals($expected, $signature)) {
            return true;
        }

        // Cek jika amount dikirim dengan pembulatan int
        $intAmount = (string) (int) round((float) $amount);
        $expectedInt = md5($this->merchantCode . $intAmount . $orderId . $this->apiKey);

        return hash_equals($expectedInt, $signature);
    }

    /**
     * Mapping status kode Duitku ke status internal aplikasi
     * - "00" => paid (Lunas)
     * - "01" => pending (Menunggu pembayaran)
     * - "02" / lainnya => failed / expired
     */
    public function mapStatus(string $statusCode): string
    {
        return match ($statusCode) {
            '00' => 'paid',
            '01' => 'pending',
            '02' => 'expired',
            default => 'failed',
        };
    }

    /**
     * Sinkronisasi status transaksi ke database secara atomik (termasuk pengembalian stok jika expire/cancel)
     */
    public function syncTransactionStatus(Transaction $transaction, string $statusCode, ?string $gatewayTransactionId = null): Transaction
    {
        $mappedStatus = $this->mapStatus($statusCode);

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
     * HTTP Client terkonfigurasi aman untuk Windows/XAMPP (mencegah cURL error 35 TLS/IPv6/HTTP2 reset)
     */
    protected function client(int $timeout = 20): PendingRequest
    {
        $curlOptions = [
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
        ];

        return Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Expect' => '',
        ])
        ->connectTimeout(10)
        ->timeout($timeout)
        ->withOptions([
            'version' => 1.1,
            'force_ip_resolve' => 'v4',
            'curl' => $curlOptions,
        ])
        ->retry(3, 300, function (\Exception $exception): bool {
            return $exception instanceof ConnectionException;
        }, false);
    }

    protected function ensureConfigured(): void
    {
        if (empty($this->merchantCode) || empty($this->apiKey)) {
            throw new RuntimeException('DUITKU_MERCHANT_CODE atau DUITKU_API_KEY belum dikonfigurasi pada file .env.');
        }
    }
}
