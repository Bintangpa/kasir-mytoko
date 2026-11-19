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

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
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

            <!-- Upload Foto Produk (Disimpan Langsung ke Database, Max 2 MB) -->
            <div class="p-4 sm:p-5 bg-[#F8F8F8] rounded-2xl border border-slate-200/90 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                    <div>
                        <label class="block text-xs font-black text-[#0F0F0F]">Foto Produk (Opsional)</label>
                        <p class="text-[11px] text-slate-500">Maksimal ukuran: <span class="font-black text-[#337418]">2 MB</span>. Format: <span class="font-bold text-slate-700">JPG, JPEG, PNG, WEBP</span></p>
                    </div>
                    <span class="self-start sm:self-auto px-2.5 py-1 rounded-lg text-[10px] font-bold bg-[#5DD62C]/15 text-[#337418] border border-[#5DD62C]/30 flex items-center gap-1.5">
                        <i class="fa-solid fa-database text-[10px]"></i>
                        <span>Disimpan ke Database</span>
                    </span>
                </div>

                <!-- Dropzone / File Picker Container -->
                <div id="imageUploadArea" class="relative border-2 border-dashed border-slate-300 hover:border-[#5DD62C] bg-white rounded-2xl p-5 text-center transition-colors group">
                    <input type="file" name="image" id="productImageInput" accept="image/jpeg,image/png,image/webp,image/jpg"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewProductImage(this)">
                    
                    <!-- Empty / Initial Prompt State -->
                    <div id="uploadPrompt" class="space-y-2 pointer-events-none">
                        <div class="w-12 h-12 rounded-2xl bg-[#F8F8F8] group-hover:bg-[#5DD62C]/15 text-slate-400 group-hover:text-[#337418] mx-auto flex items-center justify-center transition-colors shadow-2xs">
                            <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[#0F0F0F]"><span class="text-[#337418] underline">Pilih foto produk</span> atau seret file ke sini</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Ukuran maksimal 2 MB (Foto tersimpan langsung di database, bukan folder)</p>
                        </div>
                    </div>

                    <!-- Live Preview State (Hidden by default) -->
                    <div id="uploadPreviewWrap" class="hidden flex flex-col sm:flex-row items-center gap-4 text-left pointer-events-auto">
                        <div class="w-24 h-24 rounded-2xl overflow-hidden border border-slate-200 bg-[#F8F8F8] shrink-0 relative shadow-2xs">
                            <img id="imagePreviewImg" src="" alt="Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-[#5DD62C]/20 text-[#337418]">Foto Terpilih</span>
                                <span id="previewFileSize" class="text-[11px] font-bold text-slate-500">0 KB</span>
                            </div>
                            <p id="previewFileName" class="text-xs font-black text-[#0F0F0F] truncate mt-1">nama-file.jpg</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Siap disimpan langsung ke database saat tombol Simpan ditekan.</p>
                            <div class="mt-2.5">
                                <button type="button" onclick="clearImagePreview()"
                                    class="px-3 py-1 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-600 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-1.5 border border-rose-200/60">
                                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    <span>Hapus Pilihan Foto</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client-side Error Box -->
                <div id="imageErrorBox" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                    <span id="imageErrorMessage">Ukuran foto maksimal adalah 2 MB.</span>
                </div>

                @error('image')
                    <p class="text-[11px] text-rose-600 mt-1 font-bold">{{ $message }}</p>
                @enderror
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

@push('scripts')
<script>
    function previewProductImage(input) {
        const errorBox = document.getElementById('imageErrorBox');
        const errorMsg = document.getElementById('imageErrorMessage');
        const prompt = document.getElementById('uploadPrompt');
        const previewWrap = document.getElementById('uploadPreviewWrap');
        const previewImg = document.getElementById('imagePreviewImg');
        const previewFileName = document.getElementById('previewFileName');
        const previewFileSize = document.getElementById('previewFileSize');

        errorBox.classList.add('hidden');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxBytes = 2 * 1024 * 1024; // 2 MB

            // Validasi ukuran maksimal 2 MB
            if (file.size > maxBytes) {
                const actualMB = (file.size / (1024 * 1024)).toFixed(2);
                errorMsg.textContent = 'Ukuran file terlalu besar (' + actualMB + ' MB). Maksimal ukuran foto adalah 2 MB.';
                errorBox.classList.remove('hidden');
                input.value = '';
                return;
            }

            // Validasi format file
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                errorMsg.textContent = 'Format file tidak didukung! Harap gunakan file JPG, PNG, atau WEBP.';
                errorBox.classList.remove('hidden');
                input.value = '';
                return;
            }

            previewFileName.textContent = file.name;
            const sizeKB = Math.round(file.size / 1024);
            previewFileSize.textContent = sizeKB > 1024 ? (sizeKB / 1024).toFixed(1) + ' MB' : sizeKB + ' KB';

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                prompt.classList.add('hidden');
                previewWrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function clearImagePreview() {
        const input = document.getElementById('productImageInput');
        const prompt = document.getElementById('uploadPrompt');
        const previewWrap = document.getElementById('uploadPreviewWrap');
        const previewImg = document.getElementById('imagePreviewImg');
        const errorBox = document.getElementById('imageErrorBox');

        input.value = '';
        previewImg.src = '';
        previewWrap.classList.add('hidden');
        prompt.classList.remove('hidden');
        errorBox.classList.add('hidden');
    }
</script>
@endpush
