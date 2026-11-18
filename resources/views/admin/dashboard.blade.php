@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header-title', 'Dashboard Administrator')

@section('content')
<div class="space-y-6 pt-2">

    <!-- HERO / WELCOME BANNER (Palet Warna #0F0F0F, #202020, & #337418 dengan Aksen #5DD62C) -->
    <div class="bg-gradient-to-r from-[#0F0F0F] via-[#202020] to-[#337418] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-[#2A2A2A]">
        <div class="absolute -right-8 -bottom-8 w-56 h-56 bg-[#5DD62C]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-3 py-1 bg-[#5DD62C]/20 border border-[#5DD62C]/30 backdrop-blur-md rounded-full text-xs font-bold text-[#5DD62C] uppercase tracking-wider mb-2">
                    Ringkasan Bisnis & Penjualan
                </span>
                <h2 class="text-xl sm:text-2xl font-black flex items-center gap-2">
                    <span>Halo, {{ auth()->user()->name }}!</span>
                    <i class="fa-solid fa-hand text-amber-300 text-lg"></i>
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">Berikut adalah ikhtisar performa penjualan toko, inventori produk, dan aktivitas kasir hari ini.</p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('kasir.pos') }}" class="px-5 py-2.5 bg-[#5DD62C] hover:bg-[#337418] active:bg-[#245311] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-lg shadow-[#5DD62C]/25 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Buka POS / Kasir</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition-all flex items-center gap-2 border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Laporan Lengkap</span>
                </a>
            </div>
        </div>
    </div>

    <!-- METRICS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Pendapatan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pendapatan</p>
                <h3 class="text-xl sm:text-2xl font-black text-[#0F0F0F] mt-1">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#337418] mt-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Semua Transaksi
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#0F0F0F] text-[#5DD62C] flex items-center justify-center shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Card 2: Penjualan Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Penjualan Hari Ini</p>
                <h3 class="text-xl sm:text-2xl font-black text-[#337418] mt-1">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 mt-1">
                    {{ $todayTransactions }} Transaksi hari ini
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#5DD62C]/20 text-[#337418] flex items-center justify-center border border-[#5DD62C]/30 shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>

        <!-- Card 3: Estimasi Laba Kotor -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Estimasi Laba Kotor</p>
                <h3 class="text-xl sm:text-2xl font-black text-[#337418] mt-1">Rp {{ number_format($grossProfit, 0, ',', '.') }}</h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 mt-1">
                    Selisih Omset - Modal
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#337418]/15 text-[#337418] flex items-center justify-center border border-[#337418]/30 shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
        </div>

        <!-- Card 4: Total Produk & Stok Menipis -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Produk Aktif</p>
                <h3 class="text-xl sm:text-2xl font-black text-[#0F0F0F] mt-1">{{ $totalProducts }} <span class="text-sm font-normal text-slate-500">item</span></h3>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold {{ $lowStockProducts > 0 ? 'text-amber-600' : 'text-slate-500' }} mt-1">
                    @if($lowStockProducts > 0)
                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                        <span>{{ $lowStockProducts }} stok menipis (≤5)</span>
                    @else
                        <i class="fa-solid fa-circle-check text-[#337418]"></i>
                        <span>Semua stok aman</span>
                    @endif
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F8F8F8] text-[#202020] flex items-center justify-center border border-slate-200/60 shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
    </div>

    <!-- TREN 7 HARI & TOP PRODUK -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tren Penjualan 7 Hari Terakhir -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-black text-[#0F0F0F]">Tren Penjualan 7 Hari Terakhir</h3>
                    <p class="text-xs text-slate-500">Omset harian toko</p>
                </div>
            </div>

            <div class="grid grid-cols-7 gap-2 pt-4 items-end h-48">
                @php
                    $maxTotal = $last7Days->max('total') ?: 1;
                @endphp
                @foreach($last7Days as $day)
                    @php
                        $percentage = min(100, max(10, round(($day['total'] / $maxTotal) * 100)));
                    @endphp
                    <div class="flex flex-col items-center h-full justify-end group">
                        <div class="text-[10px] font-bold text-slate-600 mb-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{ number_format($day['total'] / 1000, 0) }}k
                        </div>
                        <div class="w-full bg-[#5DD62C]/10 rounded-t-xl relative flex items-end justify-center overflow-hidden" style="height: {{ $percentage }}%;">
                            <div class="w-full bg-gradient-to-t from-[#337418] to-[#5DD62C] h-full rounded-t-xl transition-all group-hover:brightness-110 shadow-sm"></div>
                        </div>
                        <div class="text-[11px] font-bold text-slate-700 mt-2">{{ $day['date'] }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">{{ $day['count'] }} trx</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 5 Produk Terlaris -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs">
            <h3 class="text-sm font-black text-[#0F0F0F] mb-1">Top Produk Terlaris</h3>
            <p class="text-xs text-slate-500 mb-4">Berdasarkan total kuantiti terjual</p>

            @if($topProducts->isEmpty())
                <div class="text-center py-8 text-xs text-slate-400">Belum ada data penjualan</div>
            @else
                <div class="space-y-3">
                    @foreach($topProducts as $idx => $prod)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#F8F8F8] border border-slate-200/80">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-[#5DD62C]/20 text-[#337418] text-xs font-black flex items-center justify-center border border-[#5DD62C]/30">
                                    {{ $idx + 1 }}
                                </span>
                                <div>
                                    <h4 class="text-xs font-bold text-[#0F0F0F] truncate max-w-[140px]">{{ $prod->product_name }}</h4>
                                    <span class="text-[10px] text-slate-500 font-medium">Terjual: {{ $prod->total_sold }} item</span>
                                </div>
                            </div>
                            <span class="text-xs font-black text-[#0F0F0F]">
                                Rp {{ number_format($prod->total_revenue, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- TABEL: TRANSAKSI TERBARU & PERINGATAN STOK -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 8 Transaksi Terbaru (Col Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-[#0F0F0F]">Transaksi Terbaru</h3>
                    <p class="text-xs text-slate-500">Aktivitas penjualan kasir terkini</p>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-[#337418] hover:text-[#0F0F0F]">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F8F8F8] text-slate-500 border-b border-slate-100 uppercase tracking-wider text-[10px]">
                            <th class="p-3.5 pl-5">Invoice</th>
                            <th class="p-3.5">Kasir / Pelanggan</th>
                            <th class="p-3.5">Metode</th>
                            <th class="p-3.5">Total Belanja</th>
                            <th class="p-3.5">Waktu</th>
                            <th class="p-3.5 pr-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions as $trx)
                            <tr class="hover:bg-[#F8F8F8]/80 transition-colors">
                                <td class="p-3.5 pl-5 font-bold font-mono text-[#337418]">
                                    {{ $trx->invoice_code }}
                                </td>
                                <td class="p-3.5">
                                    <div class="font-bold text-[#0F0F0F]">{{ $trx->customer_name }}</div>
                                    <div class="text-[10px] text-slate-400">Kasir: {{ $trx->user->name ?? 'Sistem' }}</div>
                                </td>
                                <td class="p-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $trx->payment_method === 'cash' ? 'bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30' : 'bg-[#202020] text-[#5DD62C] border border-[#2A2A2A]' }}">
                                        {{ $trx->payment_method_label }}
                                    </span>
                                    @if(!$trx->isPaid())
                                        <span class="block mt-1 text-[10px] font-bold {{ $trx->isPending() ? 'text-amber-600' : 'text-rose-600' }}">
                                            {{ $trx->isPending() ? 'Menunggu Bayar' : ucfirst($trx->payment_status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5 font-black text-[#0F0F0F]">
                                    Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                                </td>
                                <td class="p-3.5 text-slate-500">
                                    {{ $trx->created_at->format('H:i') }} <span class="text-[10px] text-slate-400">({{ $trx->created_at->format('d/m') }})</span>
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
                                <td colspan="6" class="p-8 text-center text-slate-400">Belum ada transaksi tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Peringatan Stok Menipis (Col Span 1) -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    <h3 class="text-sm font-black text-[#0F0F0F]">Peringatan Stok Menipis</h3>
                </div>
                <a href="{{ route('admin.products.index', ['stock_status' => 'low']) }}" class="text-xs font-bold text-[#337418] hover:text-[#0F0F0F]">
                    Kelola &rarr;
                </a>
            </div>
            <p class="text-xs text-slate-500 mb-4">Produk yang tersisa 5 unit atau kurang:</p>

            @if($lowStockList->isEmpty())
                <div class="p-6 text-center text-xs text-[#337418] bg-[#5DD62C]/10 rounded-2xl border border-[#5DD62C]/30 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-check text-[#337418] text-sm"></i>
                    <span class="font-bold">Luar biasa! Semua stok produk dalam batas aman.</span>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($lowStockList as $item)
                        <div class="p-3 rounded-2xl bg-amber-50/60 border border-amber-200 flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-[#0F0F0F]">{{ $item->name }}</h4>
                                <span class="text-[10px] text-slate-500 font-mono">{{ $item->code }} • {{ $item->category->name ?? 'Umum' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-0.5 rounded-md text-xs font-black {{ $item->stock == 0 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $item->stock }} {{ $item->unit }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
