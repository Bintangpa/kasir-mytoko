@extends('layouts.app')

@section('title', 'Manajemen Produk')
@section('header-title', 'Manajemen Data Produk')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200/90 shadow-2xs">
        <div>
            <h2 class="text-base font-black text-[#0F0F0F]">Daftar Produk Toko</h2>
            <p class="text-xs text-slate-500">Kelola harga beli (modal), harga jual, barcode, dan stok inventaris.</p>
        </div>
        <div>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Produk Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk atau kode/barcode..."
                    class="w-full pl-9 pr-4 py-2 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-medium">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div>
                <select name="category_id" class="w-full px-3 py-2 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-slate-700 font-medium">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <select name="stock_status" class="w-full px-3 py-2 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-slate-700 font-medium">
                    <option value="">Status Stok</option>
                    <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Stok Tersedia (>5)</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis (≤5)</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-[#202020] hover:bg-[#0F0F0F] text-white font-bold text-xs rounded-xl transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F8F8F8] text-slate-500 border-b border-slate-100 uppercase tracking-wider text-[10px]">
                        <th class="p-3.5 pl-5">Kode / SKU</th>
                        <th class="p-3.5">Nama Produk</th>
                        <th class="p-3.5">Kategori</th>
                        <th class="p-3.5">Harga Modal</th>
                        <th class="p-3.5">Harga Jual</th>
                        <th class="p-3.5">Margin / Untung</th>
                        <th class="p-3.5">Stok</th>
                        <th class="p-3.5 pr-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $prod)
                        <tr class="hover:bg-[#F8F8F8]/80 transition-colors">
                            <td class="p-3.5 pl-5 font-mono font-bold text-[#337418]">
                                {{ $prod->code }}
                            </td>
                            <td class="p-3.5 font-bold text-[#0F0F0F]">
                                {{ $prod->name }}
                            </td>
                            <td class="p-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#F8F8F8] text-slate-700 border border-slate-200">
                                    {{ $prod->category->name ?? 'Tanpa Kategori' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-500 font-medium">
                                Rp {{ number_format($prod->cost_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 font-black text-[#0F0F0F]">
                                Rp {{ number_format($prod->price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-[#337418] font-bold">
                                +Rp {{ number_format($prod->price - $prod->cost_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5">
                                @if($prod->stock <= 0)
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700">
                                        Habis (0)
                                    </span>
                                @elseif($prod->stock <= 5)
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">
                                        {{ $prod->stock }} {{ $prod->unit }} (Menipis)
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30">
                                        {{ $prod->stock }} {{ $prod->unit }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 pr-5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.products.edit', $prod->id) }}" class="p-1.5 text-slate-600 hover:text-[#337418] hover:bg-[#5DD62C]/15 rounded-lg transition-colors" title="Edit Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Produk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Tidak ada data produk yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100 bg-[#F8F8F8]/50">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
