@extends('layouts.app')

@section('title', 'Dashboard Kasir')
@section('header-title', 'Dashboard Shift Kasir')

@section('content')
<div class="space-y-6 pt-2">

    <!-- HERO BANNER KASIR (Palet Warna #0F0F0F, #202020, #337418, #5DD62C) -->
    <div class="bg-gradient-to-r from-[#0F0F0F] via-[#202020] to-[#337418] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-[#2A2A2A]">
        <div class="absolute -right-8 -bottom-8 w-56 h-56 bg-[#5DD62C]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-3 py-1 bg-[#5DD62C]/20 border border-[#5DD62C]/30 backdrop-blur-md rounded-full text-xs font-bold text-[#5DD62C] uppercase tracking-wider mb-2">
                    Shift Pelayanan Aktif • {{ date('d F Y') }}
                </span>
                <h2 class="text-xl sm:text-2xl font-black flex items-center gap-2">
                    <span>Semangat Melayani, {{ auth()->user()->name }}!</span>
                    <i class="fa-solid fa-rocket text-[#5DD62C] text-lg"></i>
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">Siap melayani pelanggan dengan ramah dan cepat. Buka mesin POS untuk mulai transaksi.</p>
            </div>
            <div>
                <a href="{{ route('kasir.pos') }}" class="px-6 py-3.5 bg-[#5DD62C] text-[#0F0F0F] hover:bg-[#337418] hover:text-white active:bg-[#245311] font-black text-sm rounded-xl shadow-xl shadow-[#5DD62C]/25 transition-all flex items-center gap-2.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>BUKA MESIN KASIR (POS)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- METRICS SHIFT HARI INI -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Pendapatan Shift Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Uang Masuk Shift Hari Ini</p>
                <h3 class="text-xl sm:text-2xl font-black text-[#337418] mt-1">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 mt-1">
                    Diproses oleh Anda
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#5DD62C]/20 text-[#337418] flex items-center justify-center border border-[#5DD62C]/30 shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Jumlah Transaksi Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Struk / Transaksi</p>
                <h3 class="text-xl sm:text-2xl font-black text-[#0F0F0F] mt-1">{{ $todayTransactions }} <span class="text-sm font-normal text-slate-500">transaksi</span></h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 mt-1">
                    Hari ini ({{ date('d M') }})
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#0F0F0F] text-[#5DD62C] flex items-center justify-center shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>

        <!-- Total Item Terjual -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Item Terjual</p>
                <h3 class="text-xl sm:text-2xl font-black text-[#0F0F0F] mt-1">{{ $todayItemsSold }} <span class="text-sm font-normal text-slate-500">produk</span></h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 mt-1">
                    Kuantiti kumulatif
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#337418]/15 text-[#337418] flex items-center justify-center border border-[#337418]/30 shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS & STATUS TOKO -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('kasir.pos') }}" class="p-5 rounded-2xl bg-[#202020] hover:bg-[#2A2A2A] text-white shadow-md transition-all flex items-center gap-4 border-l-4 border-[#5DD62C] group">
            <div class="w-12 h-12 rounded-xl bg-[#5DD62C]/20 text-[#5DD62C] flex items-center justify-center shrink-0 group-hover:bg-[#5DD62C] group-hover:text-[#0F0F0F] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-white group-hover:text-[#5DD62C] transition-colors">Transaksi Baru (POS)</h4>
                <p class="text-xs text-slate-400">Buka mesin input belanja pelanggan</p>
            </div>
        </a>

        <a href="{{ route('kasir.transactions.index') }}" class="p-5 rounded-2xl bg-white hover:bg-[#F8F8F8] border border-slate-200/90 shadow-2xs transition-all flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-[#F8F8F8] text-[#202020] flex items-center justify-center shrink-0 border border-slate-200/70">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-[#0F0F0F]">Riwayat Penjualan Saya</h4>
                <p class="text-xs text-slate-500">Cetak ulang struk pelanggan sebelumnya</p>
            </div>
        </a>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-[#0F0F0F]">Status Inventaris</h4>
                <p class="text-xs text-slate-500">{{ $availableProducts }} Tersedia • <span class="{{ $outOfStockCount > 0 ? 'text-rose-600 font-bold' : '' }}">{{ $outOfStockCount }} Habis</span></p>
            </div>
        </div>
    </div>

    <!-- TRANSAKSI TERAKHIR KASIR INI HARI INI -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-[#0F0F0F]">Riwayat Transaksi Shift Anda Hari Ini</h3>
                <p class="text-xs text-slate-500">Daftar struk yang baru saja Anda terbitkan</p>
            </div>
            <a href="{{ route('kasir.transactions.index') }}" class="text-xs font-bold text-[#337418] hover:text-[#0F0F0F]">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F8F8F8] text-slate-500 border-b border-slate-100 uppercase tracking-wider text-[10px]">
                        <th class="p-3.5 pl-5">Invoice</th>
                        <th class="p-3.5">Pelanggan</th>
                        <th class="p-3.5">Item Terjual</th>
                        <th class="p-3.5">Metode Bayar</th>
                        <th class="p-3.5">Total Belanja</th>
                        <th class="p-3.5">Waktu</th>
                        <th class="p-3.5 pr-5 text-right">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $trx)
                        <tr class="hover:bg-[#F8F8F8]/80 transition-colors">
                            <td class="p-3.5 pl-5 font-bold font-mono text-[#337418]">
                                {{ $trx->invoice_code }}
                            </td>
                            <td class="p-3.5 font-bold text-[#0F0F0F]">
                                {{ $trx->customer_name }}
                            </td>
                            <td class="p-3.5 text-slate-600 font-medium">
                                {{ $trx->items->sum('quantity') }} item
                            </td>
                            <td class="p-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $trx->payment_method === 'cash' ? 'bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30' : 'bg-[#202020] text-[#5DD62C] border border-[#2A2A2A]' }}">
                                    {{ $trx->payment_method }}
                                </span>
                            </td>
                            <td class="p-3.5 font-black text-[#0F0F0F]">
                                Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-slate-500">
                                {{ $trx->created_at->format('H:i:s') }}
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
                            <td colspan="7" class="p-8 text-center text-slate-400">Anda belum memproses transaksi pada shift hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
