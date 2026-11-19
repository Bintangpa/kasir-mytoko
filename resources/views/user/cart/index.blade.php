@extends('layouts.app')

@section('title', 'Keranjang & Pembayaran')
@section('header-title', 'Keranjang & Pembayaran')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-1 sm:pt-2 pb-28 sm:pb-12">

    <!-- HEADER BAR KERANJANG -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-2xl bg-[#0F0F0F] text-[#5DD62C] flex items-center justify-center shrink-0 shadow-xs">
                <i class="fa-solid fa-cart-shopping text-base"></i>
            </div>
            <div class="min-w-0">
                <h2 class="text-sm sm:text-base font-black text-[#0F0F0F] truncate">Keranjang Belanja & Pembayaran</h2>
                <p id="cartHeaderSubtitle" class="text-[11px] sm:text-xs text-slate-500">0 Jenis (0 Item) siap dibayar</p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('user.products.index') }}"
               class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#F8F8F8] hover:bg-slate-200/80 text-[#0F0F0F] border border-slate-200 font-bold text-xs transition-colors">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
                <span>Tambah Produk</span>
            </a>
            <button type="button" id="btnClearCartTop" onclick="clearCart()"
                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 font-bold text-xs transition-colors">
                <i class="fa-solid fa-trash-can text-[11px]"></i>
                <span>Kosongkan</span>
            </button>
        </div>
    </div>

    <!-- STATE KERANJANG KOSONG -->
    <div id="emptyCartPageState" class="hidden bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-8 sm:p-14 text-center max-w-xl mx-auto">
        <div class="w-16 h-16 rounded-3xl bg-[#F8F8F8] border border-slate-200 text-slate-300 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-cart-shopping text-2xl"></i>
        </div>
        <h3 class="text-base sm:text-lg font-black text-[#0F0F0F]">Keranjang Belanja Masih Kosong</h3>
        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
            Anda belum memilih produk apa pun. Jelajahi katalog produk untuk menambahkan barang ke troli.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-2.5">
            <a href="{{ route('user.products.index') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white font-black text-xs sm:text-sm shadow-md shadow-[#5DD62C]/25 transition-all">
                <i class="fa-solid fa-bag-shopping"></i>
                <span>Lihat Katalog Produk</span>
            </a>
            <a href="{{ route('user.transactions.index') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#F8F8F8] hover:bg-slate-200 text-[#0F0F0F] font-bold text-xs sm:text-sm border border-slate-200 transition-all">
                <i class="fa-solid fa-clock-rotate-left text-[#337418]"></i>
                <span>Riwayat Pembelian</span>
            </a>
        </div>
    </div>

    <!-- KONTEN UTAMA KERANJANG & PEMBAYARAN (2 KOLOM DI DESKTOP, TERSUSUN RAPI DI HP) -->
    <div id="cartMainContent" class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">

        <!-- KOLOM KIRI: DAFTAR ITEM KERANJANG -->
        <div class="lg:col-span-7 space-y-3">
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-[#F8F8F8] border-b border-slate-200/80 flex items-center justify-between">
                    <span class="text-xs font-black text-[#0F0F0F] uppercase tracking-wider">Daftar Barang Pilihan</span>
                    <span id="cartItemCountPill" class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30">0 Item</span>
                </div>

                <div id="cartItemsContainer" class="divide-y divide-slate-100 p-3 sm:p-4 space-y-2.5"></div>
            </div>
        </div>

        <!-- KOLOM KANAN: RINGKASAN & FORM PEMBAYARAN (QRIS & VA MIDTRANS SANDBOX) -->
        <div class="lg:col-span-5 lg:sticky lg:top-2">
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-md overflow-hidden">
                <div class="p-4 sm:p-5 bg-[#0F0F0F] text-white border-b border-[#2A2A2A]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#5DD62C] block">Pembayaran Non-Tunai</span>
                    <h3 class="text-base font-black mt-0.5">Selesaikan Transaksi Anda</h3>
                </div>

                <div class="p-4 sm:p-5 bg-[#F8F8F8] space-y-4">
                    <!-- Rincian Tagihan -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span class="font-semibold">Nama Pemesan</span>
                            <span class="font-bold text-[#0F0F0F]">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span class="font-semibold">Total Barang</span>
                            <span class="font-bold text-[#0F0F0F]" id="summaryTotalQty">0 item</span>
                        </div>
                        <div class="flex items-center justify-between pt-2.5 border-t border-slate-100">
                            <span class="text-xs font-black text-[#0F0F0F] uppercase tracking-wider">Total Tagihan</span>
                            <span class="text-xl font-black text-[#337418]" id="cartTotalText">Rp 0</span>
                        </div>
                    </div>

                    <!-- Pilih Metode Pembayaran (Hanya QRIS & Virtual Account) -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider">Metode Pembayaran</label>
                            <span class="text-[10px] font-semibold text-slate-400">Hanya QRIS & VA</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2" id="paymentMethodGroup">
                            <button type="button" onclick="setPaymentMethod('qris')" data-method="qris"
                                class="pay-btn active py-2.5 px-3 rounded-xl text-xs font-black border transition-all bg-[#5DD62C] text-[#0F0F0F] border-[#5DD62C] shadow-2xs flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-qrcode text-sm"></i>
                                <span>QRIS</span>
                            </button>
                            <button type="button" onclick="setPaymentMethod('va')" data-method="va"
                                class="pay-btn py-2.5 px-3 rounded-xl text-xs font-bold border transition-all bg-white text-slate-700 border-slate-200 hover:bg-slate-50 flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-building-columns text-sm"></i>
                                <span>Virtual Account (VA)</span>
                            </button>
                        </div>
                    </div>

                    <!-- PANEL INFO QRIS (TERINTEGRASI DUITKU SANDBOX) -->
                    <div id="qrisInfoBox" class="p-4 rounded-2xl bg-[#202020] text-white border border-[#2E2E2E] space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 font-black text-xs text-[#5DD62C]">
                                <i class="fa-solid fa-qrcode text-sm"></i>
                                <span>QRIS (Duitku Sandbox)</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-md bg-[#5DD62C]/15 text-[#5DD62C] text-[10px] font-bold border border-[#5DD62C]/30">Live API</span>
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Kode QRIS resmi akan dibuat langsung oleh <strong class="text-white">Duitku Sandbox</strong>. Anda dapat scan QR atau klik tombol simulator Duitku untuk simulasi pelunasan instan.
                        </p>
                    </div>

                    <!-- PANEL INFO VIRTUAL ACCOUNT (TERINTEGRASI DUITKU SANDBOX) -->
                    <div id="vaInfoBox" class="hidden p-4 rounded-2xl bg-[#202020] text-white border border-[#2E2E2E] space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 font-black text-xs text-[#5DD62C]">
                                <i class="fa-solid fa-building-columns text-sm"></i>
                                <span>Virtual Account (Duitku Sandbox)</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-md bg-[#5DD62C]/15 text-[#5DD62C] text-[10px] font-bold border border-[#5DD62C]/30">Live API</span>
                        </div>

                        <!-- Pilihan Bank VA Duitku -->
                        <div class="space-y-1.5">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Bank Virtual Account:</span>
                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-1.5" id="vaBankGroup">
                                <button type="button" onclick="setVaBank('bca')" data-bank="bca"
                                    class="va-bank-btn py-1.5 rounded-lg text-[11px] font-black bg-[#5DD62C] text-[#0F0F0F] border border-[#5DD62C] transition-all">BCA</button>
                                <button type="button" onclick="setVaBank('bni')" data-bank="bni"
                                    class="va-bank-btn py-1.5 rounded-lg text-[11px] font-bold bg-[#0F0F0F] text-slate-300 border border-[#2E2E2E] hover:border-[#5DD62C]/50 transition-all">BNI</button>
                                <button type="button" onclick="setVaBank('bri')" data-bank="bri"
                                    class="va-bank-btn py-1.5 rounded-lg text-[11px] font-bold bg-[#0F0F0F] text-slate-300 border border-[#2E2E2E] hover:border-[#5DD62C]/50 transition-all">BRI</button>
                                <button type="button" onclick="setVaBank('mandiri')" data-bank="mandiri"
                                    class="va-bank-btn py-1.5 rounded-lg text-[11px] font-bold bg-[#0F0F0F] text-slate-300 border border-[#2E2E2E] hover:border-[#5DD62C]/50 transition-all">Mandiri</button>
                                <button type="button" onclick="setVaBank('permata')" data-bank="permata"
                                    class="va-bank-btn py-1.5 rounded-lg text-[11px] font-bold bg-[#0F0F0F] text-slate-300 border border-[#2E2E2E] hover:border-[#5DD62C]/50 transition-all">Permata</button>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-[#0F0F0F] border border-[#2E2E2E] text-[11px] text-slate-300 leading-relaxed flex items-start gap-2">
                            <i class="fa-solid fa-circle-info text-[#5DD62C] mt-0.5 shrink-0"></i>
                            <span>
                                Nomor Virtual Account resmi dibuat langsung oleh <strong class="text-white">Duitku Sandbox</strong> setelah Anda klik tombol bayar. Anda dapat mensimulasikan pembayaran di Simulator Duitku lalu klik <em>Cek Status</em>.
                            </span>
                        </div>
                    </div>

                    <!-- Catatan Pesanan -->
                    <div>
                        <label for="paymentNotes" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Catatan Pesanan (Opsional)</label>
                        <input type="text" id="paymentNotes" maxlength="255" placeholder="Contoh: Pisahkan kantong plastik..."
                            class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] text-[#0F0F0F] font-medium">
                    </div>

                    <!-- Pesan Error Inline -->
                    <div id="checkoutErrorBox" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-start gap-2">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
                        <span id="checkoutErrorText"></span>
                    </div>

                    <!-- Tombol Bayar Utama -->
                    <button type="button" id="btnCheckout" onclick="processPayment()"
                        class="w-full py-3.5 px-5 bg-[#5DD62C] hover:bg-[#337418] active:bg-[#245311] disabled:opacity-50 disabled:cursor-not-allowed text-[#0F0F0F] hover:text-white font-black text-sm rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span id="btnCheckoutLabel">BAYAR SEKARANG</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MODAL MENUNGGU PEMBAYARAN VIRTUAL ACCOUNT (DUITKU SANDBOX) -->
<div id="vaPendingModal" class="fixed inset-0 z-50 bg-[#0F0F0F]/85 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 hidden">
    <div class="bg-white rounded-t-3xl sm:rounded-3xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200 max-h-[92vh] flex flex-col">
        <div class="p-4 sm:p-5 bg-[#0F0F0F] text-white flex items-center justify-between border-b border-[#2A2A2A]">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-400/20 text-amber-400 border border-amber-400/30 flex items-center justify-center">
                    <i class="fa-solid fa-hourglass-half text-sm"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#5DD62C] block">Duitku Sandbox VA</span>
                    <h3 class="text-sm sm:text-base font-black">Menunggu Pembayaran</h3>
                </div>
            </div>
            <span id="vaModalInvoice" class="font-mono text-[11px] font-bold text-slate-300 bg-[#202020] px-2.5 py-1 rounded-lg border border-[#2E2E2E]">TRX-XXXX</span>
        </div>

        <div class="p-4 sm:p-5 space-y-4 overflow-y-auto text-xs">
            <!-- Biller Code (Jika ada) -->
            <div id="vaModalBillerWrap" class="hidden p-3.5 rounded-2xl bg-[#F8F8F8] border border-slate-200 flex items-center justify-between gap-2">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Kode Perusahaan / Biller</span>
                    <span id="vaModalBillerCode" class="font-mono text-base font-black text-[#0F0F0F] tracking-wider">-</span>
                </div>
                <button type="button" onclick="copyText('vaModalBillerCode', this)"
                    class="px-3 py-1.5 rounded-xl bg-[#0F0F0F] hover:bg-[#5DD62C] text-white hover:text-[#0F0F0F] text-[11px] font-black transition-colors">
                    Salin
                </button>
            </div>

            <!-- Nomor Virtual Account Asli Duitku -->
            <div class="p-4 rounded-2xl bg-[#0F0F0F] text-white border border-[#2A2A2A] space-y-2">
                <div class="flex items-center justify-between">
                    <span id="vaModalBankTitle" class="text-[10px] font-bold text-[#5DD62C] uppercase tracking-wider">Virtual Account</span>
                    <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-[10px] font-bold">Pending</span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span id="vaModalNumber" class="font-mono text-base sm:text-lg font-black text-white tracking-wider break-all">-</span>
                    <button type="button" onclick="copyText('vaModalNumber', this)"
                        class="px-3 py-1.5 rounded-xl bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white text-[11px] font-black transition-colors shrink-0">
                        Salin VA
                    </button>
                </div>
            </div>

            <!-- Nominal & Batas Waktu -->
            <div class="bg-[#F8F8F8] p-3.5 rounded-2xl border border-slate-200 space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">Total Pembayaran</span>
                    <span id="vaModalTotal" class="font-black text-sm text-[#337418]">Rp 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-semibold">Berlaku Hingga</span>
                    <div class="text-right">
                        <span id="vaModalExpiry" class="font-bold text-[#0F0F0F] block">-</span>
                        <span id="vaModalCountdown" class="text-[10px] font-black text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200 mt-0.5 hidden"></span>
                    </div>
                </div>
            </div>

            <!-- Pesan Status Cek Live -->
            <div id="vaStatusFeedback" class="hidden p-3 rounded-xl text-xs font-bold flex items-start gap-2"></div>

            <!-- Aksi: Simulator Duitku & Cek Status -->
            <div class="space-y-2 pt-1 pb-2 sm:pb-0">
                <button type="button" id="btnCheckVaStatus" onclick="checkVaPaymentStatus()"
                    class="w-full py-3 px-4 bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Cek Status Pembayaran</span>
                </button>

                <a id="vaSimulatorLink" href="https://sandbox.duitku.com" target="_blank"
                   class="w-full py-2.5 px-4 bg-[#0F0F0F] hover:bg-[#202020] text-[#5DD62C] font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-2 border border-[#2A2A2A]">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                    <span>Buka Halaman Pembayaran Duitku Sandbox</span>
                </a>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('user.transactions.index') }}"
                       class="py-2.5 px-3 bg-[#F8F8F8] hover:bg-slate-200 text-[#0F0F0F] font-bold text-xs rounded-xl transition-all border border-slate-200 text-center">
                        Riwayat Transaksi
                    </a>
                    <a href="{{ route('user.products.index') }}"
                       class="py-2.5 px-3 bg-[#F8F8F8] hover:bg-slate-200 text-[#0F0F0F] font-bold text-xs rounded-xl transition-all border border-slate-200 text-center">
                        Kembali ke Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL MENUNGGU PEMBAYARAN QRIS (DUITKU SANDBOX) -->
<div id="qrisPendingModal" class="fixed inset-0 z-50 bg-[#0F0F0F]/85 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 hidden">
    <div class="bg-white rounded-t-3xl sm:rounded-3xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200 max-h-[92vh] flex flex-col">
        <div class="p-4 sm:p-5 bg-[#0F0F0F] text-white flex items-center justify-between border-b border-[#2A2A2A]">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-[#5DD62C]/20 text-[#5DD62C] border border-[#5DD62C]/30 flex items-center justify-center">
                    <i class="fa-solid fa-qrcode text-base"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#5DD62C] block">Duitku Sandbox QRIS</span>
                    <h3 class="text-sm sm:text-base font-black">Scan QR untuk Bayar</h3>
                </div>
            </div>
            <span id="qrisModalInvoice" class="font-mono text-[11px] font-bold text-slate-300 bg-[#202020] px-2.5 py-1 rounded-lg border border-[#2E2E2E]">TRX-XXXX</span>
        </div>

        <div class="p-4 sm:p-5 space-y-4 overflow-y-auto text-xs text-center">
            <!-- QRIS Code Image Container -->
            <div class="p-4 rounded-2xl bg-[#F8F8F8] border border-slate-200 inline-block mx-auto shadow-inner">
                <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-xs">
                    <img id="qrisModalImage" src="" alt="QRIS Code" class="w-48 h-48 sm:w-56 sm:h-56 mx-auto object-contain">
                </div>
                <div class="mt-2.5 flex items-center justify-center gap-2">
                    <span class="text-[10px] font-black uppercase text-slate-700 tracking-wider">SHOPEEPAY QRIS (SP)</span>
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#5DD62C]"></span>
                    <span class="text-[10px] font-bold text-[#337418]">NMID Duitku Sandbox</span>
                </div>
            </div>

            <!-- Total Pembayaran & Batas Waktu -->
            <div class="bg-[#F8F8F8] p-3.5 rounded-2xl border border-slate-200 space-y-1.5 text-left">
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">Total Tagihan</span>
                    <span id="qrisModalTotal" class="font-black text-sm text-[#337418]">Rp 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-semibold">Berlaku Hingga</span>
                    <div class="text-right">
                        <span id="qrisModalExpiry" class="font-bold text-[#0F0F0F] block">-</span>
                        <span id="qrisModalCountdown" class="text-[10px] font-black text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200 mt-0.5 hidden"></span>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Simulator Sandbox Duitku -->
            <div class="p-3 bg-emerald-50/90 border border-emerald-200/90 rounded-2xl text-[11px] text-emerald-950 text-left flex items-start gap-2.5">
                <i class="fa-solid fa-circle-info text-[#337418] text-sm mt-0.5 shrink-0"></i>
                <div class="leading-relaxed">
                    <span class="font-black text-[#0F0F0F] block">Petunjuk Demo Simulator:</span>
                    Di halaman simulator Duitku, pada pilihan <span class="font-bold text-slate-800">"Choose Payment Method"</span>, pilih <span class="font-black text-[#337418] bg-white px-1.5 py-0.5 rounded border border-emerald-300">SHOPEEPAY QRIS</span> agar tagihan ditemukan dan tombol bayar muncul.
                </div>
            </div>

            <!-- Pesan Status Cek Live -->
            <div id="qrisStatusFeedback" class="hidden p-3 rounded-xl text-xs font-bold text-left flex items-start gap-2"></div>

            <!-- Aksi: Cek Status & Buka Simulator Duitku -->
            <div class="space-y-2 pt-1 pb-2 sm:pb-0">
                <button type="button" id="btnCheckQrisStatus" onclick="checkQrisPaymentStatus()"
                    class="w-full py-3 px-4 bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Cek Status Pembayaran</span>
                </button>

                <a id="qrisSimulatorLink" href="https://sandbox.duitku.com" target="_blank"
                   class="w-full py-2.5 px-4 bg-[#0F0F0F] hover:bg-[#202020] text-[#5DD62C] font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-2 border border-[#2A2A2A]">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                    <span>Buka Halaman Pembayaran / Simulator QRIS</span>
                </a>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('user.transactions.index') }}"
                       class="py-2.5 px-3 bg-[#F8F8F8] hover:bg-slate-200 text-[#0F0F0F] font-bold text-xs rounded-xl transition-all border border-slate-200 text-center">
                        Riwayat Transaksi
                    </a>
                    <a href="{{ route('user.products.index') }}"
                       class="py-2.5 px-3 bg-[#F8F8F8] hover:bg-slate-200 text-[#0F0F0F] font-bold text-xs rounded-xl transition-all border border-slate-200 text-center">
                        Kembali ke Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL SUKSES PEMBAYARAN LUNAS -->
<div id="successModal" class="fixed inset-0 z-50 bg-[#0F0F0F]/80 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 hidden">
    <div class="bg-white rounded-t-3xl sm:rounded-3xl max-w-sm w-full p-5 sm:p-6 text-center shadow-2xl space-y-4 border border-slate-200 max-h-[90vh] overflow-y-auto">
        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/40 flex items-center justify-center mx-auto shadow-inner">
            <i class="fa-solid fa-check text-2xl"></i>
        </div>

        <div>
            <h3 class="text-base sm:text-lg font-black text-[#0F0F0F]">Pembayaran Berhasil!</h3>
            <p class="text-xs text-slate-500 font-mono font-bold mt-1" id="modalInvoiceCode">TRX-XXXXXXXX</p>
        </div>

        <div class="bg-[#F8F8F8] p-3.5 sm:p-4 rounded-2xl space-y-1.5 text-xs text-left border border-slate-200">
            <div class="flex justify-between text-slate-600">
                <span class="font-medium">Metode Bayar:</span>
                <span class="font-black text-[#0F0F0F]" id="modalMethod">QRIS</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span class="font-medium">Total Tagihan:</span>
                <span class="font-bold text-[#0F0F0F]" id="modalTotal">Rp 0</span>
            </div>
            <div class="flex justify-between text-[#337418] font-black text-sm pt-1.5 border-t border-slate-200">
                <span>Status:</span>
                <span>Lunas</span>
            </div>
        </div>

        <div class="space-y-2 pt-1 pb-2 sm:pb-0">
            <a id="modalPrintBtn" href="#" target="_blank"
               class="w-full py-2.5 px-4 bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-receipt"></i>
                <span>Lihat Struk Pembayaran</span>
            </a>

            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('user.transactions.index') }}"
                   class="py-2.5 px-3 bg-[#0F0F0F] hover:bg-[#202020] text-white font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left text-[#5DD62C] text-[11px]"></i>
                    <span>Riwayat</span>
                </a>
                <a href="{{ route('user.products.index') }}"
                   class="py-2.5 px-3 bg-[#F8F8F8] hover:bg-slate-200 text-[#0F0F0F] font-bold text-xs rounded-xl transition-all border border-slate-200 flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-bag-shopping text-[#337418] text-[11px]"></i>
                    <span>Katalog</span>
                </a>
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
    let activePaymentMethod = 'qris';
    let activeVaBank = 'bca';
    let pendingVaContext = null;
    let pendingQrisContext = null;

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
    }

    function showCheckoutError(msg) {
        const box = document.getElementById('checkoutErrorBox');
        const txt = document.getElementById('checkoutErrorText');
        if (msg) {
            txt.textContent = msg;
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }

    function loadCart() {
        const stored = window.getUserCart ? window.getUserCart() : [];
        cart = stored
            .filter(item => productsMap[item.id] && productsMap[item.id].stock > 0)
            .map(item => {
                const prod = productsMap[item.id];
                const clampedQty = Math.min(Math.max(1, parseInt(item.qty, 10) || 1), prod.stock);
                return {
                    id: prod.id,
                    code: prod.code,
                    category: prod.category ? prod.category.name : 'Umum',
                    name: prod.name,
                    unit: prod.unit || 'pcs',
                    image_url: prod.image_url || null,
                    price: parseFloat(prod.price),
                    stock: prod.stock,
                    qty: clampedQty
                };
            });

        persistCart();
        renderCart();
        setVaBank(activeVaBank);
    }

    function persistCart() {
        if (window.saveUserCart) {
            window.saveUserCart(cart.map(i => ({ id: i.id, qty: i.qty })));
        }
    }

    function updateCartQty(prodId, delta) {
        showCheckoutError('');
        const index = cart.findIndex(i => i.id === prodId);
        if (index === -1) return;

        const newQty = cart[index].qty + delta;
        if (newQty <= 0) {
            cart.splice(index, 1);
        } else if (newQty > cart[index].stock) {
            showCheckoutError(`Stok ${cart[index].name} tersisa ${cart[index].stock}.`);
            return;
        } else {
            cart[index].qty = newQty;
        }

        persistCart();
        renderCart();
    }

    function setCartQtyDirect(prodId, value) {
        showCheckoutError('');
        const index = cart.findIndex(i => i.id === prodId);
        if (index === -1) return;

        let parsed = parseInt(value, 10);
        if (isNaN(parsed) || parsed < 1) parsed = 1;
        if (parsed > cart[index].stock) {
            parsed = cart[index].stock;
            showCheckoutError(`Stok ${cart[index].name} maksimal ${cart[index].stock}.`);
        }

        cart[index].qty = parsed;
        persistCart();
        renderCart();
    }

    function removeCartItem(prodId) {
        cart = cart.filter(i => i.id !== prodId);
        persistCart();
        renderCart();
    }

    function clearCart() {
        if (cart.length === 0) return;
        cart = [];
        showCheckoutError('');
        persistCart();
        renderCart();
    }

    function getCartTotal() {
        return cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    }

    function renderCart() {
        const emptyState = document.getElementById('emptyCartPageState');
        const mainContent = document.getElementById('cartMainContent');
        const container = document.getElementById('cartItemsContainer');
        const btnClearTop = document.getElementById('btnClearCartTop');

        const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
        document.getElementById('cartHeaderSubtitle').textContent = `${cart.length} Jenis (${totalQty} Item) siap dibayar`;
        document.getElementById('cartItemCountPill').textContent = `${totalQty} Item`;
        document.getElementById('summaryTotalQty').textContent = `${totalQty} item`;
        document.getElementById('cartTotalText').textContent = 'Rp ' + formatNumber(getCartTotal());

        if (cart.length === 0) {
            emptyState.classList.remove('hidden');
            mainContent.classList.add('hidden');
            btnClearTop.classList.add('hidden');
            container.innerHTML = '';
        } else {
            emptyState.classList.add('hidden');
            mainContent.classList.remove('hidden');
            btnClearTop.classList.remove('hidden');

            container.innerHTML = cart.map(item => `
                <div class="pt-2.5 first:pt-0">
                    <div class="p-3 sm:p-4 bg-[#F8F8F8] border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            ${item.image_url ? `
                                <img src="${item.image_url}" alt="${item.name}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0 shadow-2xs">
                            ` : `
                                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 text-slate-300 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-box text-sm"></i>
                                </div>
                            `}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-mono text-[10px] font-bold text-slate-400">${item.code}</span>
                                    <span class="text-[10px] font-bold text-[#337418] bg-[#5DD62C]/15 px-2 py-0.5 rounded-md">Stok ${item.stock}</span>
                                </div>
                                <h4 class="text-xs sm:text-sm font-black text-[#0F0F0F] leading-snug">${item.name}</h4>
                                <div class="text-xs text-slate-500 font-medium mt-1">
                                    Rp ${formatNumber(item.price)} / ${item.unit} × <span class="font-bold text-[#337418]">${item.qty}</span>
                                    = <span class="font-black text-[#0F0F0F]">Rp ${formatNumber(item.price * item.qty)}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200/70 shrink-0">
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="updateCartQty(${item.id}, -1)"
                                    class="w-8 h-8 rounded-xl bg-white border border-slate-300 text-slate-700 font-black hover:bg-slate-100 flex items-center justify-center text-xs shadow-2xs">-</button>
                                <input type="number" min="1" max="${item.stock}" value="${item.qty}"
                                    onchange="setCartQtyDirect(${item.id}, this.value)"
                                    class="w-12 h-8 text-center font-black text-xs text-[#0F0F0F] bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C]">
                                <button type="button" onclick="updateCartQty(${item.id}, 1)"
                                    class="w-8 h-8 rounded-xl bg-white border border-slate-300 text-slate-700 font-black hover:bg-slate-100 flex items-center justify-center text-xs shadow-2xs">+</button>
                            </div>
                            <button type="button" onclick="removeCartItem(${item.id})"
                                class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/60 flex items-center justify-center text-xs transition-colors" title="Hapus dari keranjang">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    }

    function setPaymentMethod(method) {
        if (method !== 'qris' && method !== 'va') return;
        activePaymentMethod = method;
        showCheckoutError('');

        document.querySelectorAll('.pay-btn').forEach(btn => {
            if (btn.dataset.method === method) {
                btn.className = 'pay-btn active py-2.5 px-3 rounded-xl text-xs font-black border transition-all bg-[#5DD62C] text-[#0F0F0F] border-[#5DD62C] shadow-2xs flex items-center justify-center gap-1.5';
            } else {
                btn.className = 'pay-btn py-2.5 px-3 rounded-xl text-xs font-bold border transition-all bg-white text-slate-700 border-slate-200 hover:bg-slate-50 flex items-center justify-center gap-1.5';
            }
        });

        const qrisBox = document.getElementById('qrisInfoBox');
        const vaBox = document.getElementById('vaInfoBox');
        const btnLabel = document.getElementById('btnCheckoutLabel');

        if (method === 'va') {
            qrisBox.classList.add('hidden');
            vaBox.classList.remove('hidden');
            btnLabel.textContent = 'BUAT NOMOR VIRTUAL ACCOUNT';
        } else {
            vaBox.classList.add('hidden');
            qrisBox.classList.remove('hidden');
            btnLabel.textContent = 'BAYAR SEKARANG';
        }
    }

    function setVaBank(bank) {
        activeVaBank = bank;

        document.querySelectorAll('.va-bank-btn').forEach(btn => {
            if (btn.dataset.bank === bank) {
                btn.className = 'va-bank-btn py-1.5 rounded-lg text-[11px] font-black bg-[#5DD62C] text-[#0F0F0F] border border-[#5DD62C] transition-all';
            } else {
                btn.className = 'va-bank-btn py-1.5 rounded-lg text-[11px] font-bold bg-[#0F0F0F] text-slate-300 border border-[#2E2E2E] hover:border-[#5DD62C]/50 transition-all';
            }
        });
    }

    function copyText(elementId, btnEl) {
        const text = document.getElementById(elementId).textContent.replace(/\s+/g, '');
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                const orig = btnEl.textContent;
                btnEl.textContent = 'Tersalin!';
                setTimeout(() => { btnEl.textContent = orig; }, 1600);
            });
        }
    }

    let liveCountdownTimer = null;

    function formatRealtimeExpiry(data) {
        if (data.payment_expired_ts) {
            const date = new Date(data.payment_expired_ts * 1000);
            return formatLocalExpiryDate(date);
        }
        if (data.payment_expired_iso) {
            const date = new Date(data.payment_expired_iso);
            return formatLocalExpiryDate(date);
        }
        return data.payment_expired_at || '-';
    }

    function formatLocalExpiryDate(date) {
        if (!date || isNaN(date.getTime())) return '-';
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const day = date.getDate();
        const month = months[date.getMonth()];
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${day} ${month} ${year}, ${hours}:${minutes} WIB`;
    }

    function startLiveCountdown(data, countdownElId) {
        clearInterval(liveCountdownTimer);
        const el = document.getElementById(countdownElId);
        if (!el) return;

        let targetTime = null;
        if (data.payment_expired_ts) {
            targetTime = data.payment_expired_ts * 1000;
        } else if (data.payment_expired_iso) {
            targetTime = new Date(data.payment_expired_iso).getTime();
        }

        if (!targetTime || isNaN(targetTime)) {
            el.classList.add('hidden');
            return;
        }

        function tick() {
            const now = Date.now();
            const diff = targetTime - now;

            if (diff <= 0) {
                clearInterval(liveCountdownTimer);
                el.textContent = 'Waktu Habis';
                el.className = 'text-[10px] font-black text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full inline-block mt-0.5';
                el.classList.remove('hidden');
                return;
            }

            const totalSecs = Math.floor(diff / 1000);
            const hours = Math.floor(totalSecs / 3600);
            const mins = Math.floor((totalSecs % 3600) / 60);
            const secs = totalSecs % 60;

            let countdownText = '';
            if (hours > 0) {
                countdownText = `${hours}j ${mins}m ${secs}d tersisa`;
            } else {
                countdownText = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')} tersisa`;
            }

            el.textContent = countdownText;
            if (diff < 300000) { // < 5 menit
                el.className = 'text-[10px] font-black text-rose-600 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full inline-block mt-0.5 animate-pulse';
            } else {
                el.className = 'text-[10px] font-black text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full inline-block mt-0.5';
            }
            el.classList.remove('hidden');
        }

        tick();
        liveCountdownTimer = setInterval(tick, 1000);
    }

    function openSuccessModal(data) {
        clearInterval(liveCountdownTimer);
        document.getElementById('vaPendingModal').classList.add('hidden');
        const qrisModal = document.getElementById('qrisPendingModal');
        if (qrisModal) qrisModal.classList.add('hidden');
        document.getElementById('modalInvoiceCode').textContent = data.invoice_code;
        document.getElementById('modalMethod').textContent = data.payment_label || (data.payment_method || '').toUpperCase();
        document.getElementById('modalTotal').textContent = 'Rp ' + formatNumber(data.total_price);
        document.getElementById('modalPrintBtn').href = data.receipt_url;
        document.getElementById('successModal').classList.remove('hidden');
    }

    function openQrisPendingModal(data) {
        pendingQrisContext = data;

        document.getElementById('qrisModalInvoice').textContent = data.invoice_code;
        document.getElementById('qrisModalTotal').textContent = 'Rp ' + formatNumber(data.total_price);
        document.getElementById('qrisModalExpiry').textContent = formatRealtimeExpiry(data);
        startLiveCountdown(data, 'qrisModalCountdown');
        document.getElementById('qrisSimulatorLink').href = data.simulator_url || data.payment_url || 'https://sandbox.duitku.com';

        const qrImg = document.getElementById('qrisModalImage');
        if (data.qr_image_url) {
            qrImg.src = data.qr_image_url;
        } else if (data.qr_string) {
            qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(data.qr_string);
        }

        const fb = document.getElementById('qrisStatusFeedback');
        fb.classList.add('hidden');
        document.getElementById('qrisPendingModal').classList.remove('hidden');
    }

    async function checkQrisPaymentStatus() {
        if (!pendingQrisContext || !pendingQrisContext.status_check_url) return;

        const btn = document.getElementById('btnCheckQrisStatus');
        const fb = document.getElementById('qrisStatusFeedback');
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Mengecek ke Duitku Sandbox...</span>';

        try {
            const res = await fetch(pendingQrisContext.status_check_url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const statusRes = await res.json();

            if (!res.ok || !statusRes.success) {
                throw new Error(statusRes.message || 'Gagal mengecek status pembayaran.');
            }

            if (statusRes.is_paid) {
                openSuccessModal({
                    ...pendingQrisContext,
                    receipt_url: statusRes.receipt_url || pendingQrisContext.receipt_url
                });
            } else {
                fb.classList.remove('hidden');
                if (statusRes.payment_status === 'expired' || statusRes.payment_status === 'failed') {
                    fb.className = 'p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-start gap-2';
                    fb.innerHTML = `<i class="fa-solid fa-circle-xmark mt-0.5 shrink-0"></i><span>${statusRes.message}</span>`;
                } else {
                    fb.className = 'p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold flex items-start gap-2';
                    fb.innerHTML = `<i class="fa-solid fa-clock mt-0.5 shrink-0"></i><span>${statusRes.message}</span>`;
                }
            }
        } catch (err) {
            fb.classList.remove('hidden');
            fb.className = 'p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-start gap-2';
            fb.innerHTML = `<i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i><span>${err.message}</span>`;
        } finally {
            btn.disabled = false;
            btn.innerHTML = origHtml;
        }
    }

    function openVaPendingModal(data) {
        pendingVaContext = data;

        document.getElementById('vaModalInvoice').textContent = data.invoice_code;
        document.getElementById('vaModalBankTitle').textContent = `Virtual Account ${data.va_bank || ''}`;
        document.getElementById('vaModalNumber').textContent = data.va_number || '-';
        document.getElementById('vaModalTotal').textContent = 'Rp ' + formatNumber(data.total_price);
        document.getElementById('vaModalExpiry').textContent = formatRealtimeExpiry(data);
        startLiveCountdown(data, 'vaModalCountdown');
        document.getElementById('vaSimulatorLink').href = data.simulator_url || data.payment_url || 'https://sandbox.duitku.com';

        const billerWrap = document.getElementById('vaModalBillerWrap');
        if (data.va_biller_code) {
            document.getElementById('vaModalBillerCode').textContent = data.va_biller_code;
            billerWrap.classList.remove('hidden');
        } else {
            billerWrap.classList.add('hidden');
        }

        const fb = document.getElementById('vaStatusFeedback');
        fb.classList.add('hidden');
        document.getElementById('vaPendingModal').classList.remove('hidden');
    }

    async function checkVaPaymentStatus() {
        if (!pendingVaContext || !pendingVaContext.status_check_url) return;

        const btn = document.getElementById('btnCheckVaStatus');
        const fb = document.getElementById('vaStatusFeedback');
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Mengecek ke Duitku Sandbox...</span>';

        try {
            const res = await fetch(pendingVaContext.status_check_url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const statusRes = await res.json();

            if (!res.ok || !statusRes.success) {
                throw new Error(statusRes.message || 'Gagal mengecek status pembayaran.');
            }

            if (statusRes.is_paid) {
                openSuccessModal({
                    ...pendingVaContext,
                    receipt_url: statusRes.receipt_url || pendingVaContext.receipt_url
                });
            } else {
                fb.classList.remove('hidden');
                if (statusRes.payment_status === 'expired' || statusRes.payment_status === 'failed') {
                    fb.className = 'p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-start gap-2';
                    fb.innerHTML = `<i class="fa-solid fa-circle-xmark mt-0.5 shrink-0"></i><span>${statusRes.message}</span>`;
                } else {
                    fb.className = 'p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold flex items-start gap-2';
                    fb.innerHTML = `<i class="fa-solid fa-clock mt-0.5 shrink-0"></i><span>${statusRes.message}</span>`;
                }
            }
        } catch (err) {
            fb.classList.remove('hidden');
            fb.className = 'p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-start gap-2';
            fb.innerHTML = `<i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i><span>${err.message}</span>`;
        } finally {
            btn.disabled = false;
            btn.innerHTML = origHtml;
        }
    }

    async function processPayment() {
        showCheckoutError('');

        if (cart.length === 0) {
            showCheckoutError('Keranjang belanja masih kosong.');
            return;
        }

        const btn = document.getElementById('btnCheckout');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Menghubungi Duitku...</span>';

        try {
            const payload = {
                items: cart.map(i => ({ id: i.id, qty: i.qty })),
                payment_method: activePaymentMethod,
                notes: document.getElementById('paymentNotes').value.trim()
            };

            if (activePaymentMethod === 'va') {
                payload.va_bank = activeVaBank;
            }

            const res = await fetch("{{ route('user.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Gagal memproses pembayaran.');
            }

            // Kosongkan keranjang di localStorage & perbarui badge troli navbar
            cart = [];
            persistCart();
            renderCart();

            if (data.payment_status === 'pending') {
                if (data.payment_method === 'qris') {
                    openQrisPendingModal(data);
                } else {
                    openVaPendingModal(data);
                }
            } else {
                openSuccessModal(data);
            }
        } catch (err) {
            showCheckoutError(err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    document.addEventListener('DOMContentLoaded', loadCart);
</script>
@endpush
