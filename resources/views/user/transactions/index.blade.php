@extends('layouts.app')

@section('title', 'Riwayat Pembelian Saya')
@section('header-title', 'Riwayat Pembelian')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-1 sm:pt-2 pb-28 sm:pb-8">

    <!-- Filter & Header Bar (Responsif Mobile) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 sm:gap-4">
        <div class="min-w-0">
            <h2 class="text-sm sm:text-base font-black text-[#0F0F0F] truncate">Riwayat Pembelian: {{ auth()->user()->name }}</h2>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Pantau status pembayaran Virtual Account (Duitku) dan riwayat belanja Anda.</p>
        </div>

        <form method="GET" action="{{ route('user.transactions.index') }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <input type="date" name="date" value="{{ $date }}"
                class="flex-1 sm:flex-none px-3 py-2 sm:py-1.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
            <button type="submit" class="px-4 py-2 sm:py-1.5 bg-[#5DD62C] hover:bg-[#337418] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-xs transition-colors">
                Filter
            </button>
            @if(!empty($date))
                <a href="{{ route('user.transactions.index') }}" class="px-3 py-2 sm:py-1.5 bg-[#F8F8F8] hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition-colors">
                    Reset
                </a>
            @endif
            <a href="{{ route('user.products.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2 sm:py-1.5 bg-[#0F0F0F] hover:bg-[#202020] text-[#5DD62C] font-bold text-xs rounded-xl transition-colors">
                <i class="fa-solid fa-bag-shopping text-[11px]"></i>
                <span>Kembali Belanja</span>
            </a>
        </form>
    </div>

    <!-- Banner Notifikasi Hasil Cek Status -->
    <div id="statusNoticeBox" class="hidden p-3.5 rounded-2xl text-xs font-bold flex items-center justify-between gap-2"></div>

    <!-- Summary Cards (2 Kolom Sejajar Bahkan di HP) -->
    <div class="grid grid-cols-2 gap-2.5 sm:gap-4">
        <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-2">
            <div class="min-w-0">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block truncate">
                    Total Belanja Lunas
                </span>
                <h3 class="text-sm sm:text-2xl font-black text-[#337418] mt-1 truncate">Rp {{ number_format($totalSpent, 0, ',', '.') }}</h3>
            </div>
            <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-[#5DD62C]/20 text-[#337418] flex items-center justify-center font-black border border-[#5DD62C]/30 shadow-xs shrink-0">
                <i class="fa-solid fa-wallet text-sm sm:text-lg"></i>
            </div>
        </div>

        <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-2">
            <div class="min-w-0">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block truncate">Total Transaksi</span>
                <h3 class="text-sm sm:text-2xl font-black text-[#0F0F0F] mt-1 truncate">{{ $totalCount }} <span class="text-[11px] sm:text-base font-bold text-slate-500">Order</span></h3>
            </div>
            <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-[#0F0F0F] text-[#5DD62C] flex items-center justify-center font-black shadow-xs shrink-0">
                <i class="fa-solid fa-receipt text-sm sm:text-lg"></i>
            </div>
        </div>
    </div>

    <!-- TAMPILAN KARTU KHUSUS HANDPHONE (< MD) — Tanpa Scroll Horizontal -->
    <div class="md:hidden space-y-3">
        @forelse($transactions as $trx)
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 space-y-3">
                <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-slate-100">
                    <div class="min-w-0">
                        <span class="font-mono font-black text-xs text-[#337418] block truncate">{{ $trx->invoice_code }}</span>
                        <span class="text-[11px] text-slate-400 font-medium block mt-0.5">{{ $trx->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex flex-col items-end gap-1 shrink-0">
                        @if($trx->isPaid())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30">Lunas</span>
                        @elseif($trx->isPending())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-200">Menunggu Bayar</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-700 border border-rose-200">{{ ucfirst($trx->payment_status) }}</span>
                        @endif
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-[#202020] text-[#5DD62C]">
                            {{ $trx->payment_method === 'va' ? 'VA ' . strtoupper($trx->va_bank ?? '') : strtoupper($trx->payment_method) }}
                        </span>
                    </div>
                </div>

                @if($trx->isPending() && $trx->va_number)
                    <div class="p-3 rounded-xl bg-[#0F0F0F] text-white text-xs space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[10px] font-bold text-[#5DD62C] uppercase">No. VA {{ strtoupper($trx->va_bank ?? '') }}</span>
                            <button type="button" onclick="copyVaQuick('{{ $trx->va_number }}', this)" class="text-[10px] font-black text-[#0F0F0F] bg-[#5DD62C] px-2 py-0.5 rounded-md">Salin</button>
                        </div>
                        <div class="font-mono font-black text-sm tracking-wider">{{ $trx->va_number }}</div>
                        @if($trx->va_biller_code)
                            <div class="text-[10px] text-slate-300">Biller Code: <strong class="font-mono text-white">{{ $trx->va_biller_code }}</strong></div>
                        @endif
                    </div>
                @endif

                <div class="space-y-1 text-xs text-slate-600">
                    @foreach($trx->items as $it)
                        <div class="flex items-center justify-between gap-2">
                            <span class="truncate font-medium">• {{ $it->product_name }} <span class="text-slate-400 font-bold">({{ $it->quantity }}x)</span></span>
                            <span class="font-bold text-[#0F0F0F] shrink-0">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-2.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Total Tagihan</span>
                        <span class="text-sm font-black text-[#0F0F0F]">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        @if($trx->isPending())
                            <a href="{{ $trx->payment_url ?: 'https://sandbox.duitku.com' }}" target="_blank"
                                class="inline-flex items-center gap-1 px-2.5 py-2 text-[11px] font-bold text-white bg-[#202020] hover:bg-[#0F0F0F] rounded-xl transition-all">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-[#5DD62C]"></i>
                                <span>Simulator</span>
                            </a>
                            <button type="button" onclick="checkRowStatus('{{ route('user.transactions.check-status', $trx->id) }}', this)"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-[11px] font-black text-[#0F0F0F] bg-[#5DD62C] hover:bg-[#337418] hover:text-white rounded-xl transition-all shadow-xs">
                                <i class="fa-solid fa-rotate text-[10px]"></i>
                                <span>Cek Status</span>
                            </button>
                        @else
                            <a href="{{ route('transactions.receipt', $trx->id) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-black text-[#0F0F0F] bg-[#5DD62C] hover:bg-[#337418] hover:text-white rounded-xl transition-all shadow-xs shrink-0">
                                <i class="fa-solid fa-receipt text-xs"></i>
                                <span>Lihat Struk</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-400">
                <i class="fa-solid fa-receipt text-2xl text-slate-300 mb-2 block"></i>
                <span class="text-xs font-bold text-slate-500">Belum ada riwayat pembelian.</span>
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN TABEL DESKTOP & TABLET (MD+) -->
    <div class="hidden md:block bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F8F8F8] text-slate-500 border-b border-slate-100 uppercase tracking-wider text-[10px]">
                        <th class="p-3.5 pl-5">No. Invoice</th>
                        <th class="p-3.5">Produk Dibeli</th>
                        <th class="p-3.5">Metode & No. VA</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Total Tagihan</th>
                        <th class="p-3.5">Tanggal & Waktu</th>
                        <th class="p-3.5 pr-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-[#F8F8F8]/80 transition-colors">
                            <td class="p-3.5 pl-5 font-mono font-bold text-[#337418]">
                                {{ $trx->invoice_code }}
                            </td>
                            <td class="p-3.5 text-slate-600 max-w-xs font-medium">
                                <div class="text-[11px] space-y-0.5">
                                    @foreach($trx->items as $it)
                                        <div>• {{ $it->product_name }} <span class="text-slate-400 font-bold">({{ $it->quantity }}x)</span></div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-[#202020] text-[#5DD62C] border border-[#2A2A2A]">
                                    {{ $trx->payment_method === 'va' ? 'VA ' . strtoupper($trx->va_bank ?? '') : strtoupper($trx->payment_method) }}
                                </span>
                                @if($trx->va_number)
                                    <div class="mt-1 flex items-center gap-1.5 font-mono text-[11px] font-bold text-[#0F0F0F]">
                                        <span>{{ $trx->va_number }}</span>
                                        <button type="button" onclick="copyVaQuick('{{ $trx->va_number }}', this)" class="text-[10px] text-[#337418] hover:underline font-sans font-bold">Salin</button>
                                    </div>
                                    @if($trx->va_biller_code)
                                        <div class="text-[10px] text-slate-500">Biller: <span class="font-mono font-bold text-[#0F0F0F]">{{ $trx->va_biller_code }}</span></div>
                                    @endif
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($trx->isPaid())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#337418]"></span> Lunas
                                    </span>
                                @elseif($trx->isPending())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu Bayar
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-700 border border-rose-200">
                                        {{ ucfirst($trx->payment_status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 font-black text-[#0F0F0F]">
                                Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-slate-500 whitespace-nowrap">
                                {{ $trx->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                @if($trx->isPending())
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ $trx->payment_url ?: 'https://sandbox.duitku.com' }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-bold text-white bg-[#202020] hover:bg-[#0F0F0F] rounded-lg transition-all"
                                            title="Buka Simulator Duitku Sandbox">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-[#5DD62C]"></i>
                                            <span>Simulator</span>
                                        </a>
                                        <button type="button" onclick="checkRowStatus('{{ route('user.transactions.check-status', $trx->id) }}', this)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-black text-[#0F0F0F] bg-[#5DD62C] hover:bg-[#337418] hover:text-white rounded-lg transition-all">
                                            <i class="fa-solid fa-rotate text-[10px]"></i>
                                            <span>Cek Status</span>
                                        </button>
                                    </div>
                                @else
                                    <a href="{{ route('transactions.receipt', $trx->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-[#337418] bg-[#5DD62C]/15 hover:bg-[#5DD62C] hover:text-[#0F0F0F] rounded-lg transition-all border border-[#5DD62C]/30">
                                        <i class="fa-solid fa-receipt text-[11px]"></i>
                                        <span>Struk</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-2xl text-slate-300 mb-2 block"></i>
                                <span class="font-bold text-slate-500">Belum ada riwayat pembelian.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($transactions->hasPages())
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs">
            {{ $transactions->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function copyVaQuick(vaNumber, btnEl) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(vaNumber).then(() => {
                const orig = btnEl.textContent;
                btnEl.textContent = 'Tersalin!';
                setTimeout(() => { btnEl.textContent = orig; }, 1500);
            });
        }
    }

    async function checkRowStatus(url, btnEl) {
        const notice = document.getElementById('statusNoticeBox');
        const origHtml = btnEl.innerHTML;
        btnEl.disabled = true;
        btnEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[10px]"></i><span>Cek...</span>';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Gagal mengecek status ke Duitku Sandbox.');
            }

            notice.classList.remove('hidden');
            if (data.is_paid) {
                notice.className = 'p-3.5 rounded-2xl bg-[#5DD62C]/20 border border-[#5DD62C]/40 text-[#337418] text-xs font-bold flex items-center justify-between gap-2';
                notice.innerHTML = `<span><i class="fa-solid fa-circle-check mr-1.5"></i>${data.message}</span>`;
                setTimeout(() => window.location.reload(), 900);
            } else if (data.payment_status === 'expired' || data.payment_status === 'failed') {
                notice.className = 'p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center justify-between gap-2';
                notice.innerHTML = `<span><i class="fa-solid fa-circle-xmark mr-1.5"></i>${data.message}</span>`;
                setTimeout(() => window.location.reload(), 1200);
            } else {
                notice.className = 'p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold flex items-center justify-between gap-2';
                notice.innerHTML = `<span><i class="fa-solid fa-clock mr-1.5"></i>${data.message}</span>`;
            }
        } catch (err) {
            notice.classList.remove('hidden');
            notice.className = 'p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center justify-between gap-2';
            notice.innerHTML = `<span><i class="fa-solid fa-circle-exclamation mr-1.5"></i>${err.message}</span>`;
        } finally {
            btnEl.disabled = false;
            btnEl.innerHTML = origHtml;
        }
    }
</script>
@endpush
