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

        // Metrik Keuangan & Transaksi
        $totalSales = Transaction::sum('total_price');
        $todaySales = Transaction::whereDate('created_at', $today)->sum('total_price');
        $totalTransactions = Transaction::count();
        $todayTransactions = Transaction::whereDate('created_at', $today)->count();

        // Perkiraan Total Modal & Laba Kotor Keseluruhan
        $totalCost = TransactionItem::select(DB::raw('SUM(cost_price * quantity) as total_cost'))
            ->value('total_cost') ?? 0;
        $grossProfit = $totalSales - $totalCost;

        // Metrik Master Data
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 5)->count();
        $totalCategories = Category::count();
        $totalCashiers = User::where('role', 'kasir')->count();

        // 8 Transaksi Terbaru
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

        // 5 Produk Terlaris
        $topProducts = TransactionItem::select(
                'product_name',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Statistik Penjualan 7 Hari Terakhir
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayTotal = Transaction::whereDate('created_at', $date)->sum('total_price');
            $dayCount = Transaction::whereDate('created_at', $date)->count();

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
