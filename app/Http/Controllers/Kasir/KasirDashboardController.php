<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KasirDashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        $today = Carbon::today();

        // Metrik Kasir Hari Ini
        $todaySales = Transaction::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->sum('total_price');

        $todayTransactions = Transaction::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();

        $todayItemsSold = TransactionItem::whereHas('transaction', function ($q) use ($userId, $today) {
            $q->where('user_id', $userId)->whereDate('created_at', $today);
        })->sum('quantity');

        // Transaksi shift hari ini oleh kasir ini
        $recentTransactions = Transaction::with(['items'])
            ->where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->latest()
            ->take(10)
            ->get();

        // Ringkasan Stok Produk (Informasi cepat untuk kasir)
        $totalProducts = Product::where('is_active', true)->count();
        $availableProducts = Product::where('is_active', true)->where('stock', '>', 0)->count();
        $outOfStockCount = Product::where('is_active', true)->where('stock', '<=', 0)->count();

        return view('kasir.dashboard', compact(
            'todaySales',
            'todayTransactions',
            'todayItemsSold',
            'recentTransactions',
            'totalProducts',
            'availableProducts',
            'outOfStockCount'
        ));
    }
}
