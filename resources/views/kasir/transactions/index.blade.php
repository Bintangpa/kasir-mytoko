@extends('layouts.app')

@section('title', 'Riwayat Penjualan Saya')
@section('header-title', 'Riwayat Penjualan Shift Kasir')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Filter & Summary Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-black text-[#0F0F0F]">Riwayat Penjualan: {{ auth()->user()->name }}</h2>
            <p class="text-xs text-slate-500">Daftar transaksi yang Anda proses pada tanggal terpilih.</p>
        </div>

        <form method="GET" action="{{ route('kasir.transactions.index') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}"
                class="px-3.5 py-1.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
            <button type="submit" class="px-4 py-1.5 bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-xs transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Daily Quick Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Penjualan Tanggal {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
                <h3 class="text-xl sm:text-2xl font-black text-[#337418] mt-1">Rp {{ number_format($dailyTotal, 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#5DD62C]/20 text-[#337418] flex items-center justify-center font-black border border-[#5DD62C]/30 shadow-xs">
                Rp
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah Struk / Transaksi</span>
                <h3 class="text-xl sm:text-2xl font-black text-[#0F0F0F] mt-1">{{ $dailyCount }} Transaksi</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#0F0F0F] text-[#5DD62C] flex items-center justify-center font-black shadow-xs">
                #
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F8F8F8] text-slate-500 border-b border-slate-100 uppercase tracking-wider text-[10px]">
                        <th class="p-3.5 pl-5">Invoice</th>
                        <th class="p-3.5">Pelanggan</th>
                        <th class="p-3.5">Rincian Item</th>
                        <th class="p-3.5">Metode</th>
                        <th class="p-3.5">Total Belanja</th>
                        <th class="p-3.5">Uang Diterima</th>
                        <th class="p-3.5">Kembalian</th>
                        <th class="p-3.5">Waktu</th>
                        <th class="p-3.5 pr-5 text-right">Cetak Ulang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-[#F8F8F8]/80 transition-colors">
                            <td class="p-3.5 pl-5 font-mono font-bold text-[#337418]">
                                {{ $trx->invoice_code }}
                            </td>
                            <td class="p-3.5 font-bold text-[#0F0F0F]">
                                {{ $trx->customer_name }}
                            </td>
                            <td class="p-3.5 text-slate-600 max-w-xs font-medium">
                                <div class="text-[11px] space-y-0.5">
                                    @foreach($trx->items as $it)
                                        <div>• {{ $it->product_name }} <span class="text-slate-400 font-bold">({{ $it->quantity }}x)</span></div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $trx->payment_method === 'cash' ? 'bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30' : 'bg-[#202020] text-[#5DD62C] border border-[#2A2A2A]' }}">
                                    {{ $trx->payment_method }}
                                </span>
                            </td>
                            <td class="p-3.5 font-black text-[#0F0F0F]">
                                Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-slate-600 font-medium">
                                Rp {{ number_format($trx->pay_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 font-bold text-[#337418]">
                                Rp {{ number_format($trx->change_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-slate-500">
                                {{ $trx->created_at->format('H:i:s') }}
                            </td>
                            <td class="p-3.5 pr-5 text-right">
                                <a href="{{ route('transactions.receipt', $trx->id) }}" target="_blank"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-[#337418] bg-[#5DD62C]/15 hover:bg-[#5DD62C] hover:text-[#0F0F0F] rounded-lg transition-all border border-[#5DD62C]/30">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Struk</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">Tidak ada riwayat transaksi pada tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100 bg-[#F8F8F8]/50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
