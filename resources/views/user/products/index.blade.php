@extends('layouts.app')

@section('title', 'Katalog Produk')
@section('header-title', 'Katalog Produk')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-1 sm:pt-2 pb-8">

    <!-- HERO BANNER PENGGUNA (Kompak di HP, Luas di Desktop) -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#0F0F0F] via-[#202020] to-[#337418] p-4 sm:p-7 text-white shadow-xl border border-[#2A2A2A]">
        <div class="absolute -right-12 -top-12 w-48 sm:w-56 h-48 sm:h-56 rounded-full bg-[#5DD62C]/15 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-[#5DD62C]/15 border border-[#5DD62C]/30 text-[10px] sm:text-[11px] font-bold text-[#5DD62C] mb-1.5 sm:mb-2">
                    <i class="fa-solid fa-bag-shopping text-[10px] sm:text-xs"></i>
                    <span>Katalog Produk Toko</span>
                </div>
                <h2 class="text-lg sm:text-2xl font-black tracking-tight truncate">Halo, {{ auth()->user()->name }}</h2>
                <p class="text-[11px] sm:text-sm text-slate-300 mt-0.5 sm:mt-1 max-w-xl leading-snug">
                    Pilih produk yang Anda inginkan, lalu klik ikon troli di kanan atas untuk melakukan pembayaran.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('user.transactions.index') }}"
                   class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl bg-[#202020]/90 hover:bg-[#2A2A2A] text-white border border-white/15 font-bold text-xs transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-[#5DD62C]"></i>
                    <span>Riwayat</span>
                </a>
                <a href="{{ route('user.cart') }}"
                   class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl bg-[#5DD62C] hover:bg-[#4dc223] text-[#0F0F0F] font-black text-xs shadow-md shadow-[#5DD62C]/20 transition-all">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Buka Keranjang</span>
                </a>
            </div>
        </div>
    </div>

    <!-- BAR PENCARIAN, SWITCHER TAMPILAN HP & FILTER KATEGORI -->
    <div class="bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs space-y-2.5 sm:space-y-3">
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" id="searchInput" placeholder="Cari produk atau kode SKU..."
                    class="w-full pl-9 sm:pl-10 pr-3 py-2 sm:py-2.5 bg-[#F8F8F8] focus:bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:border-[#5DD62C] text-[#0F0F0F] transition-all">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs sm:text-sm absolute left-3 sm:left-3.5 top-2.5 sm:top-3"></i>
            </div>

            <!-- Switcher Susunan Produk Khusus Handphone (Grid 2 Kolom vs List 1 Kolom) -->
            <div class="flex sm:hidden items-center bg-[#F8F8F8] p-1 rounded-xl border border-slate-200 shrink-0" role="group" aria-label="Susunan Produk">
                <button type="button" id="btnLayoutGrid" onclick="setMobileLayout('grid')"
                    class="w-8 h-7 rounded-lg flex items-center justify-center text-xs bg-[#5DD62C] text-[#0F0F0F] font-bold shadow-2xs transition-all"
                    title="Susunan Grid 2 Kolom">
                    <i class="fa-solid fa-grip"></i>
                </button>
                <button type="button" id="btnLayoutList" onclick="setMobileLayout('list')"
                    class="w-8 h-7 rounded-lg flex items-center justify-center text-xs text-slate-500 hover:text-[#0F0F0F] transition-all"
                    title="Susunan Daftar 1 Kolom">
                    <i class="fa-solid fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Filter Kategori Geser Horizontal (Touch Friendly) -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-0.5 snap-x" id="categoryTabs">
            <button type="button" onclick="filterCategory('all')"
                class="category-btn active snap-start px-3 sm:px-3.5 py-1.5 rounded-xl text-[11px] sm:text-xs font-black transition-all whitespace-nowrap bg-[#5DD62C] text-[#0F0F0F] shadow-xs" data-category="all">
                Semua ({{ $products->count() }})
            </button>
            @foreach($categories as $cat)
                <button type="button" onclick="filterCategory('{{ $cat->id }}')"
                    class="category-btn snap-start px-3 sm:px-3.5 py-1.5 rounded-xl text-[11px] sm:text-xs font-bold transition-all whitespace-nowrap bg-[#F8F8F8] text-slate-700 hover:bg-slate-200/70 border border-slate-200/80" data-category="{{ $cat->id }}">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- GRID KATALOG PRODUK FULL-WIDTH (2 Kolom di HP, 3-4 Kolom di Tablet/Desktop) -->
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-4" id="productGrid">
        @forelse($products as $prod)
            <div class="product-card bg-white p-2.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-[#5DD62C] hover:shadow-md transition-all flex flex-col justify-between relative min-w-0 {{ $prod->stock <= 0 ? 'opacity-65' : '' }}"
                 data-id="{{ $prod->id }}"
                 data-code="{{ strtolower($prod->code) }}"
                 data-name="{{ strtolower($prod->name) }}"
                 data-category="{{ $prod->category_id }}"
                 data-price="{{ (float)$prod->price }}"
                 data-stock="{{ $prod->stock }}">

                <div class="min-w-0">
                    <!-- Baris Atas: SKU (Desktop/List) + Status Stok + Indikator Terpilih -->
                    <div class="flex items-center justify-between gap-1 mb-1.5 sm:mb-2.5">
                        <span class="card-sku hidden sm:inline-block font-mono text-[10px] font-bold text-slate-400 bg-[#F8F8F8] px-2 py-0.5 rounded-md border border-slate-200/60 truncate">{{ $prod->code }}</span>

                        <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-1">
                            @if($prod->stock <= 0)
                                <span class="stock-badge px-1.5 sm:px-2 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold bg-rose-100 text-rose-700 whitespace-nowrap">Habis</span>
                            @elseif($prod->stock <= 5)
                                <span class="stock-badge px-1.5 sm:px-2 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold bg-amber-100 text-amber-800 whitespace-nowrap">Sisa {{ $prod->stock }}</span>
                            @else
                                <span class="stock-badge px-1.5 sm:px-2 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold bg-[#5DD62C]/15 text-[#337418] border border-[#5DD62C]/25 whitespace-nowrap">Stok {{ $prod->stock }}</span>
                            @endif

                            <span class="cart-indicator-badge hidden px-1.5 sm:px-2 py-0.5 rounded-full bg-[#5DD62C] text-[#0F0F0F] font-black text-[9px] sm:text-[10px] shadow-xs items-center gap-0.5 sm:gap-1 shrink-0">
                                <i class="fa-solid fa-check text-[8px] sm:text-[9px]"></i> <span class="cart-indicator-qty">0</span>
                            </span>
                        </div>
                    </div>

                    <!-- Kategori & Nama Produk -->
                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-[#337418] block truncate">{{ $prod->category->name ?? 'Umum' }}</span>
                    <h3 class="text-xs sm:text-sm font-black text-[#0F0F0F] leading-snug mt-0.5 line-clamp-2 min-h-[2.1rem] sm:min-h-[2.5rem]">{{ $prod->name }}</h3>
                    @if($prod->description)
                        <p class="card-desc hidden sm:line-clamp-2 text-[11px] text-slate-500 mt-1 leading-relaxed">{{ $prod->description }}</p>
                    @endif
                </div>

                <!-- Harga & Tombol Aksi -->
                <div class="mt-2.5 sm:mt-4 pt-2 sm:pt-3 border-t border-slate-100 space-y-2 sm:space-y-2.5">
                    <div class="flex items-baseline justify-between gap-1">
                        <span class="text-[13px] sm:text-base font-black text-[#0F0F0F] tracking-tight truncate">
                            Rp {{ number_format($prod->price, 0, ',', '.') }}
                        </span>
                        <span class="text-[9px] sm:text-[10px] font-semibold text-slate-400 shrink-0">/ {{ $prod->unit ?? 'pcs' }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="openProductDetail({{ $prod->id }})"
                            class="btn-detail-card w-8 h-8 sm:w-auto sm:h-auto sm:px-2.5 sm:py-2 rounded-xl bg-[#F8F8F8] hover:bg-slate-200/80 active:bg-slate-200 text-slate-700 font-bold text-[11px] border border-slate-200 transition-colors flex items-center justify-center gap-1 shrink-0"
                            title="Lihat detail produk">
                            <i class="fa-regular fa-eye text-xs sm:text-[11px]"></i>
                            <span class="detail-label hidden sm:inline">Detail</span>
                        </button>
                        <button type="button" onclick="addToCart({{ $prod->id }})"
                            class="btn-add-cart flex-1 h-8 sm:h-auto sm:py-2 px-2 rounded-xl bg-[#0F0F0F] hover:bg-[#5DD62C] active:bg-[#5DD62C] text-white hover:text-[#0F0F0F] active:text-[#0F0F0F] disabled:opacity-40 disabled:cursor-not-allowed font-black text-[11px] transition-all flex items-center justify-center gap-1 shadow-2xs"
                            {{ $prod->stock <= 0 ? 'disabled' : '' }}>
                            <i class="fa-solid fa-cart-plus text-[11px]"></i>
                            <span>{{ $prod->stock <= 0 ? 'Habis' : 'Pilih' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-10 text-center text-slate-400">
                <i class="fa-solid fa-box-open text-3xl text-slate-300 mb-2 block"></i>
                <p class="text-xs font-bold text-slate-600">Belum ada produk yang tersedia saat ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Empty Search State -->
    <div id="noProductsFound" class="hidden bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-10 text-center text-slate-400">
        <i class="fa-solid fa-magnifying-glass text-3xl text-slate-300 mb-2 block"></i>
        <p class="text-xs font-bold text-slate-600">Produk tidak ditemukan.</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Coba gunakan kata kunci atau kategori lain.</p>
    </div>

</div>

<!-- TOAST FEEDBACK TAMBAH KERANJANG -->
<div id="cartToast" class="fixed bottom-4 right-4 left-4 sm:left-auto z-40 max-w-sm bg-[#0F0F0F] text-white border border-[#2E2E2E] rounded-2xl shadow-2xl p-3.5 flex items-center justify-between gap-3 translate-y-24 opacity-0 pointer-events-none transition-all duration-300">
    <div class="flex items-center gap-2.5 min-w-0">
        <div id="cartToastIconWrap" class="w-8 h-8 rounded-xl bg-[#5DD62C]/20 text-[#5DD62C] flex items-center justify-center shrink-0">
            <i id="cartToastIcon" class="fa-solid fa-check text-xs"></i>
        </div>
        <div class="min-w-0">
            <p id="cartToastTitle" class="text-xs font-black text-white truncate">Ditambahkan ke Keranjang</p>
            <p id="cartToastSubtitle" class="text-[11px] text-slate-400 truncate">Produk siap dibayar</p>
        </div>
    </div>
    <a href="{{ route('user.cart') }}" class="px-3 py-1.5 rounded-xl bg-[#5DD62C] hover:bg-[#4dc223] text-[#0F0F0F] font-black text-[11px] whitespace-nowrap shrink-0 transition-colors">
        Lihat Troli
    </a>
</div>

<!-- MODAL DETAIL PRODUK (READ-ONLY — Bottom Sheet di HP, Centered Dialog di Desktop) -->
<div id="productDetailModal" class="fixed inset-0 z-50 bg-[#0F0F0F]/80 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 hidden">
    <div class="bg-white rounded-t-3xl sm:rounded-3xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="p-4 sm:p-5 bg-[#0F0F0F] text-white flex items-center justify-between shrink-0">
            <div class="min-w-0 pr-2">
                <span id="detailCategory" class="text-[10px] font-bold uppercase tracking-wider text-[#5DD62C]">Kategori</span>
                <h3 id="detailName" class="text-sm sm:text-base font-black text-white mt-0.5 truncate">Nama Produk</h3>
            </div>
            <button type="button" onclick="closeProductDetail()" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-[#202020] transition-colors shrink-0">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
        <div class="p-4 sm:p-5 space-y-3.5 sm:space-y-4 text-xs overflow-y-auto">
            <div class="grid grid-cols-3 gap-2">
                <div class="p-2.5 sm:p-3 rounded-2xl bg-[#F8F8F8] border border-slate-200/80">
                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase block">Kode SKU</span>
                    <span id="detailCode" class="font-mono font-black text-[#0F0F0F] mt-0.5 block text-[11px] sm:text-xs truncate">-</span>
                </div>
                <div class="p-2.5 sm:p-3 rounded-2xl bg-[#F8F8F8] border border-slate-200/80">
                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase block">Satuan</span>
                    <span id="detailUnit" class="font-bold text-[#0F0F0F] mt-0.5 block capitalize text-[11px] sm:text-xs truncate">-</span>
                </div>
                <div class="p-2.5 sm:p-3 rounded-2xl bg-[#F8F8F8] border border-slate-200/80">
                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase block">Stok</span>
                    <span id="detailStock" class="font-black text-[#337418] mt-0.5 block text-[11px] sm:text-xs truncate">-</span>
                </div>
            </div>

            <div class="p-3 sm:p-3.5 rounded-2xl bg-[#5DD62C]/10 border border-[#5DD62C]/30 flex items-center justify-between">
                <span class="text-xs font-bold text-[#337418]">Harga Satuan</span>
                <span id="detailPrice" class="text-base sm:text-lg font-black text-[#0F0F0F]">Rp 0</span>
            </div>

            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Deskripsi Produk</span>
                <p id="detailDescription" class="text-xs text-slate-600 leading-relaxed bg-[#F8F8F8] p-3.5 rounded-2xl border border-slate-200/80">
                    Tidak ada deskripsi tambahan.
                </p>
            </div>

            <div class="flex items-center gap-2 pt-1 pb-2 sm:pb-0">
                <button type="button" onclick="closeProductDetail()"
                    class="flex-1 py-2.5 px-4 bg-[#F8F8F8] hover:bg-slate-200 text-[#0F0F0F] font-bold rounded-xl border border-slate-200 transition-colors">
                    Tutup
                </button>
                <button type="button" id="detailAddCartBtn"
                    class="flex-1 py-2.5 px-4 bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white disabled:opacity-40 disabled:cursor-not-allowed font-black rounded-xl transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-cart-plus"></i>
                    <span>Pilih Produk</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const rawProducts = @json($products);
    const productsMap = {};
    rawProducts.forEach(p => { productsMap[p.id] = p; });

    let cart = [];
    let activeCategory = 'all';
    let toastTimeout = null;

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
    }

    // Muat keranjang tersimpan & sinkronkan terhadap stok terbaru dari server
    function loadCart() {
        const stored = window.getUserCart ? window.getUserCart() : [];
        cart = stored
            .filter(item => productsMap[item.id] && productsMap[item.id].stock > 0)
            .map(item => {
                const prod = productsMap[item.id];
                const clampedQty = Math.min(Math.max(1, parseInt(item.qty, 10) || 1), prod.stock);
                return {
                    id: prod.id,
                    name: prod.name,
                    price: parseFloat(prod.price),
                    stock: prod.stock,
                    qty: clampedQty
                };
            });

        if (window.saveUserCart) {
            window.saveUserCart(cart);
        }
        updateCatalogIndicators();
    }

    function showToast(title, subtitle, isWarning = false) {
        const toast = document.getElementById('cartToast');
        const iconWrap = document.getElementById('cartToastIconWrap');
        const icon = document.getElementById('cartToastIcon');

        document.getElementById('cartToastTitle').textContent = title;
        document.getElementById('cartToastSubtitle').textContent = subtitle;

        if (isWarning) {
            iconWrap.className = 'w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0';
            icon.className = 'fa-solid fa-triangle-exclamation text-xs';
        } else {
            iconWrap.className = 'w-8 h-8 rounded-xl bg-[#5DD62C]/20 text-[#5DD62C] flex items-center justify-center shrink-0';
            icon.className = 'fa-solid fa-check text-xs';
        }

        toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
        }, 2800);
    }

    // Switcher susunan produk di layar handphone (2 kolom vs 1 kolom)
    function setMobileLayout(mode) {
        const grid = document.getElementById('productGrid');
        const btnGrid = document.getElementById('btnLayoutGrid');
        const btnList = document.getElementById('btnLayoutList');

        if (mode === 'list') {
            grid.classList.remove('grid-cols-2');
            grid.classList.add('grid-cols-1');
            btnList.className = 'w-8 h-7 rounded-lg flex items-center justify-center text-xs bg-[#5DD62C] text-[#0F0F0F] font-bold shadow-2xs transition-all';
            btnGrid.className = 'w-8 h-7 rounded-lg flex items-center justify-center text-xs text-slate-500 hover:text-[#0F0F0F] transition-all';

            document.querySelectorAll('.card-sku').forEach(el => {
                el.classList.remove('hidden');
                el.classList.add('inline-block');
            });
            document.querySelectorAll('.card-desc').forEach(el => {
                el.classList.remove('hidden');
                el.classList.add('line-clamp-2');
            });
            document.querySelectorAll('.detail-label').forEach(el => {
                el.classList.remove('hidden');
                el.classList.add('inline');
            });
            document.querySelectorAll('.btn-detail-card').forEach(el => {
                el.classList.remove('w-8');
                el.classList.add('px-3');
            });
        } else {
            grid.classList.remove('grid-cols-1');
            grid.classList.add('grid-cols-2');
            btnGrid.className = 'w-8 h-7 rounded-lg flex items-center justify-center text-xs bg-[#5DD62C] text-[#0F0F0F] font-bold shadow-2xs transition-all';
            btnList.className = 'w-8 h-7 rounded-lg flex items-center justify-center text-xs text-slate-500 hover:text-[#0F0F0F] transition-all';

            document.querySelectorAll('.card-sku').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('inline-block');
            });
            document.querySelectorAll('.card-desc').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('line-clamp-2');
            });
            document.querySelectorAll('.detail-label').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('inline');
            });
            document.querySelectorAll('.btn-detail-card').forEach(el => {
                el.classList.add('w-8');
                el.classList.remove('px-3');
            });
        }
    }

    // Filter kategori
    function filterCategory(catId) {
        activeCategory = String(catId);
        document.querySelectorAll('.category-btn').forEach(btn => {
            if (btn.dataset.category === activeCategory) {
                btn.className = 'category-btn active snap-start px-3 sm:px-3.5 py-1.5 rounded-xl text-[11px] sm:text-xs font-black transition-all whitespace-nowrap bg-[#5DD62C] text-[#0F0F0F] shadow-xs';
            } else {
                btn.className = 'category-btn snap-start px-3 sm:px-3.5 py-1.5 rounded-xl text-[11px] sm:text-xs font-bold transition-all whitespace-nowrap bg-[#F8F8F8] text-slate-700 hover:bg-slate-200/70 border border-slate-200/80';
            }
        });
        applySearchAndFilter();
    }

    document.getElementById('searchInput').addEventListener('input', applySearchAndFilter);

    function applySearchAndFilter() {
        const keyword = document.getElementById('searchInput').value.trim().toLowerCase();
        let visibleCount = 0;

        document.querySelectorAll('.product-card').forEach(card => {
            const matchCat = activeCategory === 'all' || card.dataset.category === activeCategory;
            const matchSearch = !keyword || card.dataset.name.includes(keyword) || card.dataset.code.includes(keyword);

            if (matchCat && matchSearch) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const noFound = document.getElementById('noProductsFound');
        if (visibleCount === 0 && rawProducts.length > 0) {
            noFound.classList.remove('hidden');
        } else {
            noFound.classList.add('hidden');
        }
    }

    // Modal Detail Produk (Read-Only)
    function openProductDetail(prodId) {
        const prod = productsMap[prodId];
        if (!prod) return;

        document.getElementById('detailCategory').textContent = prod.category ? prod.category.name : 'Umum';
        document.getElementById('detailName').textContent = prod.name;
        document.getElementById('detailCode').textContent = prod.code;
        document.getElementById('detailUnit').textContent = prod.unit || 'pcs';
        document.getElementById('detailStock').textContent = prod.stock > 0 ? `${prod.stock} tersedia` : 'Habis';
        document.getElementById('detailPrice').textContent = 'Rp ' + formatNumber(prod.price);
        document.getElementById('detailDescription').textContent = prod.description || 'Tidak ada deskripsi tambahan untuk produk ini.';

        const addBtn = document.getElementById('detailAddCartBtn');
        addBtn.disabled = prod.stock <= 0;
        addBtn.onclick = () => {
            addToCart(prod.id);
            closeProductDetail();
        };

        document.getElementById('productDetailModal').classList.remove('hidden');
    }

    function closeProductDetail() {
        document.getElementById('productDetailModal').classList.add('hidden');
    }

    // Tambah produk ke keranjang tersimpan (localStorage) & update badge troli di navbar
    function addToCart(prodId) {
        const prod = productsMap[prodId];
        if (!prod || prod.stock <= 0) return;

        const existing = cart.find(i => i.id === prodId);
        if (existing) {
            if (existing.qty >= prod.stock) {
                showToast('Batas Stok Tercapai', `Stok ${prod.name} tersisa ${prod.stock}.`, true);
                return;
            }
            existing.qty += 1;
        } else {
            cart.push({
                id: prod.id,
                name: prod.name,
                price: parseFloat(prod.price),
                stock: prod.stock,
                qty: 1
            });
        }

        if (window.saveUserCart) {
            window.saveUserCart(cart);
        }
        updateCatalogIndicators();

        const itemNow = cart.find(i => i.id === prodId);
        showToast('Masuk ke Keranjang', `${prod.name} (${itemNow.qty}x)`);
    }

    function updateCatalogIndicators() {
        document.querySelectorAll('.product-card').forEach(card => {
            const prodId = parseInt(card.dataset.id, 10);
            const item = cart.find(i => i.id === prodId);
            const badge = card.querySelector('.cart-indicator-badge');
            const qtySpan = card.querySelector('.cart-indicator-qty');

            if (item && item.qty > 0) {
                card.classList.add('border-[#5DD62C]', 'ring-1', 'ring-[#5DD62C]/40');
                badge.classList.remove('hidden');
                badge.classList.add('flex');
                qtySpan.textContent = item.qty;
            } else {
                card.classList.remove('border-[#5DD62C]', 'ring-1', 'ring-[#5DD62C]/40');
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', loadCart);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeProductDetail();
        }
    });
</script>
@endpush
