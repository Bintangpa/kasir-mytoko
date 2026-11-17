@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('header-title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-3xl mx-auto pt-2">
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 sm:p-8">
        <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-100">
            <div>
                <h2 class="text-base font-black text-[#0F0F0F]">Formulir Tambah Produk</h2>
                <p class="text-xs text-slate-500">Lengkapi data produk untuk mulai dijual di mesin kasir POS.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-600 hover:text-[#0F0F0F] flex items-center gap-1 transition-colors">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kode Produk / Barcode / SKU</label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="Contoh: PRD-001 (Kosongkan utk auto)"
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-mono">
                    @error('code') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Produk</label>
                    <select name="category_id" class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-slate-700 font-medium">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Produk <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Indomie Goreng Spesial"
                    class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-semibold">
                @error('name') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Harga Modal / Beli (HPP) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="cost_price" value="{{ old('cost_price') }}" required min="0" step="100" placeholder="0"
                            class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
                    </div>
                    @error('cost_price') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Harga Jual Kasir <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="price" value="{{ old('price') }}" required min="0" step="100" placeholder="0"
                            class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-black">
                    </div>
                    @error('price') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Stok Awal <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', 10) }}" required min="0"
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
                    @error('stock') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Satuan Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" required placeholder="pcs, kg, box, botol, dll."
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-medium">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan / Deskripsi (Opsional)</label>
                <textarea name="description" rows="2" placeholder="Catatan produk..."
                    class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-medium">{{ old('description') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-[#F8F8F8] rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#5DD62C] hover:bg-[#337418] active:bg-[#245311] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
