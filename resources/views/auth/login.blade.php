<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ config('app.name', 'MyTokoGue') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome & Lucide Icons (Imported Icon Libraries - No Raw Emojis) -->
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
                            dark: '#0F0F0F',     // Obsidian Base
                            surface: '#202020',  // Charcoal Surface
                            neon: '#5DD62C',     // Electric Lime
                            forest: '#337418',   // Forest Green
                            light: '#F8F8F8',    // Clean Off-White
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0F0F0F;
        }
    </style>
</head>
<body class="min-h-screen bg-[#0F0F0F] text-[#0F0F0F] flex items-center justify-center p-3 sm:p-6 lg:p-8 relative overflow-x-hidden select-none">

    <!-- Ambient Glow Latar Belakang Luar -->
    <div class="fixed -top-40 -left-40 w-96 h-96 bg-[#337418]/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="fixed -bottom-40 -right-40 w-96 h-96 bg-[#5DD62C]/15 rounded-full blur-[120px] pointer-events-none"></div>

    <!-- POP-UP KOTAK BESAR (Floating Dialog Box Proposional Sesuai Konsep Foto) -->
    <div class="w-full max-w-4xl lg:max-w-5xl bg-white rounded-3xl sm:rounded-[36px] shadow-2xl p-3 sm:p-5 lg:p-6 border border-slate-100/80 relative z-10 transition-all duration-300">
        <div class="flex flex-col md:flex-row gap-4 sm:gap-6 lg:gap-8 items-stretch">
            
            <!-- SISI KIRI: SHOWCASE BANNER KARTU HITAM (Side Card Modern dengan Efek Glow Berkas Cahaya) -->
            <div class="w-full md:w-5/12 lg:w-1/2 bg-[#0F0F0F] rounded-2xl sm:rounded-[28px] p-6 sm:p-8 lg:p-10 flex flex-col justify-between relative overflow-hidden min-h-[360px] md:min-h-[560px] border border-[#202020] shadow-inner">
                
                <!-- Efek Berkas Cahaya Vertikal Gradien Hijau (Sesuai Gaya Desain Foto Referensi) -->
                <div class="absolute inset-x-0 bottom-0 h-72 pointer-events-none flex items-end justify-center gap-2 sm:gap-3 px-6 overflow-hidden">
                    <div class="w-6 sm:w-8 h-40 bg-gradient-to-t from-[#5DD62C] via-[#337418] to-transparent rounded-t-full filter blur-md opacity-40"></div>
                    <div class="w-10 sm:w-12 h-64 bg-gradient-to-t from-[#86EFAC] via-[#5DD62C] to-transparent rounded-t-full filter blur-lg opacity-60"></div>
                    <div class="w-8 sm:w-10 h-52 bg-gradient-to-t from-[#5DD62C] via-[#337418] to-transparent rounded-t-full filter blur-md opacity-55"></div>
                    <div class="w-5 sm:w-7 h-36 bg-gradient-to-t from-[#337418] to-transparent rounded-t-full filter blur-sm opacity-40"></div>
                </div>
                <!-- Ambient Glow Halus di Bagian Bawah Banner -->
                <div class="absolute -bottom-16 inset-x-0 h-44 bg-gradient-to-t from-[#5DD62C]/30 via-[#337418]/20 to-transparent filter blur-2xl pointer-events-none"></div>

                <!-- Bagian Atas Banner: Brand & Tagline Kuat -->
                <div class="relative z-10">
                    <!-- Brand Pill Badge -->
                    <div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-[#202020]/90 border border-[#2E2E2E] text-xs font-bold text-white mb-6 backdrop-blur-xs">
                        <div class="w-2 h-2 rounded-full bg-[#5DD62C] animate-pulse"></div>
                        <span class="tracking-wide">MYTOKO<span class="text-[#5DD62C]">GUE</span> POS</span>
                    </div>

                    <!-- Tagline Utama (Terinspirasi Konsep: "Convert your ideas into successful business") -->
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-[1.15] tracking-tight">
                        Kelola toko & kasir<br>
                        lebih <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#5DD62C] via-[#86EFAC] to-[#5DD62C]">cepat, akurat,</span><br>
                        dan modern.
                    </h2>

                    <p class="text-xs sm:text-sm text-slate-400 mt-4 leading-relaxed max-w-sm font-medium">
                        Solusi Point of Sale terpadu dengan cetak struk thermal 80mm & 58mm, kontrol stok real-time, dan laporan keuangan instan.
                    </p>
                </div>

                <!-- Bagian Bawah Banner: Feature Pills Modern -->
                <div class="relative z-10 pt-6">
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#202020]/80 border border-[#2E2E2E] text-[11px] font-bold text-slate-300 backdrop-blur-xs">
                            <i class="fa-solid fa-bolt text-[#5DD62C] text-xs"></i> POS Responsif
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#202020]/80 border border-[#2E2E2E] text-[11px] font-bold text-slate-300 backdrop-blur-xs">
                            <i class="fa-solid fa-receipt text-[#5DD62C] text-xs"></i> Struk 80mm
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#202020]/80 border border-[#2E2E2E] text-[11px] font-bold text-slate-300 backdrop-blur-xs">
                            <i class="fa-solid fa-chart-line text-[#5DD62C] text-xs"></i> Laporan Realtime
                        </span>
                    </div>
                </div>

            </div>

            <!-- SISI KANAN: SIDE FORM LOGIN (Bersih, Rapi, & Fungsional) -->
            <div class="w-full md:w-7/12 lg:w-1/2 p-4 sm:p-6 lg:p-8 flex flex-col justify-center">
                
                <!-- Icon Sunburst / Starburst (Sesuai Karakter Logo di Foto Referensi) -->
                <div class="mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-[#5DD62C]/15 border border-[#5DD62C]/30 flex items-center justify-center text-[#337418] shadow-xs">
                        <!-- Sunburst Sparkle SVG -->
                        <svg class="w-6 h-6 text-[#337418]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3.5"/>
                            <path d="M12 2v2.5"/>
                            <path d="M12 19.5V22"/>
                            <path d="m4.93 4.93 1.77 1.77"/>
                            <path d="m17.3 17.3 1.77 1.77"/>
                            <path d="M2 12h2.5"/>
                            <path d="M19.5 12H22"/>
                            <path d="m6.7 17.3-1.77 1.77"/>
                            <path d="m19.07 4.93-1.77 1.77"/>
                        </svg>
                    </div>
                </div>

                <!-- Judul & Subjudul Form -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-[#0F0F0F] tracking-tight">Masuk ke Akun</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Selamat datang kembali — Silakan masuk untuk mengelola toko.</p>
                </div>

                <!-- Garis Pemisah Halus -->
                <div class="h-px bg-slate-100 my-5"></div>

                <!-- Notifikasi Pesan Kesalahan / Sukses -->
                @if(session('error'))
                    <div class="flex items-center gap-2.5 p-3.5 mb-4 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 rounded-xl shadow-xs">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="flex items-center gap-2.5 p-3.5 mb-4 text-xs font-bold text-[#337418] bg-[#5DD62C]/15 border border-[#5DD62C]/30 rounded-xl shadow-xs">
                        <i class="fa-solid fa-circle-check text-[#337418] text-sm shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Formulir Login -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Field Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full px-4 py-3 bg-[#F8F8F8] hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-medium text-[#0F0F0F] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:border-[#5DD62C] transition-all shadow-2xs"
                                placeholder="nama@mytoko.com">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-500 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation text-[11px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Field Password dengan Fitur Toggle Show/Hide -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="w-full pl-4 pr-11 py-3 bg-[#F8F8F8] hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-medium text-[#0F0F0F] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:border-[#5DD62C] transition-all shadow-2xs"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                                title="Lihat / Sembunyikan Sandi">
                                <i id="passwordToggleIcon" class="fa-regular fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-500 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation text-[11px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Ingat Saya Checkbox -->
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <label class="flex items-center text-slate-600 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-[#337418] rounded border-slate-300 focus:ring-[#5DD62C]">
                            <span class="ml-2 font-semibold">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Tombol Masuk Utama (Warna #5DD62C Neon Green dengan teks #0F0F0F Sesuai Palet) -->
                    <button type="submit"
                        class="w-full py-3.5 px-5 bg-[#5DD62C] hover:bg-[#337418] active:bg-[#204911] text-[#0F0F0F] hover:text-white font-black rounded-xl text-sm shadow-md shadow-[#5DD62C]/25 transition-all flex items-center justify-center gap-2 mt-2">
                        <span>Masuk ke Akun</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <!-- AKUN DEMO CEPAT (1-Klik untuk mengisi form, mempertahankan fungsionalitas) -->
                <div class="mt-6 pt-5 border-t border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Pilih Cepat Akun Demo (1-Klik):</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <button type="button" onclick="fillCreds('admin@mytoko.com', 'password')" 
                            class="p-2.5 rounded-xl border border-slate-200 hover:border-[#5DD62C] bg-[#F8F8F8] hover:bg-[#5DD62C]/10 text-left transition-all group">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#0F0F0F]"></span>
                                <span class="text-xs font-black text-[#0F0F0F] group-hover:text-[#337418]">Admin Toko</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono block mt-0.5 truncate">admin@mytoko.com</span>
                        </button>

                        <button type="button" onclick="fillCreds('kasir@mytoko.com', 'password')" 
                            class="p-2.5 rounded-xl border border-slate-200 hover:border-[#5DD62C] bg-[#F8F8F8] hover:bg-[#5DD62C]/10 text-left transition-all group">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#337418]"></span>
                                <span class="text-xs font-black text-[#0F0F0F] group-hover:text-[#337418]">Kasir Utama</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono block mt-0.5 truncate">kasir@mytoko.com</span>
                        </button>

                        <button type="button" onclick="fillCreds('user@mytoko.com', 'password')" 
                            class="p-2.5 rounded-xl border border-slate-200 hover:border-[#5DD62C] bg-[#F8F8F8] hover:bg-[#5DD62C]/10 text-left transition-all group">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#5DD62C]"></span>
                                <span class="text-xs font-black text-[#0F0F0F] group-hover:text-[#337418]">Pengguna</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono block mt-0.5 truncate">user@mytoko.com</span>
                        </button>
                    </div>
                </div>

                <!-- Footer Info -->
                <p class="text-center text-[11px] text-slate-400 mt-5 font-medium">
                    &copy; {{ date('Y') }} MyTokoGue POS • Point of Sale & Retail Management
                </p>

            </div>

        </div>
    </div>

    <!-- Script Fungsionalitas Login -->
    <script>
        // Isi Otomatis Kredensial Demo
        function fillCreds(email, password) {
            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');
            emailInput.value = email;
            passInput.value = password;
            emailInput.focus();
        }

        // Toggle Password Show/Hide
        function togglePasswordVisibility() {
            const passInput = document.getElementById('password');
            const icon = document.getElementById('passwordToggleIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.className = 'fa-regular fa-eye-slash text-sm text-[#337418]';
            } else {
                passInput.type = 'password';
                icon.className = 'fa-regular fa-eye text-sm text-slate-400';
            }
        }
    </script>
</body>
</html>
