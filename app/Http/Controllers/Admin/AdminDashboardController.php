<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        // Metrik Keuangan & Transaksi (Hanya menghitung transaksi berstatus LUNAS / paid)
        $totalSales = Transaction::paid()->sum('total_price');
        $todaySales = Transaction::paid()->whereDate('created_at', $today)->sum('total_price');
        $totalTransactions = Transaction::paid()->count();
        $todayTransactions = Transaction::paid()->whereDate('created_at', $today)->count();

        // Perkiraan Total Modal & Laba Kotor Keseluruhan (khusus transaksi lunas)
        $totalCost = TransactionItem::whereHas('transaction', fn ($q) => $q->paid())
            ->select(DB::raw('SUM(cost_price * quantity) as total_cost'))
            ->value('total_cost') ?? 0;
        $grossProfit = $totalSales - $totalCost;

        // Metrik Master Data
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 5)->count();
        $totalCategories = Category::count();
        $totalCashiers = User::where('role', 'kasir')->count();

        // 8 Transaksi Terbaru (termasuk transaksi VA pending agar admin bisa memantau)
        $recentTransactions = Transaction::with(['user', 'items'])
            ->latest()
            ->take(8)
            ->get();

        // Daftar Produk Stok Menipis (Stok <= 5)
        $lowStockList = Product::with('category')
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // 5 Produk Terlaris (khusus transaksi lunas)
        $topProducts = TransactionItem::whereHas('transaction', fn ($q) => $q->paid())
            ->select(
                'product_name',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Statistik Penjualan 7 Hari Terakhir (khusus transaksi lunas)
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayTotal = Transaction::paid()->whereDate('created_at', $date)->sum('total_price');
            $dayCount = Transaction::paid()->whereDate('created_at', $date)->count();

            $last7Days->push([
                'date' => $date->format('d M'),
                'day' => $date->translatedFormat('D'),
                'total' => $dayTotal,
                'count' => $dayCount,
            ]);
        }

        return view('admin.dashboard', compact(
            'totalSales',
            'todaySales',
            'totalTransactions',
            'todayTransactions',
            'grossProfit',
            'totalProducts',
            'lowStockProducts',
            'totalCategories',
            'totalCashiers',
            'recentTransactions',
            'lowStockList',
            'topProducts',
            'last7Days'
        ));
    }
}
