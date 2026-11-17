@extends('layouts.app')

@section('title', 'Kelola Pengguna & Kasir')
@section('header-title', 'Kelola Pengguna & Akun Kasir')

@section('content')
<div class="space-y-6 pt-2">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- FORM TAMBAH PENGGUNA (1 COL) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs h-fit">
            <h3 class="text-sm font-black text-[#0F0F0F] mb-1">Tambah Akun Baru</h3>
            <p class="text-xs text-slate-500 mb-5">Daftarkan akun kasir baru untuk shift pelayanan toko.</p>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-bold">
                    @error('name') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="kasir@mytoko.com"
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-medium">
                    @error('email') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Peran / Role <span class="text-rose-500">*</span></label>
                    <select name="role" required class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-slate-700 font-bold">
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir (Akses Mesin POS & Transaksi)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Akses Penuh Seluruh Sistem)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08123456789"
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F] font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi Awal <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter"
                        class="w-full px-3.5 py-2.5 text-xs bg-[#F8F8F8] border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5DD62C] focus:bg-white text-[#0F0F0F]">
                    @error('password') <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#5DD62C] hover:bg-[#337418] active:bg-[#245311] text-[#0F0F0F] hover:text-white font-black text-xs rounded-xl shadow-md shadow-[#5DD62C]/25 transition-all">
                    Daftarkan Pengguna
                </button>
            </form>
        </div>

        <!-- DAFTAR PENGGUNA (2 COL) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-[#0F0F0F]">Daftar Akun Terdaftar</h3>
                    <p class="text-xs text-slate-500">Kelola akun kasir dan administrator</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F8F8F8] text-slate-500 border-b border-slate-100 uppercase tracking-wider text-[10px]">
                            <th class="p-3.5 pl-5">Nama & Kontak</th>
                            <th class="p-3.5">Peran / Role</th>
                            <th class="p-3.5">Total Transaksi</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5 pr-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-[#F8F8F8]/80 transition-colors">
                                <td class="p-3.5 pl-5">
                                    <div class="font-bold text-[#0F0F0F]">{{ $user->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-medium">{{ $user->email }} {{ $user->phone ? '• ' . $user->phone : '' }}</div>
                                </td>
                                <td class="p-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $user->isAdmin() ? 'bg-[#202020] text-[#5DD62C] border border-[#337418]/40' : 'bg-[#5DD62C]/20 text-[#337418] border border-[#5DD62C]/30' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="p-3.5 font-bold text-slate-700">
                                    {{ $user->transactions_count }} transaksi
                                </td>
                                <td class="p-3.5">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#337418]">
                                            <span class="w-2 h-2 rounded-full bg-[#5DD62C]"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-rose-500">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5 pr-5 text-right">
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan/menghapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus / Nonaktifkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic font-medium">Akun Anda</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
