<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $date = $request->input('date', Carbon::today()->toDateString());

        $query = Transaction::with(['items'])
            ->where('user_id', $userId);

        if ($request->filled('date')) {
            $query->whereDate('created_at', $date);
        }

        if ($request->filled('search')) {
            $query->where('invoice_code', 'like', "%{$request->search}%");
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        $dailyTotal = Transaction::paid()
            ->where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->sum('total_price');

        $dailyCount = Transaction::paid()
            ->where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->count();

        return view('kasir.transactions.index', compact('transactions', 'date', 'dailyTotal', 'dailyCount'));
    }
}
