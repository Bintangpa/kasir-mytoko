<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $userId = $request->input('user_id');

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $query = Transaction::with(['user', 'items'])
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Hitung Summary Statistik
        $totalTransactions = (clone $query)->count();
        $totalSales = (clone $query)->sum('total_price');

        // Hitung Total Modal (HPP) dalam periode ini
        $transactionIds = (clone $query)->pluck('id');
        $totalCost = TransactionItem::whereIn('transaction_id', $transactionIds)
            ->select(DB::raw('SUM(cost_price * quantity) as total_cost'))
            ->value('total_cost') ?? 0;

        $grossProfit = $totalSales - $totalCost;
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Ambil data transaksi dengan paginasi
        $transactions = $query->latest()->paginate(15)->withQueryString();

        // Kasir list untuk filter dropdown
        $cashiers = User::where('role', 'kasir')->orderBy('name')->get();

        return view('admin.reports.index', compact(
            'transactions',
            'cashiers',
            'startDate',
            'endDate',
            'userId',
            'totalTransactions',
            'totalSales',
            'totalCost',
            'grossProfit',
            'averageTransaction'
        ));
    }
}
