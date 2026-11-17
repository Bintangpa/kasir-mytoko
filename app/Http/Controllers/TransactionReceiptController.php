<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionReceiptController extends Controller
{
    /**
     * Tampilkan struk kasir format thermal siap cetak
     */
    public function show(Transaction $transaction): View
    {
        $transaction->load(['user', 'items']);

        // Kasir hanya boleh mencetak transaksinya sendiri kecuali jika user adalah admin
        if (Auth::user()->isKasir() && $transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencetak struk transaksi ini.');
        }

        return view('receipts.print', compact('transaction'));
    }

    /**
     * Pratinjau dan Pengaturan Format Nota (Default 80mm Roll, 58mm Roll, 58x40 Label)
     */
    public function preview(): View
    {
        $transaction = Transaction::with(['user', 'items'])->latest()->first();

        if (!$transaction) {
            $transaction = new Transaction([
                'invoice_code' => 'TRX-' . date('Ymd') . '-0001',
                'customer_name' => 'Pelanggan Umum',
                'subtotal' => 28000,
                'discount' => 0,
                'tax' => 0,
                'total_price' => 28000,
                'pay_amount' => 50000,
                'change_amount' => 22000,
                'payment_method' => 'cash',
                'notes' => 'Contoh Nota Thermal Kasir 80mm Auto',
                'created_at' => now(),
            ]);

            $transaction->setRelation('items', collect([
                new TransactionItem(['product_name' => 'Aqua Air Mineral 600ml', 'quantity' => 2, 'price' => 3500, 'subtotal' => 7000]),
                new TransactionItem(['product_name' => 'Indomie Goreng Spesial', 'quantity' => 4, 'price' => 3500, 'subtotal' => 14000]),
                new TransactionItem(['product_name' => 'Teh Botol Sosro 250ml', 'quantity' => 1, 'price' => 4500, 'subtotal' => 4500]),
                new TransactionItem(['product_name' => 'Oreo Vanila 133g', 'quantity' => 1, 'price' => 10500, 'subtotal' => 10500]),
            ]));

            $transaction->setRelation('user', Auth::user());
        }

        return view('receipts.print', compact('transaction'));
    }
}
