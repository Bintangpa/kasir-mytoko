@extends('layouts.app')

@section('title', 'Edit Produk')
@section('header-title', 'Edit Data Produk')

@section('content')
<div class="max-w-3xl mx-auto pt-2">
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 sm:p-8">
        <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-100">
            <div>
                <h2 class="text-base font-black text-[#0F0F0F]">Edit Produk: {{ $product->name }}</h2>
                <p class="text-xs text-slate-500">Perbarui informasi harga, stok, atau status produk.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-600 hover:text-[#0F0F0F] flex items-center gap-1 transition-colors">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kode Produk / Barcode / SKU <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $product->code) }}" required
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-mono font-bold">
                    @error('code') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Produk</label>
                    <select name="category_id" class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-slate-700 font-medium">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Produk <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
                @error('name') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Harga Modal / Beli (HPP) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="cost_price" value="{{ old('cost_price', (int)$product->cost_price) }}" required min="0" step="100"
                            class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
                    </div>
                    @error('cost_price') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Harga Jual Kasir <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="price" value="{{ old('price', (int)$product->price) }}" required min="0" step="100"
                            class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-black">
                    </div>
                    @error('price') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Sisa Stok <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required min="0"
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
                    @error('stock') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Satuan Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-medium">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan / Deskripsi</label>
                <textarea name="description" rows="2"
                    class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-medium">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-[#337418] rounded border-slate-300 focus:ring-[#5DD62C]">
                    <span>Produk Aktif (Dapat dicari & dijual di mesin kasir POS)</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-[#F8F8F8] rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#5DD62C] hover:bg-[#337418] active:bg-[#245311] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
