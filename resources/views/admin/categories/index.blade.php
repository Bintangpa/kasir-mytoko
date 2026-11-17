@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('header-title', 'Kategori Produk')

@section('content')
<div class="space-y-6 pt-2">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- FORM TAMBAH KATEGORI (1 COL) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs h-fit">
            <h3 class="text-sm font-black text-[#0F0F0F] mb-1">Tambah Kategori Baru</h3>
            <p class="text-xs text-slate-500 mb-5">Klasifikasikan produk agar kasir mudah mencarinya di POS.</p>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Minuman Dingin"
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
                    @error('name') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi / Keterangan</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi singkat..."
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-medium"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#5DD62C] hover:bg-[#337418] active:bg-[#245311] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all">
                    Simpan Kategori
                </button>
            </form>
        </div>

        <!-- DAFTAR KATEGORI (2 COL) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-[#0F0F0F]">Daftar Kategori Tersedia</h3>
                    <p class="text-xs text-slate-500">Total: {{ $categories->count() }} kategori</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F8F8F8] text-slate-500 border-b border-slate-100 uppercase tracking-wider text-[10px]">
                            <th class="p-3.5 pl-5">Nama Kategori</th>
                            <th class="p-3.5">Slug</th>
                            <th class="p-3.5">Jumlah Produk</th>
                            <th class="p-3.5 pr-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-[#F8F8F8]/80 transition-colors">
                                <td class="p-3.5 pl-5">
                                    <div class="font-bold text-[#0F0F0F]">{{ $cat->name }}</div>
                                    @if($cat->description)
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $cat->description }}</div>
                                    @endif
                                </td>
                                <td class="p-3.5 font-mono text-slate-500 font-bold">
                                    {{ $cat->slug }}
                                </td>
                                <td class="p-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-black bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30">
                                        {{ $cat->products_count }} Produk
                                    </span>
                                </td>
                                <td class="p-3.5 pr-5 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Kategori">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">Belum ada kategori yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
