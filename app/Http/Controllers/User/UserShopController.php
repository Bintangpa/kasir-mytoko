<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\DuitkuService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserShopController extends Controller
{
    public function __construct(protected DuitkuService $duitku)
    {
    }

    /**
     * Halaman katalog produk (hanya melihat) untuk role user/pengguna
     */
    public function index(): View
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('user.products.index', compact('categories', 'products'));
    }

    /**
     * Halaman khusus keranjang belanja & pembayaran pengguna
     */
    public function cart(): View
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('user.cart.index', compact('products'));
    }

    /**
     * Proses pembayaran oleh pengguna (QRIS & Virtual Account Duitku Sandbox)
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:qris,va'],
            'va_bank' => ['nullable', 'in:bca,bni,bri,mandiri,permata'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Keranjang belanja tidak boleh kosong.',
            'items.min' => 'Pilih minimal satu produk untuk melakukan pembayaran.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran hanya mendukung QRIS atau Virtual Account (VA).',
            'va_bank.in' => 'Bank Virtual Account tidak didukung.',
        ]);

        $itemsInput = $request->items;
        $paymentMethod = $request->payment_method;
        $vaBank = $paymentMethod === 'va' ? strtolower($request->input('va_bank', 'bca')) : null;
        $vaBankUpper = $vaBank ? strtoupper($vaBank) : null;
        $userNote = $request->notes ? trim($request->notes) : '';
        $methodNote = $paymentMethod === 'va' ? "Virtual Account ({$vaBankUpper}) - Duitku Sandbox" : 'Pembayaran QRIS';
        $notes = $userNote !== '' ? "{$methodNote} | {$userNote}" : $methodNote;
        $user = Auth::user();

        try {
            $result = DB::transaction(function () use ($itemsInput, $paymentMethod, $vaBank, $notes, $user) {
                $subtotal = 0;
                $preparedItems = [];
                $updatedStocks = [];

                // Gabungkan item duplikat agar validasi stok akurat
                $mergedItems = [];
                foreach ($itemsInput as $item) {
                    $id = (int) $item['id'];
                    $qty = (int) $item['qty'];
                    $mergedItems[$id] = ($mergedItems[$id] ?? 0) + $qty;
                }

                foreach ($mergedItems as $productId => $qty) {
                    $product = Product::lockForUpdate()->findOrFail($productId);

                    if (!$product->is_active) {
                        throw new \Exception("Produk [{$product->name}] sedang tidak tersedia.");
                    }

                    if ($product->stock < $qty) {
                        throw new \Exception("Stok produk [{$product->name}] tidak mencukupi (Tersisa: {$product->stock}).");
                    }

                    $itemSubtotal = $product->price * $qty;
                    $subtotal += $itemSubtotal;

                    $product->decrement('stock', $qty);
                    $updatedStocks[$product->id] = $product->fresh()->stock;

                    $preparedItems[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'cost_price' => $product->cost_price,
                        'price' => $product->price,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                $totalPrice = $subtotal;
                $payAmount = $totalPrice;
                $changeAmount = 0;

                // Generate kode invoice unik anti-duplikasi
                $todayPrefix = 'TRX-' . Carbon::today()->format('Ymd') . '-';
                $latestTrx = Transaction::where('invoice_code', 'like', $todayPrefix . '%')
                    ->orderByDesc('id')
                    ->first();

                if ($latestTrx) {
                    $lastNumber = (int) substr($latestTrx->invoice_code, strlen($todayPrefix));
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }

                do {
                    $invoiceCode = $todayPrefix . str_pad($nextNumber++, 4, '0', STR_PAD_LEFT);
                } while (Transaction::where('invoice_code', $invoiceCode)->exists());

                $isOnlinePayment = in_array($paymentMethod, ['va', 'qris'], true);

                $trx = Transaction::create([
                    'invoice_code' => $invoiceCode,
                    'user_id' => $user->id,
                    'customer_name' => $user->name,
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'tax' => 0,
                    'total_price' => $totalPrice,
                    'pay_amount' => $payAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $isOnlinePayment ? 'pending' : 'paid',
                    'va_bank' => $vaBank,
                    'paid_at' => $isOnlinePayment ? null : now(),
                    'notes' => $notes,
                ]);

                foreach ($preparedItems as $prepItem) {
                    $trx->items()->create($prepItem);
                }

                $paymentUrl = null;
                $qrString = null;

                if ($paymentMethod === 'va') {
                    $trx->load(['items', 'user']);
                    $charge = $this->duitku->chargeVirtualAccount($trx, $vaBank);
                    $paymentUrl = $charge['payment_url'] ?? null;

                    $trx->update([
                        'payment_status' => $charge['payment_status'],
                        'va_bank' => $charge['va_bank'],
                        'va_number' => $charge['va_number'],
                        'va_biller_code' => $charge['va_biller_code'] ?? null,
                        'payment_url' => $paymentUrl,
                        'gateway_transaction_id' => $charge['gateway_transaction_id'],
                        'payment_expired_at' => $charge['payment_expired_at'],
                        'paid_at' => $charge['payment_status'] === 'paid' ? now() : null,
                    ]);
                } elseif ($paymentMethod === 'qris') {
                    $trx->load(['items', 'user']);
                    $charge = $this->duitku->chargeQris($trx);
                    $paymentUrl = $charge['payment_url'] ?? null;
                    $qrString = $charge['qr_string'] ?? null;

                    $trx->update([
                        'payment_status' => $charge['payment_status'],
                        'qr_string' => $qrString,
                        'payment_url' => $paymentUrl,
                        'gateway_transaction_id' => $charge['gateway_transaction_id'],
                        'payment_expired_at' => $charge['payment_expired_at'],
                        'paid_at' => $charge['payment_status'] === 'paid' ? now() : null,
                    ]);
                }

                return [
                    'transaction' => $trx->fresh(),
                    'updated_stocks' => $updatedStocks,
                    'payment_url' => $paymentUrl,
                    'qr_string' => $qrString,
                ];
            });

            /** @var Transaction $transaction */
            $transaction = $result['transaction'];
            $qrImageUrl = !empty($result['qr_string'])
                ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($result['qr_string'])
                : null;

            return response()->json([
                'success' => true,
                'message' => $transaction->isPending()
                    ? ($transaction->payment_method === 'qris'
                        ? 'QRIS Duitku Sandbox berhasil dibuat. Silakan scan kode QR atau buka simulator.'
                        : 'Nomor Virtual Account berhasil dibuat melalui Duitku Sandbox. Silakan selesaikan pembayaran.')
                    : 'Pembayaran berhasil diproses!',
                'invoice_code' => $transaction->invoice_code,
                'transaction_id' => $transaction->id,
                'total_price' => $transaction->total_price,
                'pay_amount' => $transaction->pay_amount,
                'change_amount' => $transaction->change_amount,
                'payment_method' => $transaction->payment_method,
                'payment_status' => $transaction->payment_status,
                'payment_label' => $transaction->payment_method === 'va'
                    ? 'Virtual Account (' . strtoupper((string) $transaction->va_bank) . ')'
                    : 'QRIS',
                'va_bank' => $transaction->va_bank ? strtoupper($transaction->va_bank) : null,
                'va_number' => $transaction->va_number,
                'va_biller_code' => $transaction->va_biller_code,
                'qr_string' => $transaction->qr_string ?? $result['qr_string'],
                'qr_image_url' => $qrImageUrl,
                'payment_expired_at' => $transaction->payment_expired_at
                    ? $transaction->payment_expired_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB'
                    : null,
                'payment_expired_iso' => $transaction->payment_expired_at?->timezone('Asia/Jakarta')->toIso8601String(),
                'payment_expired_ts' => $transaction->payment_expired_at?->timestamp,
                'simulator_url' => $result['payment_url'] ?? 'https://sandbox.duitku.com',
                'payment_url' => $result['payment_url'] ?? null,
                'status_check_url' => route('user.transactions.check-status', $transaction->id),
                'receipt_url' => route('transactions.receipt', $transaction->id),
                'updated_stocks' => $result['updated_stocks'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cek status pembayaran VA langsung ke Duitku Sandbox
     */
    public function checkPaymentStatus(Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        if ($transaction->isPaid()) {
            return response()->json([
                'success' => true,
                'payment_status' => 'paid',
                'is_paid' => true,
                'paid_at' => $transaction->paid_at ? $transaction->paid_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : null,
                'receipt_url' => route('transactions.receipt', $transaction->id),
                'message' => 'Pembayaran sudah berstatus Lunas.',
            ]);
        }

        try {
            $statusData = $this->duitku->getTransactionStatus($transaction->invoice_code);
            $updated = $this->duitku->syncTransactionStatus(
                $transaction,
                $statusData['duitku_status_code'],
                $statusData['gateway_transaction_id']
            );

            $statusMessage = match ($updated->payment_status) {
                'paid' => ($transaction->payment_method === 'qris'
                    ? 'Pembayaran QRIS berhasil dikonfirmasi Lunas!'
                    : 'Pembayaran Virtual Account berhasil dikonfirmasi Lunas!'),
                'expired' => 'Pembayaran telah kedaluwarsa. Stok produk telah dikembalikan.',
                'failed' => 'Transaksi dibatalkan atau ditolak oleh Duitku.',
                default => 'Pembayaran masih menunggu proses di Duitku Sandbox.',
            };

            return response()->json([
                'success' => true,
                'payment_status' => $updated->payment_status,
                'duitku_status' => $statusData['duitku_status_code'],
                'is_paid' => $updated->isPaid(),
                'paid_at' => $updated->paid_at ? $updated->paid_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : null,
                'receipt_url' => route('transactions.receipt', $updated->id),
                'message' => $statusMessage,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Riwayat pembelian / pembayaran milik pengguna yang sedang login
     */
    public function transactions(Request $request): View
    {
        $date = $request->input('date');

        $query = Transaction::with('items')
            ->where('user_id', Auth::id());

        if (!empty($date)) {
            $query->whereDate('created_at', $date);
        }

        $totalSpent = (clone $query)->where('payment_status', 'paid')->sum('total_price');
        $totalCount = (clone $query)->count();
        $transactions = $query->latest()->paginate(15)->withQueryString();

        return view('user.transactions.index', compact('transactions', 'totalSpent', 'totalCount', 'date'));
    }
}
