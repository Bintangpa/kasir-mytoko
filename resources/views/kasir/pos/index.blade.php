<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mesin Kasir (POS) - {{ config('app.name', 'MyToko') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome & Lucide Icons (Imported Icon Libraries) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        palette: {
                            dark: '#0F0F0F',
                            surface: '#202020',
                            neon: '#5DD62C',
                            forest: '#337418',
                            light: '#F8F8F8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8F8F8; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="bg-[#F8F8F8] text-[#0F0F0F] h-screen flex flex-col overflow-hidden select-none">

    <!-- TOP HEADER BAR (#0F0F0F & #202020) -->
    <header class="h-14 bg-[#0F0F0F] text-white flex items-center justify-between px-4 sm:px-6 shrink-0 shadow-md border-b border-[#2A2A2A]">
        <div class="flex items-center gap-3">
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('kasir.dashboard') }}" 
               class="px-2.5 py-1.5 bg-[#202020] hover:bg-[#2A2A2A] text-slate-300 hover:text-white rounded-lg text-xs font-bold flex items-center gap-1.5 transition-colors border border-[#2E2E2E]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Dashboard</span>
            </a>
            <div class="h-5 w-px bg-[#2A2A2A] mx-1"></div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#5DD62C] animate-pulse"></span>
                <span class="font-black text-sm tracking-tight text-white">KASIR <span class="text-[#5DD62C]">POINT OF SALE</span></span>
            </div>
        </div>

        <div class="flex items-center gap-3 text-xs">
            <a href="{{ route('receipt.settings') }}" target="_blank"
               class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-[#202020] hover:bg-[#2A2A2A] text-slate-300 hover:text-white transition-colors border border-[#2E2E2E]"
               title="Pengaturan Format & Ukuran Nota (Default 80mm / 58mm / 58x40)">
                <i class="fa-solid fa-sliders text-xs text-[#5DD62C]"></i>
                <span>Setting Nota (80mm)</span>
            </a>

            <div class="hidden sm:block text-slate-400">
                Kasir: <span class="text-white font-bold">{{ auth()->user()->name }}</span>
            </div>
            <div class="hidden md:block font-mono text-[#5DD62C] font-black" id="liveClock">
                00:00:00
            </div>
        </div>
    </header>

    <!-- POS MAIN WORKSPACE (2 PANELS) -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
        
        <!-- PANEL KIRI: KATALOG PRODUK -->
        <div class="flex-1 flex flex-col p-4 overflow-hidden border-r border-slate-200">
            <!-- Search & Category Filters -->
            <div class="space-y-3 mb-4 shrink-0">
                <!-- Bar Pencarian Cepat -->
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari nama produk atau ketik kode/barcode (Enter)..." autofocus
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:border-[#5DD62C] shadow-2xs text-[#0F0F0F]">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Category Pills Filter -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1" id="categoryTabs">
                    <button type="button" onclick="filterCategory('all')" 
                        class="category-btn active px-3.5 py-1.5 rounded-xl text-xs font-black transition-all whitespace-nowrap bg-[#5DD62C] text-[#0F0F0F] shadow-xs" data-category="all">
                        Semua Kategori ({{ $products->count() }})
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" onclick="filterCategory('{{ $cat->id }}')" 
                            class="category-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-slate-700 hover:bg-[#F8F8F8] border border-slate-200 shadow-2xs" data-category="{{ $cat->id }}">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto pr-1">
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3" id="productGrid">
                    @forelse($products as $prod)
                        <div class="product-card bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-[#5DD62C] hover:shadow-md transition-all cursor-pointer flex flex-col justify-between select-none relative group {{ $prod->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                             data-id="{{ $prod->id }}"
                             data-code="{{ strtolower($prod->code) }}"
                             data-name="{{ strtolower($prod->name) }}"
                             data-category="{{ $prod->category_id }}"
                             data-price="{{ (float)$prod->price }}"
                             data-stock="{{ $prod->stock }}"
                             onclick="addToCart({{ $prod->id }})">
                            
                            <!-- Badges -->
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-[10px] text-slate-400 font-bold">{{ $prod->code }}</span>
                                <div class="flex items-center gap-1.5">
                                    @if($prod->stock <= 0)
                                        <span class="stock-badge px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700">Habis</span>
                                    @elseif($prod->stock <= 5)
                                        <span class="stock-badge px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">Sisa {{ $prod->stock }}</span>
                                    @else
                                        <span class="stock-badge px-2 py-0.5 rounded-md text-[10px] font-semibold bg-[#F8F8F8] text-slate-600 border border-slate-200/60">Stok {{ $prod->stock }}</span>
                                    @endif
                                    <span class="cart-indicator-badge hidden px-2 py-0.5 rounded-full bg-[#5DD62C] text-[#0F0F0F] font-black text-[10px] shadow-xs flex items-center gap-1">
                                        <i class="fa-solid fa-check text-[9px]"></i> <span class="cart-indicator-qty">0</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Name & Category -->
                            <div>
                                <h4 class="text-xs font-bold text-[#0F0F0F] line-clamp-2 leading-tight">{{ $prod->name }}</h4>
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $prod->category->name ?? 'Umum' }}</span>
                            </div>

                            <!-- Price & Add Icon -->
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                <div class="font-black text-sm text-[#0F0F0F]">
                                    Rp {{ number_format($prod->price, 0, ',', '.') }}
                                </div>
                                <div class="w-7 h-7 rounded-lg bg-[#F8F8F8] text-slate-700 group-hover:bg-[#5DD62C] group-hover:text-[#0F0F0F] flex items-center justify-center transition-colors border border-slate-200/50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 text-slate-400 text-xs">
                            Belum ada produk aktif yang tersedia. Tambahkan produk di menu Admin.
                        </div>
                    @endforelse
                </div>

                <!-- Empty search state -->
                <div id="noProductsFound" class="hidden text-center py-16 text-slate-400 text-xs">
                    <i class="fa-solid fa-magnifying-glass text-slate-300 text-3xl mb-2 block"></i>
                    <span>Tidak ada produk yang cocok dengan pencarian Anda.</span>
                </div>
            </div>
        </div>

        <!-- PANEL KANAN: KERANJANG BELANJA & PEMBAYARAN -->
        <div class="w-full lg:w-96 xl:w-[420px] bg-white flex flex-col shadow-xl shrink-0 h-full overflow-hidden border-l border-slate-200">
            <!-- Cart Header -->
            <div class="p-3.5 border-b border-slate-200 flex items-center justify-between bg-[#F8F8F8]">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#337418]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <h3 class="text-sm font-black text-[#0F0F0F]">Keranjang Kasir</h3>
                    <span id="cartCountBadge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30">0 Jenis (0 Item)</span>
                </div>
                <button type="button" onclick="clearCart()" class="text-xs text-rose-500 hover:text-rose-700 font-bold transition-colors">
                    Kosongkan
                </button>
            </div>

            <!-- Customer Name & Notes -->
            <div class="px-3.5 py-2 bg-[#F8F8F8]/50 border-b border-slate-200 flex items-center gap-2">
                <input type="text" id="customerName" placeholder="Nama Pelanggan (Default: Pelanggan Umum)"
                    class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#5DD62C] font-medium text-[#0F0F0F]">
            </div>

            <!-- Cart Items List (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-3.5 space-y-2 min-h-[160px]" id="cartContainerWrapper">
                <!-- Cart empty state placeholder -->
                <div id="cartEmptyState" class="h-full flex flex-col items-center justify-center text-center py-12 text-slate-400">
                    <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <p class="text-xs font-bold text-slate-600">Keranjang Masih Kosong</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Klik produk di katalog untuk menambahkan.</p>
                </div>

                <!-- Container where cart items are rendered -->
                <div id="cartItemsContainer" class="space-y-2"></div>
            </div>

            <!-- Cart Footer (Billing & Checkout) -->
            <div class="p-3.5 border-t border-slate-200 bg-[#F8F8F8] space-y-2.5 shrink-0">
                <!-- Subtotal & Discount -->
                <div class="space-y-1 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="font-medium">Subtotal</span>
                        <span class="font-bold text-[#0F0F0F]" id="cartSubtotalText">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="font-medium">Diskon (Rp)</span>
                        <input type="number" id="discountInput" value="0" min="0" step="500" oninput="calculateTotal()"
                            class="w-24 px-2 py-0.5 text-right font-bold text-xs bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#5DD62C] text-[#0F0F0F]">
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-200 text-sm">
                        <span class="font-black text-[#0F0F0F]">Total Tagihan</span>
                        <span class="font-black text-base text-[#337418]" id="cartTotalText">Rp 0</span>
                    </div>
                </div>

                <!-- Payment Method Tabs -->
                <div class="pt-1 border-t border-slate-200 space-y-2">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Metode:</span>
                        <div class="grid grid-cols-3 gap-1 flex-1 max-w-[240px]" id="paymentMethodGroup">
                            <button type="button" onclick="setPaymentMethod('cash')" class="pay-btn active py-1 rounded-md text-[11px] font-bold border transition-all bg-[#5DD62C] text-[#0F0F0F] border-[#5DD62C]" data-method="cash">Tunai</button>
                            <button type="button" onclick="setPaymentMethod('qris')" class="pay-btn py-1 rounded-md text-[11px] font-semibold border transition-all bg-white text-slate-700 border-slate-200 hover:bg-[#F8F8F8]" data-method="qris">QRIS</button>
                            <button type="button" onclick="setPaymentMethod('transfer')" class="pay-btn py-1 rounded-md text-[11px] font-semibold border transition-all bg-white text-slate-700 border-slate-200 hover:bg-[#F8F8F8]" data-method="transfer">Transfer</button>
                        </div>
                    </div>

                    <!-- Quick Cash Presets (If Cash) -->
                    <div id="cashPresetsSection" class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-[11px] font-bold text-slate-400">Rp</span>
                                <input type="number" id="payAmountInput" min="0" step="1000" placeholder="Nominal Bayar" oninput="calculateChange()"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs font-black bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#5DD62C] text-[#0F0F0F]">
                            </div>
                            <button type="button" onclick="setExactAmount()" class="px-2.5 py-1.5 bg-[#5DD62C]/15 hover:bg-[#5DD62C]/25 text-[#337418] font-black text-[11px] rounded-lg border border-[#5DD62C]/30 whitespace-nowrap transition-colors">
                                Uang Pas
                            </button>
                        </div>

                        <!-- Fast Cash Buttons -->
                        <div class="grid grid-cols-4 gap-1 text-[10px] font-bold">
                            <button type="button" onclick="addPayAmount(10000)" class="py-1 bg-white hover:bg-[#F8F8F8] border border-slate-200 rounded-md text-slate-700 shadow-2xs">10k</button>
                            <button type="button" onclick="addPayAmount(20000)" class="py-1 bg-white hover:bg-[#F8F8F8] border border-slate-200 rounded-md text-slate-700 shadow-2xs">20k</button>
                            <button type="button" onclick="addPayAmount(50000)" class="py-1 bg-white hover:bg-[#F8F8F8] border border-slate-200 rounded-md text-slate-700 shadow-2xs">50k</button>
                            <button type="button" onclick="addPayAmount(100000)" class="py-1 bg-white hover:bg-[#F8F8F8] border border-slate-200 rounded-md text-slate-700 shadow-2xs">100k</button>
                        </div>

                        <!-- Kembalian Display -->
                        <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-slate-200/70 text-xs">
                            <span class="font-bold text-slate-600 text-[11px]">Kembalian:</span>
                            <span class="font-mono font-black text-xs text-[#0F0F0F]" id="changeAmountText">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button (#5DD62C Neon Green Button with #0F0F0F Text) -->
                <button type="button" id="btnCheckout" onclick="processCheckout()"
                    class="w-full py-2.5 bg-[#5DD62C] hover:bg-[#337418] active:bg-[#245311] disabled:opacity-50 disabled:cursor-not-allowed text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>BAYAR SEKARANG & CETAK STRUK</span>
                </button>
            </div>
        </div>

    </div>

    <!-- MODAL SUKSES TRANSAKSI -->
    <div id="successModal" class="fixed inset-0 z-50 bg-[#0F0F0F]/80 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl space-y-4 border border-slate-200">
            <div class="w-16 h-16 rounded-full bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/40 flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            
            <div>
                <h3 class="text-lg font-black text-[#0F0F0F]">Transaksi Berhasil!</h3>
                <p class="text-xs text-slate-500 font-mono font-bold mt-1" id="modalInvoiceCode">TRX-XXXXXXXX</p>
            </div>

            <div class="bg-[#F8F8F8] p-4 rounded-xl space-y-1.5 text-xs text-left border border-slate-200">
                <div class="flex justify-between text-slate-600">
                    <span class="font-medium">Total Belanja:</span>
                    <span class="font-bold text-[#0F0F0F]" id="modalTotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span class="font-medium">Uang Diterima:</span>
                    <span class="font-bold text-[#0F0F0F]" id="modalPay">Rp 0</span>
                </div>
                <div class="flex justify-between text-[#337418] font-black text-sm pt-1 border-t border-slate-200">
                    <span>Kembalian:</span>
                    <span id="modalChange">Rp 0</span>
                </div>
            </div>

            <div class="space-y-2 pt-2">
                <a id="modalPrintBtn" href="#" target="_blank"
                   class="w-full py-2.5 px-4 bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Struk Thermal (80mm)</span>
                </a>

                <button type="button" onclick="closeSuccessModal()"
                    class="w-full py-2.5 px-4 bg-[#F8F8F8] hover:bg-slate-200 text-[#0F0F0F] font-bold text-xs rounded-xl transition-all border border-slate-200">
                    Transaksi Baru (ESC)
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT STATE ENGINE UNTUK POS -->
    <script>
        // State products dari database
        const rawProducts = @json($products);
        const productsMap = {};
        rawProducts.forEach(p => { productsMap[p.id] = p; });

        // State Cart
        let cart = []; // [{ id, name, price, stock, qty }]
        let activePaymentMethod = 'cash';

        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID');
            const clockEl = document.getElementById('liveClock');
            if (clockEl) clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Filter Kategori
        function filterCategory(catId) {
            document.querySelectorAll('.category-btn').forEach(btn => {
                if (btn.dataset.category === String(catId)) {
                    btn.className = 'category-btn active px-3.5 py-1.5 rounded-xl text-xs font-black transition-all whitespace-nowrap bg-[#5DD62C] text-[#0F0F0F] shadow-xs';
                } else {
                    btn.className = 'category-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-slate-700 hover:bg-[#F8F8F8] border border-slate-200 shadow-2xs';
                }
            });

            applySearchAndFilter();
        }

        // Live Search Input Listener
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', applySearchAndFilter);
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const visibleCards = document.querySelectorAll('.product-card:not(.hidden)');
                if (visibleCards.length === 1) {
                    const prodId = parseInt(visibleCards[0].dataset.id);
                    addToCart(prodId);
                    searchInput.value = '';
                    applySearchAndFilter();
                }
            }
        });

        function applySearchAndFilter() {
            const query = searchInput.value.toLowerCase().trim();
            const activeCatBtn = document.querySelector('.category-btn.bg-\\[\\#5DD62C\\]') || document.querySelector('.category-btn.active');
            const catId = activeCatBtn ? activeCatBtn.dataset.category : 'all';

            let visibleCount = 0;
            document.querySelectorAll('.product-card').forEach(card => {
                const matchCat = (catId === 'all' || card.dataset.category === catId);
                const matchQuery = (card.dataset.name.includes(query) || card.dataset.code.includes(query));

                if (matchCat && matchQuery) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            const noFound = document.getElementById('noProductsFound');
            if (visibleCount === 0) {
                noFound.classList.remove('hidden');
            } else {
                noFound.classList.add('hidden');
            }
        }

        // Add to Cart
        function addToCart(prodId) {
            const prod = productsMap[prodId];
            if (!prod || prod.stock <= 0) return;

            const existingIndex = cart.findIndex(i => i.id === prodId);
            if (existingIndex > -1) {
                if (cart[existingIndex].qty < prod.stock) {
                    cart[existingIndex].qty++;
                } else {
                    alert(`Stok produk [${prod.name}] hanya tersisa ${prod.stock} unit.`);
                    return;
                }
            } else {
                cart.push({
                    id: prod.id,
                    name: prod.name,
                    price: parseFloat(prod.price),
                    stock: prod.stock,
                    unit: prod.unit,
                    qty: 1
                });
            }

            renderCart();

            // Auto-scroll ke bawah saat item baru ditambahkan agar langsung terlihat
            const scrollContainer = document.getElementById('cartContainerWrapper');
            if (scrollContainer) {
                setTimeout(() => { scrollContainer.scrollTop = scrollContainer.scrollHeight; }, 50);
            }
        }

        function updateCartQty(prodId, delta) {
            const index = cart.findIndex(i => i.id === prodId);
            if (index === -1) return;

            const newQty = cart[index].qty + delta;
            if (newQty <= 0) {
                cart.splice(index, 1);
            } else if (newQty > cart[index].stock) {
                alert(`Maksimal stok tercapai: ${cart[index].stock} ${cart[index].unit}`);
                return;
            } else {
                cart[index].qty = newQty;
            }

            renderCart();
        }

        // Atur kuantiti langsung lewat input angka
        function setCartQtyDirect(prodId, val) {
            const index = cart.findIndex(i => i.id === prodId);
            if (index === -1) return;

            let newQty = parseInt(val);
            if (isNaN(newQty) || newQty <= 0) {
                newQty = 1;
            } else if (newQty > cart[index].stock) {
                alert(`Maksimal stok [${cart[index].name}] hanya ${cart[index].stock} ${cart[index].unit}`);
                newQty = cart[index].stock;
            }

            cart[index].qty = newQty;
            renderCart();
        }

        function removeCartItem(prodId) {
            cart = cart.filter(i => i.id !== prodId);
            renderCart();
        }

        function clearCart() {
            if (cart.length === 0) return;
            if (confirm('Yakin ingin mengosongkan keranjang?')) {
                cart = [];
                renderCart();
            }
        }

        // Render Cart HTML
        function renderCart() {
            const emptyState = document.getElementById('cartEmptyState');
            const itemsContainer = document.getElementById('cartItemsContainer');
            const countBadge = document.getElementById('cartCountBadge');

            const totalItemCount = cart.reduce((sum, i) => sum + i.qty, 0);
            if (countBadge) {
                countBadge.textContent = `${cart.length} Jenis (${totalItemCount} Item)`;
            }

            if (cart.length === 0) {
                if (emptyState) emptyState.classList.remove('hidden');
                if (itemsContainer) itemsContainer.innerHTML = '';
            } else {
                if (emptyState) emptyState.classList.add('hidden');
                if (itemsContainer) {
                    itemsContainer.innerHTML = cart.map(item => `
                        <div class="p-3 bg-[#F8F8F8] hover:bg-slate-100/90 border border-slate-200/90 rounded-xl flex items-center justify-between gap-2 transition-colors">
                            <div class="overflow-hidden flex-1">
                                <h4 class="text-xs font-bold text-[#0F0F0F] truncate">${item.name}</h4>
                                <div class="text-[11px] text-slate-500 font-medium mt-0.5">
                                    Rp ${formatNumber(item.price)} x <span class="font-bold text-[#337418]">${item.qty}</span> = <span class="font-black text-[#0F0F0F]">Rp ${formatNumber(item.price * item.qty)}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="updateCartQty(${item.id}, -1)" 
                                    class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-700 font-bold hover:bg-slate-100 active:bg-slate-200 flex items-center justify-center text-xs shadow-2xs" title="Kurangi">-</button>
                                <input type="number" min="1" max="${item.stock}" value="${item.qty}"
                                    onchange="setCartQtyDirect(${item.id}, this.value)"
                                    onkeyup="if(event.key==='Enter') setCartQtyDirect(${item.id}, this.value)"
                                    class="w-10 h-7 text-center font-bold text-xs text-[#0F0F0F] bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#5DD62C]" title="Ketik angka jumlah langsung">
                                <button type="button" onclick="updateCartQty(${item.id}, 1)" 
                                    class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-700 font-bold hover:bg-slate-100 active:bg-slate-200 flex items-center justify-center text-xs shadow-2xs" title="Tambah">+</button>
                                <button type="button" onclick="removeCartItem(${item.id})" 
                                    class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 active:bg-rose-200 flex items-center justify-center ml-1 text-xs transition-colors" title="Hapus dari keranjang">
                                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                                </button>
                            </div>
                        </div>
                    `).join('');
                }
            }

            calculateTotal();
            updateCatalogCartIndicators();
        }

        // Indikator jumlah barang yang terpilih di katalog kartu produk kiri
        function updateCatalogCartIndicators() {
            document.querySelectorAll('.product-card').forEach(card => {
                const prodId = parseInt(card.dataset.id);
                const cartItem = cart.find(i => i.id === prodId);
                const badge = card.querySelector('.cart-indicator-badge');
                const qtySpan = card.querySelector('.cart-indicator-qty');

                if (cartItem && cartItem.qty > 0) {
                    card.classList.add('border-[#5DD62C]', 'bg-[#5DD62C]/10');
                    if (badge) {
                        badge.classList.remove('hidden');
                        badge.classList.add('flex');
                    }
                    if (qtySpan) {
                        qtySpan.textContent = cartItem.qty;
                    }
                } else {
                    card.classList.remove('border-[#5DD62C]', 'bg-[#5DD62C]/10');
                    if (badge) {
                        badge.classList.add('hidden');
                        badge.classList.remove('flex');
                    }
                }
            });
        }

        // Calculate Total
        function calculateTotal() {
            const subtotal = cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
            const discountInput = document.getElementById('discountInput');
            const discount = Math.max(0, parseFloat(discountInput.value) || 0);

            const total = Math.max(0, subtotal - discount);

            document.getElementById('cartSubtotalText').textContent = 'Rp ' + formatNumber(subtotal);
            document.getElementById('cartTotalText').textContent = 'Rp ' + formatNumber(total);

            // Jika metode non-tunai, samakan uang bayar dengan total
            if (activePaymentMethod !== 'cash') {
                document.getElementById('payAmountInput').value = total;
            }

            calculateChange();
        }

        // Calculate Kembalian
        function calculateChange() {
            const total = getCartTotal();
            const payInput = document.getElementById('payAmountInput');
            const payAmount = parseFloat(payInput.value) || 0;
            const changeText = document.getElementById('changeAmountText');

            if (payAmount >= total) {
                const change = payAmount - total;
                changeText.textContent = 'Rp ' + formatNumber(change);
                changeText.className = 'font-mono font-black text-sm text-[#337418]';
            } else {
                const deficit = total - payAmount;
                changeText.textContent = '-Rp ' + formatNumber(deficit) + ' (Kurang)';
                changeText.className = 'font-mono font-bold text-xs text-rose-600';
            }
        }

        function getCartTotal() {
            const subtotal = cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
            const discount = Math.max(0, parseFloat(document.getElementById('discountInput').value) || 0);
            return Math.max(0, subtotal - discount);
        }

        // Set Payment Method (cash, qris, transfer)
        function setPaymentMethod(method) {
            activePaymentMethod = method;
            document.querySelectorAll('.pay-btn').forEach(btn => {
                if (btn.dataset.method === method) {
                    btn.className = 'pay-btn active py-1 rounded-md text-[11px] font-black border transition-all bg-[#5DD62C] text-[#0F0F0F] border-[#5DD62C] shadow-2xs';
                } else {
                    btn.className = 'pay-btn py-1 rounded-md text-[11px] font-semibold border transition-all bg-white text-slate-700 border-slate-200 hover:bg-[#F8F8F8]';
                }
            });

            if (method !== 'cash') {
                document.getElementById('payAmountInput').value = getCartTotal();
                calculateChange();
            }
        }

        // Fast Cash Helpers
        function setExactAmount() {
            document.getElementById('payAmountInput').value = getCartTotal();
            calculateChange();
        }

        function addPayAmount(val) {
            const current = parseFloat(document.getElementById('payAmountInput').value) || 0;
            document.getElementById('payAmountInput').value = current + val;
            calculateChange();
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // Process Checkout via AJAX
        async function processCheckout() {
            if (cart.length === 0) {
                alert('Pilih minimal 1 produk ke dalam keranjang.');
                return;
            }

            const total = getCartTotal();
            const payAmount = parseFloat(document.getElementById('payAmountInput').value) || 0;

            if (activePaymentMethod === 'cash' && payAmount < total) {
                alert(`Uang pembayaran kurang Rp ${formatNumber(total - payAmount)} dari total belanja.`);
                document.getElementById('payAmountInput').focus();
                return;
            }

            const btn = document.getElementById('btnCheckout');
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Memproses Transaksi...`;

            const payload = {
                items: cart.map(i => ({ id: i.id, qty: i.qty })),
                customer_name: document.getElementById('customerName').value.trim() || 'Pelanggan Umum',
                pay_amount: payAmount >= total ? payAmount : total,
                payment_method: activePaymentMethod,
                discount: parseFloat(document.getElementById('discountInput').value) || 0,
            };

            try {
                const res = await fetch("{{ route('kasir.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan sistem saat memproses transaksi.');
                }

                if (data.success) {
                    // Update & Tampilkan modal sukses
                    document.getElementById('modalInvoiceCode').textContent = data.invoice_code;
                    document.getElementById('modalTotal').textContent = 'Rp ' + formatNumber(data.total_price);
                    document.getElementById('modalPay').textContent = 'Rp ' + formatNumber(data.pay_amount);
                    document.getElementById('modalChange').textContent = 'Rp ' + formatNumber(data.change_amount);
                    document.getElementById('modalPrintBtn').href = data.receipt_url;

                    document.getElementById('successModal').classList.remove('hidden');

                    // Reset cart
                    cart = [];
                    document.getElementById('customerName').value = '';
                    document.getElementById('discountInput').value = 0;
                    document.getElementById('payAmountInput').value = '';
                    setPaymentMethod('cash');
                    renderCart();
                } else {
                    alert('Gagal memproses transaksi: ' + (data.message || 'Error'));
                }
            } catch (err) {
                console.error(err);
                alert('Gagal memproses transaksi: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><span>BAYAR SEKARANG & CETAK STRUK</span>`;
            }
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            searchInput.focus();
        }

        // Global Keydown Shortcut
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuccessModal();
            }
            if (e.key === 'F9') {
                e.preventDefault();
                processCheckout();
            }
        });
    </script>
</body>
</html>
