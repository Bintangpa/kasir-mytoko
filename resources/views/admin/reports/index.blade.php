@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('header-title', 'Laporan & Riwayat Penjualan')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full px-3.5 py-2 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="w-full px-3.5 py-2 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kasir / Pengguna</label>
                <select name="user_id" class="w-full px-3.5 py-2 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-slate-700 font-medium">
                    <option value="">Semua Akun</option>
                    @foreach($cashiers as $c)
                        <option value="{{ $c->id }}" {{ $userId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ ucfirst($c->role) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Pembayaran</label>
                <select name="payment_status" class="w-full px-3.5 py-2 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-slate-700 font-medium">
                    <option value="">Semua Status</option>
                    <option value="paid" {{ ($paymentStatus ?? '') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="pending" {{ ($paymentStatus ?? '') === 'pending' ? 'selected' : '' }}>Menunggu Bayar (Pending)</option>
                    <option value="expired" {{ ($paymentStatus ?? '') === 'expired' ? 'selected' : '' }}>Kedaluwarsa (Expired)</option>
                    <option value="failed" {{ ($paymentStatus ?? '') === 'failed' ? 'selected' : '' }}>Gagal / Batal</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-[#5DD62C] hover:bg-[#337418] active:bg-[#245311] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Terapkan Filter</span>
                </button>
                <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-[#202020] hover:bg-[#0F0F0F] text-white font-bold text-xs rounded-xl transition-colors shadow-2xs" title="Cetak Laporan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Omset Penjualan</span>
            <h3 class="text-xl sm:text-2xl font-black text-[#0F0F0F] mt-1">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
            <span class="text-[11px] text-slate-400 mt-1 block font-medium">Dari {{ $totalTransactions }} transaksi</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Modal (HPP)</span>
            <h3 class="text-xl sm:text-2xl font-black text-slate-700 mt-1">Rp {{ number_format($totalCost, 0, ',', '.') }}</h3>
            <span class="text-[11px] text-slate-400 mt-1 block font-medium">Harga pokok produk terjual</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Laba Kotor (Gross Profit)</span>
            <h3 class="text-xl sm:text-2xl font-black text-[#337418] mt-1">Rp {{ number_format($grossProfit, 0, ',', '.') }}</h3>
            <span class="text-[11px] text-[#337418] font-bold mt-1 block">Omset dikurangi HPP</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rata-rata per Transaksi</span>
            <h3 class="text-xl sm:text-2xl font-black text-[#337418] mt-1">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</h3>
            <span class="text-[11px] text-slate-400 mt-1 block font-medium">Nilai basket per struk</span>
        </div>
    </div>

    <!-- Transactions List Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-[#0F0F0F]">Rincian Transaksi Penjualan</h3>
                <p class="text-xs text-slate-500">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F8F8F8] text-slate-500 border-b border-slate-100 uppercase tracking-wider text-[10px]">
                        <th class="p-3.5 pl-5">No. Invoice</th>
                        <th class="p-3.5">Tanggal / Jam</th>
                        <th class="p-3.5">Kasir</th>
                        <th class="p-3.5">Pelanggan</th>
                        <th class="p-3.5">Rincian Item</th>
                        <th class="p-3.5">Metode</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Total Bayar</th>
                        <th class="p-3.5 pr-5 text-right">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-[#F8F8F8]/80 transition-colors">
                            <td class="p-3.5 pl-5 font-bold font-mono text-[#337418]">
                                {{ $trx->invoice_code }}
                            </td>
                            <td class="p-3.5 text-slate-600 font-medium">
                                {{ $trx->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-3.5 font-bold text-[#0F0F0F]">
                                {{ $trx->user->name ?? 'Kasir Dihapus' }}
                            </td>
                            <td class="p-3.5 text-slate-600">
                                {{ $trx->customer_name }}
                            </td>
                            <td class="p-3.5 text-slate-600 max-w-xs">
                                <div class="text-[11px] space-y-0.5">
                                    @foreach($trx->items as $item)
                                        <div>• {{ $item->product_name }} <span class="text-slate-400 font-medium">({{ $item->quantity }}x)</span></div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $trx->payment_method === 'cash' ? 'bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30' : 'bg-[#202020] text-[#5DD62C] border border-[#2A2A2A]' }}">
                                    {{ $trx->payment_method_label }}
                                </span>
                                @if($trx->va_number)
                                    <div class="font-mono text-[10px] text-slate-500 mt-0.5">{{ $trx->va_number }}</div>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($trx->isPaid())
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30">Lunas</span>
                                @elseif($trx->isPending())
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-200">Pending</span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-700 border border-rose-200">{{ ucfirst($trx->payment_status) }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-black text-[#0F0F0F]">
                                Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 pr-5 text-right">
                                <a href="{{ route('transactions.receipt', $trx->id) }}" target="_blank"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-[#337418] bg-[#5DD62C]/15 hover:bg-[#5DD62C] hover:text-[#0F0F0F] rounded-lg transition-all border border-[#5DD62C]/30">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Cetak</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">Tidak ada transaksi ditemukan pada periode filter ini.</td>
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
