<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Thermal 80mm - {{ $transaction->invoice_code }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome & Tailwind CSS (No Raw Emojis) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Style Dasar & Pengaturan Thermal 80mm Full Width -->
    <style id="dynamicReceiptStyles">
        :root {
            --paper-width: 80mm;
            --print-slip-width: 76mm;
            --paper-height: auto;
            --receipt-font-size: 13px;
            --receipt-line-height: 1.45;
            --receipt-padding: 3mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        /* Tampilan di Layar Monitor (Screen Preview Lebar 80mm & Estetik) */
        @media screen {
            .receipt-slip {
                font-family: 'JetBrains Mono', monospace;
                width: 440px; /* Lebar nyaman & proporsional untuk simulasi 80mm di layar */
                max-width: 95vw;
                min-height: var(--paper-height, auto);
                font-size: var(--receipt-font-size, 13px);
                line-height: var(--receipt-line-height, 1.45);
                padding: 8mm 6mm;
                box-sizing: border-box;
            }
        }

        /* Divider Garis Khas Printer Thermal */
        .thermal-divider {
            border-bottom: 2px dashed #94a3b8;
            margin: 0.85rem 0;
        }

        /* Standar Cetak Nyata Khusus Printer Thermal 80mm (Edge-to-Edge Full Width) */
        @media print {
            *, *:before, *:after {
                box-sizing: border-box !important;
            }

            .no-print {
                display: none !important;
            }

            html, body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                display: block !important; /* Mencegah Tailwind flexbox menciutkan nota */
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Memenuhi lebar maksimal printhead kertas thermal 80mm (76mm) */
            .receipt-slip {
                font-family: 'JetBrains Mono', monospace !important;
                display: block !important;
                width: var(--print-slip-width, 76mm) !important;
                min-width: var(--print-slip-width, 76mm) !important;
                max-width: var(--print-slip-width, 76mm) !important;
                margin: 0 auto !important;
                padding: 1.5mm 2.5mm 6mm 2.5mm !important; /* Padding pas agar teks tidak terpotong */
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                font-size: var(--receipt-font-size, 13px) !important;
                line-height: var(--receipt-line-height, 1.45) !important;
                color: #000000 !important;
            }

            .thermal-divider {
                border-bottom: 1.5px dashed #000000 !important;
                margin: 0.65rem 0 !important;
            }
        }
    </style>

    <!-- Tag Style Dinamis untuk @page (Wajib Literal tanpa var() agar Driver Printer & Chrome Mengenali Ukuran 80mm) -->
    <style id="dynamicPageStyle">
        @page {
            size: 80mm 297mm;
            margin: 0mm !important;
        }
        @page :first {
            size: 80mm 297mm;
            margin: 0mm !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center py-6 px-3">

    <!-- TOOLBAR ATAS: PRESET CEPAT & AKSI (TIDAK TERCETAK) -->
    <header class="no-print sticky top-3 z-40 w-full max-w-xl bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl shadow-sm p-3 mb-3 flex flex-wrap items-center justify-between gap-2.5">
        <!-- Preset Kertas Cepat -->
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1">Preset:</span>
            
            <button type="button" onclick="applyPreset('80roll')" id="btnPreset80roll"
                class="preset-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all border border-[#5DD62C] bg-[#5DD62C]/15 text-[#337418] shadow-xs flex items-center gap-1.5">
                <i class="fa-solid fa-receipt text-[10px]"></i>
                <span>80 mm Roll (Default)</span>
            </button>

            <button type="button" onclick="applyPreset('58roll')" id="btnPreset58roll"
                class="preset-btn px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 flex items-center gap-1.5">
                <i class="fa-solid fa-scroll text-[10px]"></i>
                <span>58 mm Roll</span>
            </button>

            <button type="button" onclick="applyPreset('58x40')" id="btnPreset58x40"
                class="preset-btn px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 flex items-center gap-1.5">
                <i class="fa-solid fa-tag text-[10px]"></i>
                <span>58 x 40 mm</span>
            </button>
        </div>

        <!-- Tombol Panduan, Pengaturan, Cetak & Tutup -->
        <div class="flex items-center gap-2">
            <button type="button" onclick="toggleSettingsModal()" 
                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5" title="Kustomisasi Format & Ukuran Nota">
                <i class="fa-solid fa-sliders text-xs"></i>
                <span class="hidden sm:inline">Pengaturan</span>
            </button>

            <button type="button" onclick="handlePrint()" 
                class="px-4 py-1.5 bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white text-xs font-black rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-print text-xs"></i>
                <span>Cetak Nota (80mm)</span>
            </button>

            <button type="button" onclick="window.close()" 
                class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors" title="Tutup Halaman">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </header>

    <!-- PETUNJUK CETAK FULL LEBAR 80MM (NO PRINT) -->
    <div class="no-print w-full max-w-xl bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300/90 rounded-2xl p-4 mb-4 text-xs shadow-sm">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-bold shadow-xs">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
            </div>
            <div class="space-y-1.5 flex-1">
                <div class="font-extrabold text-amber-950 text-xs sm:text-sm flex items-center justify-between">
                    <span>AGAR HASIL CETAK FULL LEBAR 80MM (TIDAK KECIL DI TENGAH):</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-black uppercase">Penting</span>
                </div>
                <p class="text-amber-900 leading-relaxed text-[11px]">
                    Jika dicetak hasilnya masih kecil (seperti 58mm di tengah kertas 80mm), pastikan 3 pengaturan ini pada jendela cetak browser (Chrome):
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 font-semibold text-slate-800">
                    <div class="bg-white/90 p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <span class="text-emerald-700 block text-[10px] uppercase font-black">1. Paper Size</span>
                        <span class="text-[11px]">Pilih <strong>80 x 297 mm</strong> atau <strong>Roll 80mm</strong></span>
                    </div>
                    <div class="bg-white/90 p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <span class="text-emerald-700 block text-[10px] uppercase font-black">2. Margins</span>
                        <span class="text-[11px]">Pilih <strong>None / Nol</strong></span>
                    </div>
                    <div class="bg-white/90 p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <span class="text-emerald-700 block text-[10px] uppercase font-black">3. Scale</span>
                        <span class="text-[11px]">Pilih <strong>Default / 100%</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTAINER DOKUMEN STRUK / NOTA 80MM MODERN (FULL LEBAR 80MM & PANJANG KE BAWAH) -->
    <div class="receipt-slip bg-white text-slate-950 border border-slate-200/90 shadow-2xl rounded-t-xl transition-all duration-200 mx-auto select-none relative mb-12" id="receiptPaper">
        
        <!-- 1. HEADER TOKO (Modern Branding Lebar 80mm) -->
        <div id="sectionStoreHeader" class="text-center pb-2 space-y-1">
            <div class="w-14 h-14 rounded-2xl bg-slate-950 text-white mx-auto flex items-center justify-center shadow-sm mb-2">
                <i class="fa-solid fa-store text-2xl"></i>
            </div>
            <h1 class="font-black tracking-wider uppercase text-xl sm:text-2xl text-slate-950 font-sans" id="valStoreName">MYTOKOGUE</h1>
            <p class="text-[11px] font-bold text-slate-600 uppercase tracking-widest font-sans">Point of Sale & Retail Store</p>
            <p class="text-[12px] text-slate-700 leading-tight pt-0.5" id="valStoreAddress">Jl. Toko Utama No. 88, Indonesia</p>
            <p class="text-[12px] text-slate-900 font-bold" id="valStorePhone">Telp/WA: 0812-3456-7890</p>
        </div>

        <div class="thermal-divider"></div>

        <!-- 2. META DATA TRANSAKSI (Lebar & Terstruktur Rapi) -->
        <div class="space-y-1.5 text-[12px]">
            <div class="flex justify-between items-center" id="rowInvoice">
                <span class="text-slate-500 font-semibold">No. Struk:</span>
                <span class="font-black font-mono text-slate-950 text-[13px]">{{ $transaction->invoice_code }}</span>
            </div>
            <div class="flex justify-between items-center" id="rowDateTime">
                <span class="text-slate-500 font-semibold">Tanggal:</span>
                <span class="font-bold text-slate-900 font-mono">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="flex justify-between items-center" id="rowCashier">
                <span class="text-slate-500 font-semibold">Kasir:</span>
                <span class="font-bold text-slate-900">{{ $transaction->user->name ?? 'Kasir Utama' }}</span>
            </div>
            <div class="flex justify-between items-center" id="rowCustomer">
                <span class="text-slate-500 font-semibold">Pelanggan:</span>
                <span class="font-bold text-slate-900">{{ $transaction->customer_name }}</span>
            </div>
            <div class="flex justify-between items-center pt-0.5">
                <span class="text-slate-500 font-semibold">Status:</span>
                <span class="px-2.5 py-0.5 rounded text-[11px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-wider border border-emerald-300">Lunas (Paid)</span>
            </div>
        </div>

        <div class="thermal-divider"></div>

        <!-- 3. TABEL RINCIAN PRODUK (Format 80mm Panjang ke Bawah) -->
        <div>
            <!-- Header Kolom Lebar Penuh -->
            <div class="flex justify-between text-[11px] font-black text-slate-600 uppercase tracking-wider pb-1.5 border-b-2 border-slate-300">
                <span>RINCIAN PRODUK</span>
                <span>SUBTOTAL</span>
            </div>

            <!-- List Produk Belanjaan -->
            <div class="pt-2.5 space-y-3.5">
                @foreach($transaction->items as $item)
                    <div class="border-b border-slate-100 pb-2">
                        <!-- Nama Produk di baris tersendiri agar panjang muat tanpa terpotong -->
                        <div class="font-bold text-[13px] text-slate-950 leading-snug">{{ $item->product_name }}</div>
                        <div class="flex justify-between items-center text-[12px] text-slate-700 mt-1">
                            <span class="font-mono text-slate-800">{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                            <span class="font-black font-mono text-slate-950 text-[13px]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="thermal-divider"></div>

        <!-- 4. TOTAL & PERHITUNGAN BAYAR (Highlight Box Modern 80mm) -->
        <div class="space-y-2 text-[12px]">
            <div class="flex justify-between text-slate-700 font-semibold" id="rowSubtotal">
                <span>Total Item ({{ $transaction->items->sum('quantity') }} Pcs):</span>
                <span class="font-mono font-bold text-slate-950 text-[13px]">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            
            @if($transaction->discount > 0)
                <div class="flex justify-between text-rose-600 font-bold" id="rowDiscount">
                    <span>Diskon Potongan:</span>
                    <span class="font-mono text-[13px]">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if($transaction->tax > 0)
                <div class="flex justify-between text-slate-700 font-semibold" id="rowTax">
                    <span>PPN (Pajak):</span>
                    <span class="font-mono text-[13px]">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                </div>
            @endif

            <!-- Highlight Box TOTAL Modern Lebar 80MM -->
            <div class="my-3 p-3.5 bg-slate-100 rounded-xl border-2 border-slate-900 flex justify-between items-center">
                <div>
                    <div class="font-black uppercase text-[12px] text-slate-700 tracking-wider">TOTAL TAGIHAN</div>
                    <div class="text-[10px] text-slate-500">Harga sudah termasuk PPN</div>
                </div>
                <div class="font-black font-mono text-lg sm:text-xl text-slate-950">
                    Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                </div>
            </div>

            <!-- Metode & Nominal Bayar -->
            <div class="space-y-1.5 pt-0.5">
                <div class="flex justify-between text-slate-700 font-semibold" id="rowPayment">
                    <span>Metode Bayar ({{ strtoupper($transaction->payment_method) }}):</span>
                    <span class="font-mono font-bold text-slate-950 text-[13px]">Rp {{ number_format($transaction->pay_amount, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between font-black text-slate-950 text-[13px]" id="rowChange">
                    <span>Kembalian:</span>
                    <span class="font-mono text-[14px] text-emerald-800">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($transaction->notes)
            <div class="mt-3 p-2.5 text-[11px] text-slate-700 italic bg-slate-50 rounded-lg border border-slate-200" id="rowNotes">
                <span class="font-bold not-italic text-slate-900">Catatan:</span> {{ $transaction->notes }}
            </div>
        @endif

        <div class="thermal-divider"></div>

        <!-- 5. FOOTER NOTA MODERN (Barcode Lebar 80mm & Pesan Penutup) -->
        <div class="pt-2 pb-5 text-center space-y-2.5 text-[12px] text-slate-700" id="sectionFooter">
            <!-- Code 128 Thermal Barcode Vector Lebar Penuh -->
            <div class="pt-1 pb-1 flex flex-col items-center justify-center">
                <svg class="h-12 w-full max-w-[280px] mx-auto text-slate-950" viewBox="0 0 220 38" fill="currentColor">
                    <rect x="0" y="0" width="3" height="38"/>
                    <rect x="5" y="0" width="2" height="38"/>
                    <rect x="9" y="0" width="5" height="38"/>
                    <rect x="16" y="0" width="3" height="38"/>
                    <rect x="21" y="0" width="4" height="38"/>
                    <rect x="27" y="0" width="2" height="38"/>
                    <rect x="31" y="0" width="5" height="38"/>
                    <rect x="38" y="0" width="3" height="38"/>
                    <rect x="43" y="0" width="2" height="38"/>
                    <rect x="47" y="0" width="4" height="38"/>
                    <rect x="53" y="0" width="3" height="38"/>
                    <rect x="58" y="0" width="5" height="38"/>
                    <rect x="65" y="0" width="2" height="38"/>
                    <rect x="69" y="0" width="4" height="38"/>
                    <rect x="75" y="0" width="3" height="38"/>
                    <rect x="80" y="0" width="4" height="38"/>
                    <rect x="86" y="0" width="2" height="38"/>
                    <rect x="90" y="0" width="5" height="38"/>
                    <rect x="97" y="0" width="3" height="38"/>
                    <rect x="102" y="0" width="4" height="38"/>
                    <rect x="108" y="0" width="2" height="38"/>
                    <rect x="112" y="0" width="5" height="38"/>
                    <rect x="119" y="0" width="3" height="38"/>
                    <rect x="124" y="0" width="2" height="38"/>
                    <rect x="128" y="0" width="4" height="38"/>
                    <rect x="134" y="0" width="3" height="38"/>
                    <rect x="139" y="0" width="5" height="38"/>
                    <rect x="146" y="0" width="2" height="38"/>
                    <rect x="150" y="0" width="4" height="38"/>
                    <rect x="156" y="0" width="3" height="38"/>
                    <rect x="161" y="0" width="4" height="38"/>
                    <rect x="167" y="0" width="2" height="38"/>
                    <rect x="171" y="0" width="5" height="38"/>
                    <rect x="178" y="0" width="3" height="38"/>
                    <rect x="183" y="0" width="4" height="38"/>
                    <rect x="189" y="0" width="2" height="38"/>
                    <rect x="193" y="0" width="5" height="38"/>
                    <rect x="200" y="0" width="3" height="38"/>
                    <rect x="205" y="0" width="4" height="38"/>
                    <rect x="211" y="0" width="4" height="38"/>
                    <rect x="217" y="0" width="3" height="38"/>
                </svg>
                <div class="font-mono text-[11px] tracking-widest text-slate-900 mt-1.5 font-black">{{ $transaction->invoice_code }}</div>
            </div>

            <div class="space-y-1 pt-1">
                <p class="font-black text-slate-950 tracking-wider text-[13px]" id="valFooterMsg">*** TERIMA KASIH TELAH BERBELANJA ***</p>
                <p class="text-[11px] text-slate-600">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
                <p class="text-[10px] text-slate-500">Simpan struk ini sebagai bukti pembayaran resmi</p>
            </div>

            <div class="pt-3 text-[10px] font-mono text-slate-500 border-t border-slate-200 flex items-center justify-between">
                <span>MyTokoGue POS v2.0</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <!-- Sawtooth / Gerigi Sobekan Kertas Struk (Tampilan Layar Estetik) -->
        <div class="w-full overflow-hidden leading-none no-print -mb-[1px] absolute -bottom-2 left-0 right-0" style="line-height: 0;">
            <svg viewBox="0 0 120 4" fill="none" class="w-full h-2 text-slate-100" preserveAspectRatio="none">
                <path d="M0 0 L3 4 L6 0 L9 4 L12 0 L15 4 L18 0 L21 4 L24 0 L27 4 L30 0 L33 4 L36 0 L39 4 L42 0 L45 4 L48 0 L51 4 L54 0 L57 4 L60 0 L63 4 L66 0 L69 4 L72 0 L75 4 L78 0 L81 4 L84 0 L87 4 L90 0 L93 4 L96 0 L99 4 L102 0 L105 4 L108 0 L111 4 L114 0 L117 4 L120 0 V4 H0 Z" fill="currentColor"/>
            </svg>
        </div>

    </div>

    <!-- MODAL DRAWER: PENGATURAN KUSTOMISASI NOTA (NO PRINT) -->
    <div id="settingsModal" class="no-print fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-sliders text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Pengaturan Format & Ukuran Nota</h3>
                        <p class="text-xs text-slate-500">Default 80mm Roll (Lebar Penuh), 58mm Roll, atau 58x40 Label</p>
                    </div>
                </div>
                <button type="button" onclick="toggleSettingsModal()" class="text-slate-400 hover:text-slate-700 p-1">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Form Pengaturan -->
            <form id="receiptSettingsForm" onsubmit="saveCustomSettings(event)" class="space-y-4 text-xs">
                
                <!-- 1. Dimensi Kertas -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <h4 class="font-bold text-slate-800 uppercase text-[11px] tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-ruler-combined text-emerald-600"></i>
                        <span>Dimensi Kertas Nota</span>
                    </h4>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Lebar Kertas (Width)</label>
                            <div class="flex items-center gap-1">
                                <input type="number" id="cfgWidth" min="40" max="120" step="1" value="80"
                                    class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-800 font-bold text-center">
                                <span class="font-bold text-slate-500">mm</span>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tinggi Kertas (Height)</label>
                            <select id="cfgHeightType" onchange="toggleHeightInput()"
                                class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-800 font-medium">
                                <option value="auto" selected>Auto (Panjang Mengikuti Isi - 80mm)</option>
                                <option value="40mm">Pas 40 mm (Label 58x40)</option>
                                <option value="custom">Tinggi Kustom (mm)</option>
                            </select>
                        </div>
                    </div>

                    <div id="customHeightWrapper" class="hidden">
                        <label class="block font-semibold text-slate-700 mb-1">Tinggi Kustom (mm)</label>
                        <div class="flex items-center gap-1">
                            <input type="number" id="cfgCustomHeight" min="20" max="300" step="1" value="40"
                                class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-800 font-bold text-center">
                            <span class="font-bold text-slate-500">mm</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Tipografi & Margin -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <h4 class="font-bold text-slate-800 uppercase text-[11px] tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-font text-emerald-600"></i>
                        <span>Ukuran Huruf & Margin</span>
                    </h4>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Ukuran Font</label>
                            <select id="cfgFontSize" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-800">
                                <option value="10px">10px (Kecil)</option>
                                <option value="12px">12px (Standar)</option>
                                <option value="13px" selected>13px (Optimal 80mm Modern)</option>
                                <option value="14px">14px (Besar & Jelas)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Margin Pinggir (Padding)</label>
                            <select id="cfgPadding" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-800">
                                <option value="1.5mm">1.5 mm (Sangat Tipis / Edge-to-Edge)</option>
                                <option value="2.5mm" selected>2.5 mm (Optimal 80mm Full)</option>
                                <option value="4mm">4 mm (Standar)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 3. Informasi Header Toko -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <h4 class="font-bold text-slate-800 uppercase text-[11px] tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-store text-emerald-600"></i>
                        <span>Informasi Nota Toko</span>
                    </h4>

                    <div class="space-y-2">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-0.5">Nama Toko</label>
                            <input type="text" id="cfgStoreName" value="MYTOKOGUE"
                                class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-800">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-0.5">Alamat Singkat</label>
                            <input type="text" id="cfgStoreAddress" value="Jl. Toko Utama No. 88, Indonesia"
                                class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-800">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-0.5">No. Telp / WhatsApp</label>
                            <input type="text" id="cfgStorePhone" value="Telp/WA: 0812-3456-7890"
                                class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-800">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-0.5">Pesan Bawah (Footer)</label>
                            <input type="text" id="cfgFooterMsg" value="*** TERIMA KASIH TELAH BERBELANJA ***"
                                class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-800">
                        </div>
                    </div>
                </div>

                <!-- 4. Tombol Toggle Komponen -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                    <h4 class="font-bold text-slate-800 uppercase text-[11px] tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-eye text-emerald-600"></i>
                        <span>Tampilkan / Sembunyikan Elemen</span>
                    </h4>

                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="cfgShowHeader" checked class="w-3.5 h-3.5 text-emerald-600 rounded">
                            <span>Header Info Toko</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="cfgShowCashier" checked class="w-3.5 h-3.5 text-emerald-600 rounded">
                            <span>Nama Kasir</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="cfgShowCustomer" checked class="w-3.5 h-3.5 text-emerald-600 rounded">
                            <span>Nama Pelanggan</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="cfgShowFooter" checked class="w-3.5 h-3.5 text-emerald-600 rounded">
                            <span>Pesan Footer</span>
                        </label>
                    </div>
                </div>

                <!-- Aksi Simpan / Batal -->
                <div class="pt-2 flex items-center justify-between gap-2">
                    <button type="button" onclick="resetDefaultSettings()" 
                        class="text-xs text-rose-600 hover:text-rose-800 font-semibold underline">
                        Reset Default (80mm Full)
                    </button>

                    <div class="flex gap-2">
                        <button type="button" onclick="toggleSettingsModal()" 
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors">
                            Batal
                        </button>

                        <button type="submit" 
                            class="px-5 py-2 bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white font-black rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all">
                            Terapkan & Simpan
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- JAVASCRIPT STATE ENGINE FORMAT NOTA & LOCALSTORAGE -->
    <script>
        // Kunci penyimpanan baru agar konfigurasi default 80mm langsung aktif
        const STORAGE_KEY = 'mytokogue_receipt_config_80mm_v7';

        // Default Config: Khusus 80mm Roll Lebar Penuh & Panjang ke Bawah
        const defaultSettings = {
            preset: '80roll',
            width: 80,
            heightType: 'auto',
            customHeight: 40,
            fontSize: '13px',
            padding: '2.5mm',
            lineHeight: '1.45',
            storeName: 'MYTOKOGUE',
            storeAddress: 'Jl. Toko Utama No. 88, Indonesia',
            storePhone: 'Telp/WA: 0812-3456-7890',
            footerMsg: '*** TERIMA KASIH TELAH BERBELANJA ***',
            showHeader: true,
            showCashier: true,
            showCustomer: true,
            showFooter: true,
        };

        let currentSettings = { ...defaultSettings };

        // Muat dari LocalStorage saat halaman dibuka
        function loadSettings() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved) {
                    const parsed = JSON.parse(saved);
                    currentSettings = { ...defaultSettings, ...parsed };
                    // Pastikan jika preset 80roll, lebarnya wajib 80mm
                    if (currentSettings.preset === '80roll') {
                        currentSettings.width = 80;
                    }
                } else {
                    // Bersihkan cache kunci lama agar tidak tertimpa settingan 58mm lama
                    localStorage.removeItem('mytokogue_receipt_config_v4');
                    localStorage.removeItem('mytokogue_receipt_config_v3');
                    localStorage.removeItem('mytokogue_receipt_config_v2');
                }
            } catch (e) {
                console.error('Failed to load receipt settings from localStorage', e);
            }

            populateFormFields();
            applyDOMSettings();
        }

        // Terapkan ke DOM & CSS
        function applyDOMSettings() {
            const root = document.documentElement;
            const w = parseInt(currentSettings.width) || 80;
            let h = 'auto';

            if (currentSettings.heightType === '40mm') {
                h = '40mm';
            } else if (currentSettings.heightType === 'custom') {
                h = (currentSettings.customHeight || 40) + 'mm';
            } else {
                h = 'auto';
            }

            // Lebar printhead nyata: jika 80mm, printable width adalah 76mm. Jika 58mm adalah 54mm.
            const printSlipWidth = w >= 75 ? '76mm' : '54mm';

            // Atur CSS variable dinamis
            root.style.setProperty('--paper-width', `${w}mm`);
            root.style.setProperty('--print-slip-width', printSlipWidth);
            root.style.setProperty('--paper-height', h);
            root.style.setProperty('--receipt-font-size', currentSettings.fontSize || '13px');
            root.style.setProperty('--receipt-padding', currentSettings.padding || '2.5mm');
            root.style.setProperty('--receipt-line-height', currentSettings.lineHeight || '1.45');

            // Atur style @page secara dinamis agar valid dan dipatuhi Chrome
            let pageCss = '';
            if (w >= 75) {
                // Ukuran standar Windows driver thermal 80mm: 80 x 297 mm
                pageCss = `
                    @page {
                        size: 80mm 297mm;
                        margin: 0mm !important;
                    }
                    @page :first {
                        size: 80mm 297mm;
                        margin: 0mm !important;
                    }
                `;
            } else if (currentSettings.heightType === '40mm') {
                pageCss = `
                    @page {
                        size: 58mm 40mm;
                        margin: 0mm !important;
                    }
                `;
            } else {
                pageCss = `
                    @page {
                        size: 58mm 210mm;
                        margin: 0mm !important;
                    }
                `;
            }

            let dynamicPageStyle = document.getElementById('dynamicPageStyle');
            if (!dynamicPageStyle) {
                dynamicPageStyle = document.createElement('style');
                dynamicPageStyle.id = 'dynamicPageStyle';
                document.head.appendChild(dynamicPageStyle);
            }
            dynamicPageStyle.textContent = pageCss;

            // Atur konten teks
            document.getElementById('valStoreName').textContent = currentSettings.storeName;
            document.getElementById('valStoreAddress').textContent = currentSettings.storeAddress;
            document.getElementById('valStorePhone').textContent = currentSettings.storePhone;
            document.getElementById('valFooterMsg').textContent = currentSettings.footerMsg;

            // Toggle visibilitas elemen
            document.getElementById('sectionStoreHeader').style.display = currentSettings.showHeader ? 'block' : 'none';
            document.getElementById('rowCashier').style.display = currentSettings.showCashier ? 'flex' : 'none';
            document.getElementById('rowCustomer').style.display = currentSettings.showCustomer ? 'flex' : 'none';
            document.getElementById('sectionFooter').style.display = currentSettings.showFooter ? 'block' : 'none';

            // Update UI preset pills
            updatePresetPillsUI(currentSettings.preset);
        }

        function updatePresetPillsUI(preset) {
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.className = 'preset-btn px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 flex items-center gap-1.5';
            });

            if (preset === '80roll') {
                const b = document.getElementById('btnPreset80roll');
                if (b) b.className = 'preset-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all border border-[#5DD62C] bg-[#5DD62C]/15 text-[#337418] shadow-xs flex items-center gap-1.5';
            } else if (preset === '58roll') {
                const b = document.getElementById('btnPreset58roll');
                if (b) b.className = 'preset-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all border border-[#5DD62C] bg-[#5DD62C]/15 text-[#337418] shadow-xs flex items-center gap-1.5';
            } else if (preset === '58x40') {
                const b = document.getElementById('btnPreset58x40');
                if (b) b.className = 'preset-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all border border-[#5DD62C] bg-[#5DD62C]/15 text-[#337418] shadow-xs flex items-center gap-1.5';
            }
        }

        // Terapkan Preset Langsung dari Toolbar
        function applyPreset(presetType) {
            currentSettings.preset = presetType;

            if (presetType === '80roll') {
                currentSettings.width = 80;
                currentSettings.heightType = 'auto';
                currentSettings.fontSize = '13px';
                currentSettings.padding = '2.5mm';
                currentSettings.lineHeight = '1.45';
            } else if (presetType === '58roll') {
                currentSettings.width = 58;
                currentSettings.heightType = 'auto';
                currentSettings.fontSize = '9.5px';
                currentSettings.padding = '2mm';
                currentSettings.lineHeight = '1.25';
            } else if (presetType === '58x40') {
                currentSettings.width = 58;
                currentSettings.heightType = '40mm';
                currentSettings.fontSize = '8.5px';
                currentSettings.padding = '1.5mm';
                currentSettings.lineHeight = '1.15';
            }

            saveToStorage();
            populateFormFields();
            applyDOMSettings();
        }

        // Isi form modal dengan nilai currentSettings
        function populateFormFields() {
            document.getElementById('cfgWidth').value = currentSettings.width;
            document.getElementById('cfgHeightType').value = currentSettings.heightType;
            document.getElementById('cfgCustomHeight').value = currentSettings.customHeight || 40;
            document.getElementById('cfgFontSize').value = currentSettings.fontSize;
            document.getElementById('cfgPadding').value = currentSettings.padding;
            document.getElementById('cfgStoreName').value = currentSettings.storeName;
            document.getElementById('cfgStoreAddress').value = currentSettings.storeAddress;
            document.getElementById('cfgStorePhone').value = currentSettings.storePhone;
            document.getElementById('cfgFooterMsg').value = currentSettings.footerMsg;
            document.getElementById('cfgShowHeader').checked = currentSettings.showHeader;
            document.getElementById('cfgShowCashier').checked = currentSettings.showCashier;
            document.getElementById('cfgShowCustomer').checked = currentSettings.showCustomer;
            document.getElementById('cfgShowFooter').checked = currentSettings.showFooter;

            toggleHeightInput();
        }

        function toggleHeightInput() {
            const hType = document.getElementById('cfgHeightType').value;
            const customWrapper = document.getElementById('customHeightWrapper');
            if (hType === 'custom') {
                customWrapper.classList.remove('hidden');
            } else {
                customWrapper.classList.add('hidden');
            }
        }

        function toggleSettingsModal() {
            const modal = document.getElementById('settingsModal');
            modal.classList.toggle('hidden');
        }

        // Handler Cetak dengan jaminan styling 80mm
        function handlePrint() {
            // Pastikan konfigurasi aktif adalah 80mm sebelum print jika memakai 80roll
            if (currentSettings.preset === '80roll') {
                currentSettings.width = 80;
                applyDOMSettings();
            }
            window.print();
        }

        // Simpan Form Pengaturan Kustom
        function saveCustomSettings(e) {
            e.preventDefault();

            currentSettings = {
                preset: 'custom',
                width: parseInt(document.getElementById('cfgWidth').value) || 80,
                heightType: document.getElementById('cfgHeightType').value,
                customHeight: parseInt(document.getElementById('cfgCustomHeight').value) || 40,
                fontSize: document.getElementById('cfgFontSize').value,
                padding: document.getElementById('cfgPadding').value,
                lineHeight: '1.45',
                storeName: document.getElementById('cfgStoreName').value.trim() || 'MYTOKOGUE',
                storeAddress: document.getElementById('cfgStoreAddress').value.trim(),
                storePhone: document.getElementById('cfgStorePhone').value.trim(),
                footerMsg: document.getElementById('cfgFooterMsg').value.trim(),
                showHeader: document.getElementById('cfgShowHeader').checked,
                showCashier: document.getElementById('cfgShowCashier').checked,
                showCustomer: document.getElementById('cfgShowCustomer').checked,
                showFooter: document.getElementById('cfgShowFooter').checked,
            };

            saveToStorage();
            applyDOMSettings();
            toggleSettingsModal();
        }

        function resetDefaultSettings() {
            currentSettings = { ...defaultSettings };
            saveToStorage();
            populateFormFields();
            applyDOMSettings();
            toggleSettingsModal();
        }

        function saveToStorage() {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(currentSettings));
            } catch (err) {
                console.error('Storage error', err);
            }
        }

        // Jalankan inisialisasi saat window load
        window.addEventListener('DOMContentLoaded', loadSettings);
    </script>
</body>
</html>
