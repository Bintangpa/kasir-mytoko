<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('kasir.pos.index', compact('categories', 'products'));
    }

    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'pay_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,qris,transfer'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Keranjang belanja tidak boleh kosong.',
            'items.min' => 'Pilih minimal satu produk untuk transaksi.',
            'pay_amount.required' => 'Nominal uang pembayaran wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        $itemsInput = $request->items;
        $payAmount = (float) $request->pay_amount;
        $discount = (float) ($request->discount ?? 0);
        $customerName = $request->customer_name ?: 'Pelanggan Umum';
        $paymentMethod = $request->payment_method;
        $notes = $request->notes;

        try {
            $transaction = DB::transaction(function () use ($itemsInput, $payAmount, $discount, $customerName, $paymentMethod, $notes) {
                $subtotal = 0;
                $preparedItems = [];

                // Validasi dan hitung barang
                foreach ($itemsInput as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['id']);
                    $qty = (int) $item['qty'];

                    if ($product->stock < $qty) {
                        throw new \Exception("Stok produk [{$product->name}] tidak mencukupi (Tersisa: {$product->stock}).");
                    }

                    $itemSubtotal = $product->price * $qty;
                    $subtotal += $itemSubtotal;

                    // Kurangi stok produk
                    $product->decrement('stock', $qty);

                    $preparedItems[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'cost_price' => $product->cost_price,
                        'price' => $product->price,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                $totalPrice = max(0, $subtotal - $discount);

                // Validasi uang bayar jika tunai
                if ($paymentMethod === 'cash' && $payAmount < $totalPrice) {
                    throw new \Exception("Jumlah uang pembayaran (Rp " . number_format($payAmount, 0, ',', '.') . ") kurang dari total belanja (Rp " . number_format($totalPrice, 0, ',', '.') . ").");
                }

                // Jika non-tunai, uang bayar otomatis disamakan dengan total jika tidak diisi lebih
                if ($paymentMethod !== 'cash' && $payAmount < $totalPrice) {
                    $payAmount = $totalPrice;
                }

                $changeAmount = max(0, $payAmount - $totalPrice);

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

                // Pengaman ganda agar kode invoice tidak pernah bentrok / duplikat
                do {
                    $invoiceCode = $todayPrefix . str_pad($nextNumber++, 4, '0', STR_PAD_LEFT);
                } while (Transaction::where('invoice_code', $invoiceCode)->exists());

                // Buat record transaksi
                $trx = Transaction::create([
                    'invoice_code' => $invoiceCode,
                    'user_id' => Auth::id(),
                    'customer_name' => $customerName,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => 0,
                    'total_price' => $totalPrice,
                    'pay_amount' => $payAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $paymentMethod,
                    'notes' => $notes,
                ]);

                // Simpan item transaksi
                foreach ($preparedItems as $prepItem) {
                    $trx->items()->create($prepItem);
                }

                return $trx;
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!',
                'invoice_code' => $transaction->invoice_code,
                'transaction_id' => $transaction->id,
                'total_price' => $transaction->total_price,
                'pay_amount' => $transaction->pay_amount,
                'change_amount' => $transaction->change_amount,
                'receipt_url' => route('transactions.receipt', $transaction->id),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
