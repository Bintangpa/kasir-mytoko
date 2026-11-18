<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'MyToko') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome & Lucide Icons (Imported Icon Libraries - No Emojis) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS CDN Configured with Exact Project Color Palette -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        // Palet Warna Resmi Sesuai Kartu Warna Pilihan User
                        palette: {
                            dark: '#0F0F0F',     // Dark / Obsidian base
                            surface: '#202020',  // Surface / Sidebar / Dark card
                            neon: '#5DD62C',     // Electric Lime / Accent primer
                            forest: '#337418',   // Deep Forest Green / Hover / Deep accent
                            light: '#F8F8F8',    // Clean Light / Off-white background
                        },
                        brand: {
                            50: '#f2fdf0',
                            100: '#e3fce0',
                            200: '#c5f9be',
                            300: '#9cf290',
                            400: '#5DD62C',      // Exact Palette Lime
                            500: '#4dc223',
                            600: '#337418',      // Exact Palette Forest
                            700: '#275b12',
                            800: '#204911',
                            900: '#1b3d10',
                            950: '#0F0F0F',      // Exact Palette Dark
                        }
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --color-dark: #0F0F0F;
            --color-surface: #202020;
            --color-neon: #5DD62C;
            --color-forest: #337418;
            --color-light: #F8F8F8;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8F8F8; }
        [x-cloak] { display: none !important; }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #F8F8F8; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    @stack('styles')
</head>
<body class="bg-[#F8F8F8] text-[#0F0F0F] min-h-screen flex flex-col antialiased">

    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 z-30 bg-[#0F0F0F]/70 backdrop-blur-[1px] hidden lg:hidden transition-opacity"></div>

        <!-- SIDEBAR (Menggunakan #202020 Surface & #0F0F0F Brand Bar) -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-[#202020] text-slate-300 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:inset-auto border-r border-[#2A2A2A]">
            <!-- Brand Logo -->
            <div class="flex items-center justify-between h-16 px-6 bg-[#0F0F0F] border-b border-[#2A2A2A]">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isKasir() ? route('kasir.dashboard') : route('user.products.index')) }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#337418] to-[#5DD62C] flex items-center justify-center text-[#0F0F0F] font-black shadow-md shadow-[#5DD62C]/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-base font-black text-white tracking-tight">MYTOKO<span class="text-[#5DD62C]">GUE</span></span>
                        <span class="block text-[10px] text-slate-400 font-semibold leading-none tracking-wider uppercase">POS & Retail System</span>
                    </div>
                </a>
                <button id="closeSidebar" class="lg:hidden text-slate-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Role Badge -->
            <div class="px-6 py-4 border-b border-[#2A2A2A] bg-[#0F0F0F]/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#202020] text-[#5DD62C] border border-[#337418]/60 flex items-center justify-center font-black text-sm shadow-inner">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="inline-block w-2 h-2 rounded-full bg-[#5DD62C] animate-pulse"></span>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#5DD62C]">
                                Role: {{ auth()->user()->role }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                @if(auth()->user()->isAdmin())
                    <!-- ADMIN MENU -->
                    <div class="px-3 pb-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Navigasi Admin</div>
                    
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm font-black' : 'text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard Utama</span>
                    </a>

                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.products.*') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm font-black' : 'text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Manajemen Produk</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm font-black' : 'text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span>Kategori Produk</span>
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm font-black' : 'text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Laporan Penjualan</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm font-black' : 'text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Kelola Kasir & User</span>
                    </a>

                    <div class="pt-3 pb-1 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Printer & Nota</div>
                    <a href="{{ route('receipt.settings') }}" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C] transition-colors">
                        <i class="fa-solid fa-sliders text-xs text-[#5DD62C]"></i>
                        <span>Setting Format Nota (80mm)</span>
                    </a>

                    <div class="pt-3 pb-1 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Akses Mesin Kasir</div>
                    <a href="{{ route('kasir.pos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-[#5DD62C] hover:bg-[#5DD62C] hover:text-[#0F0F0F] border border-[#5DD62C]/30 bg-[#5DD62C]/10 transition-all shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Buka Kasir (POS)</span>
                    </a>
                @elseif(auth()->user()->isKasir())
                    <!-- KASIR MENU -->
                    <div class="px-3 pb-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menu Kasir</div>
                    
                    <a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('kasir.dashboard') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm font-black' : 'text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard Shift Kasir</span>
                    </a>

                    <a href="{{ route('kasir.pos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('kasir.pos') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm' : 'bg-[#5DD62C]/10 text-[#5DD62C] hover:bg-[#5DD62C] hover:text-[#0F0F0F] border border-[#5DD62C]/30' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Transaksi Kasir (POS)</span>
                    </a>

                    <a href="{{ route('kasir.transactions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('kasir.transactions.*') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm font-black' : 'text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Riwayat Penjualan Saya</span>
                    </a>

                    <div class="pt-3 pb-1 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Printer & Nota</div>
                    <a href="{{ route('receipt.settings') }}" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C] transition-colors">
                        <i class="fa-solid fa-sliders text-xs text-[#5DD62C]"></i>
                        <span>Setting Format Nota (80mm)</span>
                    </a>
                @else
                    <!-- USER / PENGGUNA MENU -->
                    <div class="px-3 pb-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menu Pengguna</div>

                    <a href="{{ route('user.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('user.products.*') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm font-black' : 'text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Katalog Produk</span>
                    </a>

                    <a href="{{ route('user.cart') }}" class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('user.cart') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm font-black' : 'text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C]' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <i class="fa-solid fa-cart-shopping w-5 text-center text-base shrink-0"></i>
                            <span class="truncate">Keranjang & Bayar</span>
                        </div>
                        <span id="sidebarCartBadge" class="hidden px-2 py-0.5 rounded-full text-[10px] font-black {{ request()->routeIs('user.cart') ? 'bg-[#0F0F0F] text-[#5DD62C]' : 'bg-[#5DD62C] text-[#0F0F0F]' }}">0</span>
                    </a>

                    <a href="{{ route('user.transactions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('user.transactions.*') ? 'bg-[#5DD62C] text-[#0F0F0F] shadow-sm font-black' : 'text-slate-300 hover:bg-[#2A2A2A] hover:text-[#5DD62C]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Riwayat Pembelian Saya</span>
                    </a>
                @endif
            </nav>

            <!-- Bottom User & Logout -->
            <div class="p-4 border-t border-[#2A2A2A] bg-[#0F0F0F]">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-bold text-rose-300 hover:text-white bg-rose-500/10 hover:bg-rose-600/30 border border-rose-500/20 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar / Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#F8F8F8]">
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-20 shadow-2xs">
                <div class="flex items-center gap-3">
                    <button id="openSidebar" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-[#F8F8F8]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-base sm:text-lg font-black text-[#0F0F0F] tracking-tight truncate">
                        @yield('header-title', 'Dashboard')
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Shortcut POS button if admin -->
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('kasir.pos') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#5DD62C]/15 text-[#337418] hover:bg-[#5DD62C] hover:text-[#0F0F0F] border border-[#5DD62C]/30 transition-all">
                            <svg class="w-4 h-4 text-[#337418]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Buka POS</span>
                        </a>
                    @endif

                    <!-- Tombol Icon Troli Keranjang & Bayar (Khusus Role User / Pengguna) -->
                    @if(auth()->user()->isUser())
                        <a href="{{ route('user.cart') }}"
                           class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl transition-all border {{ request()->routeIs('user.cart') ? 'bg-[#5DD62C] text-[#0F0F0F] border-[#5DD62C] shadow-sm' : 'bg-[#F8F8F8] hover:bg-[#5DD62C]/20 text-[#0F0F0F] hover:text-[#337418] border-slate-200 hover:border-[#5DD62C]/50' }}"
                           title="Keranjang & Pembayaran"
                           aria-label="Keranjang & Pembayaran">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                            <span id="navbarCartBadge"
                                  class="hidden absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 rounded-full bg-[#0F0F0F] text-[#5DD62C] border-2 border-white text-[10px] font-black items-center justify-center shadow-xs">
                                0
                            </span>
                        </a>
                    @endif

                    <!-- User Pill -->
                    <div class="flex items-center gap-2 pl-2">
                        <div class="w-8 h-8 rounded-full bg-[#202020] text-[#5DD62C] border border-[#337418]/50 flex items-center justify-center font-black text-xs shadow-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden md:inline-block text-xs font-bold text-[#0F0F0F]">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </header>

            <!-- FLASH ALERTS -->
            <div class="px-4 sm:px-6 pt-4">
                @if(session('success'))
                    <div class="flex items-center justify-between p-3.5 mb-2 text-xs sm:text-sm text-[#337418] bg-[#5DD62C]/15 border border-[#5DD62C]/40 rounded-xl shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-check text-[#337418] text-base shrink-0"></i>
                            <span class="font-bold">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-[#337418] hover:text-[#0F0F0F] p-1 ml-2 transition-colors" title="Tutup">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-center justify-between p-3.5 mb-2 text-xs sm:text-sm text-rose-800 bg-rose-50 border border-rose-200 rounded-xl shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-exclamation text-rose-500 text-base shrink-0"></i>
                            <span class="font-bold">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 p-1 ml-2 transition-colors" title="Tutup">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="flex items-center justify-between p-3.5 mb-2 text-xs sm:text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-xl shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-triangle-exclamation text-amber-500 text-base shrink-0"></i>
                            <span class="font-bold">{{ session('warning') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-800 p-1 ml-2 transition-colors" title="Tutup">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Page Body -->
            <main class="flex-1 overflow-y-auto px-3 sm:px-6 pb-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Script Drawer Sidebar Mobile -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const openBtn = document.getElementById('openSidebar');
        const closeBtn = document.getElementById('closeSidebar');

        function openMobileSidebar() {
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (sidebarOverlay) sidebarOverlay.classList.remove('hidden');
        }

        function closeMobileSidebar() {
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
        }

        if (openBtn) openBtn.addEventListener('click', openMobileSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeMobileSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeMobileSidebar);

        @if(auth()->user()->isUser())
        window.USER_CART_STORAGE_KEY = 'mytokogue_user_cart_{{ auth()->id() }}';

        window.getUserCart = function() {
            try {
                const raw = localStorage.getItem(window.USER_CART_STORAGE_KEY);
                const parsed = raw ? JSON.parse(raw) : [];
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                return [];
            }
        };

        window.saveUserCart = function(items) {
            try {
                localStorage.setItem(window.USER_CART_STORAGE_KEY, JSON.stringify(items || []));
            } catch (e) {}
            window.syncUserCartBadges(items);
        };

        window.syncUserCartBadges = function(itemsArg) {
            const items = Array.isArray(itemsArg) ? itemsArg : window.getUserCart();
            const totalQty = items.reduce((sum, item) => sum + (parseInt(item.qty, 10) || 0), 0);

            const navBadge = document.getElementById('navbarCartBadge');
            if (navBadge) {
                navBadge.textContent = totalQty > 99 ? '99+' : totalQty;
                if (totalQty > 0) {
                    navBadge.classList.remove('hidden');
                    navBadge.classList.add('flex');
                } else {
                    navBadge.classList.add('hidden');
                    navBadge.classList.remove('flex');
                }
            }

            const sideBadge = document.getElementById('sidebarCartBadge');
            if (sideBadge) {
                sideBadge.textContent = totalQty > 99 ? '99+' : totalQty;
                if (totalQty > 0) {
                    sideBadge.classList.remove('hidden');
                } else {
                    sideBadge.classList.add('hidden');
                }
            }
        };

        document.addEventListener('DOMContentLoaded', () => window.syncUserCartBadges());
        window.addEventListener('storage', (e) => {
            if (e.key === window.USER_CART_STORAGE_KEY) {
                window.syncUserCartBadges();
            }
        });
        @endif
    </script>

    @stack('scripts')
</body>
</html>
