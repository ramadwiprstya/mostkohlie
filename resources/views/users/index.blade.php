@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'users', createModal: {{ $errors->any() && old('name') ? 'true' : 'false' }}, editModal: false, deleteModal: false, selectedUser: null, userToDelete: null }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#70746F]">
        <span>Lainnya</span>
        <span>/</span>
        <span class="font-semibold text-[#24364A]">Pengaturan Sistem</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A]">Pengaturan Sistem & Pengguna</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Kelola hak akses akun barista, batas reminder kedaluwarsa, dan notifikasi stok.</p>
        </div>

        <button type="button" 
                @click="createModal = true"
                class="px-4 py-2.5 rounded-xl bg-[#305785] hover:bg-[#274A70] text-white text-xs font-bold transition shadow-xs flex items-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Pengguna Baru</span>
        </button>
    </div>

    <!-- Alert Success / Error / Validation -->
    @if(session('success'))
        <div class="callout-box callout-success">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-[#39715A] shrink-0 mt-0.5"></i>
            <div>
                <strong class="font-bold">Berhasil!</strong>
                <p class="text-xs text-[#24364A] mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="callout-box callout-danger">
            <i data-lucide="alert-circle" class="w-5 h-5 text-[#A65043] shrink-0 mt-0.5"></i>
            <div>
                <strong class="font-bold">Perhatian!</strong>
                <p class="text-xs text-[#24364A] mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="callout-box callout-danger">
            <i data-lucide="alert-octagon" class="w-5 h-5 text-[#A65043] shrink-0 mt-0.5"></i>
            <div>
                <strong class="font-bold">Terdapat Kesalahan:</strong>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-[#A65043]">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- 2-Column Layout matching Prototype -->
    <div class="grid grid-cols-1 md:grid-cols-[240px_1fr] items-start gap-6">

        <!-- Left Settings Navigation -->
        <div class="bg-[#FFFDFA] p-3 rounded-2xl border border-[#D8CDBD] shadow-xs flex flex-col gap-1.5">
            <button type="button"
                    @click="activeTab = 'users'"
                    :class="activeTab === 'users' ? 'bg-[#305785] text-white shadow-xs' : 'text-[#24364A] hover:bg-[#F4F1EB]/80'"
                    class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2.5 transition">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Manajemen Pengguna</span>
            </button>

            <button type="button"
                    @click="activeTab = 'reminders'"
                    :class="activeTab === 'reminders' ? 'bg-[#305785] text-white shadow-xs' : 'text-[#24364A] hover:bg-[#F4F1EB]/80'"
                    class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2.5 transition">
                <i data-lucide="bell" class="w-4 h-4"></i>
                <span>Reminder & Notifikasi</span>
            </button>
        </div>

        <!-- Right Content Area -->
        <div class="space-y-6">

            <!-- TAB 1: User Management -->
            <div x-show="activeTab === 'users'" class="space-y-4">
                <div class="bg-[#FFFDFA] rounded-3xl border border-[#D8CDBD] shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-[#E8E1D6] flex items-center justify-between">
                        <div>
                            <h3 class="font-serif text-base font-bold text-[#24364A]">Daftar Pengguna Operasional</h3>
                            <p class="text-xs text-[#70746F] mt-0.5">Akun resmi Owner dan Barista yang berhak mengakses sistem</p>
                        </div>
                        <span class="text-xs font-semibold text-[#70746F]">
                            Total {{ $users->total() }} Staf
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-[#F4F1EB] border-b border-[#D8CDBD] text-[11px] font-bold text-[#70746F] uppercase tracking-wider">
                                    <th class="py-3.5 px-4">Nama Lengkap</th>
                                    <th class="py-3.5 px-4">Email Akun</th>
                                    <th class="py-3.5 px-4">Peran (Role)</th>
                                    <th class="py-3.5 px-4">No. HP</th>
                                    <th class="py-3.5 px-4">Status</th>
                                    <th class="py-3.5 px-4">Terdaftar</th>
                                    <th class="py-3.5 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E8E1D6]">
                                @forelse($users as $user)
                                    <tr class="hover:bg-[#F4F1EB]/50 transition">
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-[#305785]/10 text-[#305785] flex items-center justify-center font-bold text-xs shrink-0">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                                <span class="font-bold text-[#24364A] text-sm">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 font-mono text-[#70746F]">{{ $user->email }}</td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $user->isOwner() ? 'bg-[#A16A28]/15 text-[#A16A28]' : 'bg-[#305785]/15 text-[#305785]' }} uppercase">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-[#70746F]">{{ $user->phone ?: '-' }}</td>
                                        <td class="py-3.5 px-4">
                                            @if($user->is_active)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#39715A]/15 text-[#39715A]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#39715A]"></span>
                                                    <span>Aktif</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#A65043]/15 text-[#A65043]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#A65043]"></span>
                                                    <span>Nonaktif</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-[#70746F]">{{ $user->created_at->format('d/m/Y') }}</td>
                                        <td class="py-3.5 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button" 
                                                        @click="selectedUser = {{ json_encode($user) }}; editModal = true; $nextTick(() => window.lucide && lucide.createIcons())"
                                                        class="p-1.5 rounded-lg border border-[#D8CDBD] text-[#70746F] hover:bg-[#E8E1D6]/40 hover:text-[#305785] transition" 
                                                        title="Edit akun pengguna">
                                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                                </button>

                                                @if($user->id !== auth()->id())
                                                    <button type="button" 
                                                            @click="userToDelete = {{ json_encode($user) }}; deleteModal = true; $nextTick(() => window.lucide && lucide.createIcons())"
                                                            class="p-1.5 rounded-lg border border-[#D8CDBD] text-[#A65043] hover:bg-[#F8E8E5] hover:border-[#A65043]/30 transition" 
                                                            title="Hapus pengguna">
                                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                @else
                                                    <span class="p-1.5 text-slate-300 cursor-not-allowed" title="Akun Anda (sedang aktif)">
                                                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-xs text-[#70746F]">Belum ada data pengguna.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-[#E8E1D6]">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>

            <!-- TAB 2: Parameter Reminder -->
            <div x-show="activeTab === 'reminders'" class="space-y-4" style="display: none;">
                <div class="bg-[#FFFDFA] rounded-3xl border border-[#D8CDBD] shadow-xs p-6">
                    <div class="border-b border-[#E8E1D6] pb-4 mb-5">
                        <h3 class="font-serif text-base font-bold text-[#24364A]">Konfigurasi Parameter Reminder</h3>
                        <p class="text-xs text-[#70746F] mt-0.5">Aturan otomatis peringatan stok dan tanggal kedaluwarsa</p>
                    </div>

                    <form onsubmit="event.preventDefault(); alert('Pengaturan reminder tersimpan!');" class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-[#24364A] mb-1.5">Pemicu Reminder Sebelum Kedaluwarsa (Hari)</label>
                            <div class="flex items-center gap-3">
                                <input type="number" value="3" class="w-24 px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs font-bold text-[#24364A] bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                                <span class="text-xs text-[#70746F]">Hari sebelum tanggal expired (Default: H-3)</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#24364A] mb-1.5">Notifikasi Stok Minimum Aktif</label>
                            <label class="flex items-center gap-2.5 cursor-pointer text-xs text-[#24364A]">
                                <input type="checkbox" checked class="w-4 h-4 rounded text-[#305785] focus:ring-[#305785] accent-[#305785]">
                                <span>Aktifkan alarm merah di topbar jika stok bahan berada &le; batas aman minimum</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#24364A] mb-1.5">Penerima Email Rekap Pengadaan Harian</label>
                            <input type="email" value="andra.owner@kopikohlie.com" class="w-full sm:w-96 px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                        </div>

                        <div class="pt-3 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#305785] hover:bg-[#274A70] text-white text-xs font-bold transition shadow-xs flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                <span>Simpan Pengaturan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Tambah Pengguna Baru -->
    <div x-show="createModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="createModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#D8CDBD] animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8E1D6]">
                <h3 class="font-serif font-bold text-base text-[#24364A]">Tambah Pengguna Baru</h3>
                <button @click="createModal = false" class="text-[#70746F] hover:text-[#24364A]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('users.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Rian Pratama"
                           class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Email Akun *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="rian@kopikohlie.com"
                           class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Peran / Role *</label>
                        <select name="role" required class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                            <option value="barista" {{ old('role', 'barista') === 'barista' ? 'selected' : '' }}>Barista (Staf Bar & Kasir)</option>
                            <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner / Admin (Akses Penuh)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">No. HP / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08..."
                               class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Kata Sandi (Password) *</label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                           class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="user_active_create" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                           class="rounded border-[#D8CDBD] text-[#305785] focus:ring-[#305785] accent-[#305785]">
                    <label for="user_active_create" class="text-xs text-[#24364A] font-semibold cursor-pointer">
                        Akun Aktif (Dapat langsung digunakan untuk login)
                    </label>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl border border-[#D8CDBD] text-[#70746F] text-xs font-bold hover:bg-[#E8E1D6]/40 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#305785] hover:bg-[#274A70] text-white text-xs font-bold transition shadow-xs">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Pengguna -->
    <div x-show="editModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="editModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#D8CDBD] animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8E1D6]">
                <h3 class="font-serif font-bold text-base text-[#24364A]">Edit Pengguna</h3>
                <button @click="editModal = false" class="text-[#70746F] hover:text-[#24364A]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'/users/' + selectedUser?.id" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" :value="selectedUser?.name" required
                           class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Email *</label>
                    <input type="email" name="email" :value="selectedUser?.email" required
                           class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Role *</label>
                        <select name="role" x-model="selectedUser.role" required class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                            <option value="barista">Barista (Staf Bar & Kasir)</option>
                            <option value="owner">Owner / Admin (Akses Penuh)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">No. HP</label>
                        <input type="text" name="phone" :value="selectedUser?.phone"
                               class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" minlength="6" placeholder="Password baru..."
                           class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="user_active_edit" value="1" :checked="selectedUser?.is_active"
                           class="rounded border-[#D8CDBD] text-[#305785] focus:ring-[#305785] accent-[#305785]">
                    <label for="user_active_edit" class="text-xs text-[#24364A] font-semibold cursor-pointer">Akun Aktif</label>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-[#D8CDBD] text-[#70746F] text-xs font-bold hover:bg-[#E8E1D6]/40 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#305785] hover:bg-[#274A70] text-white text-xs font-bold transition shadow-xs">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Pengguna -->
    <div x-show="deleteModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="deleteModal = false" 
             class="bg-[#FFFDFA] rounded-[24px] p-6 sm:p-7 w-[calc(100%-2rem)] max-w-[480px] mx-auto shadow-2xl border border-[#D8CDBD] animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Badge Peringatan -->
            <div class="mb-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-extrabold tracking-wider uppercase bg-[#F8E8E5] text-[#A65043] border border-[#A65043]/20">
                    PERINGATAN
                </span>
            </div>

            <!-- Judul Utama -->
            <h3 class="font-serif font-bold text-lg sm:text-xl text-[#24364A] mb-2 leading-tight">
                Konfirmasi Hapus Pengguna
            </h3>

            <!-- Deskripsi -->
            <p class="text-xs text-[#70746F] leading-relaxed mb-4">
                Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini akan menghapus akses login pengguna secara permanen.
            </p>

            <!-- Panel Info Pengguna -->
            <div class="bg-[#FAF8F5] rounded-xl p-4 border border-[#D8CDBD] space-y-2.5 mb-5 text-xs">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[#70746F] font-medium shrink-0">Nama Pengguna</span>
                    <strong class="text-[#24364A] font-bold text-right break-words" x-text="userToDelete?.name"></strong>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[#70746F] font-medium shrink-0">Email</span>
                    <span class="text-[#24364A] font-mono font-medium text-right break-words" x-text="userToDelete?.email"></span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[#70746F] font-medium shrink-0">Role</span>
                    <span class="capitalize px-2 py-0.5 rounded-md text-[10px] font-bold"
                          :class="userToDelete?.role === 'owner' ? 'bg-[#C5A880]/20 text-[#8C6D3F]' : 'bg-[#305785]/10 text-[#305785]'"
                          x-text="userToDelete?.role"></span>
                </div>
                <div class="flex items-start gap-2 pt-2 border-t border-[#D8CDBD]/70 text-[#A65043] text-[11px] leading-tight">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                    <span>Jika pengguna sudah memiliki riwayat transaksi/rekap, sistem akan menyarankan untuk menonaktifkan akun daripada menghapus.</span>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <form :action="'/users/' + userToDelete?.id" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E8E1D6]">
                    <button type="button" @click="deleteModal = false" 
                            class="px-4 py-2 rounded-xl border border-[#D8CDBD] bg-white hover:bg-[#F4F1EB] text-xs font-bold text-[#24364A] transition shadow-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 rounded-xl bg-[#A65043] hover:bg-[#8A3F33] text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Hapus Pengguna</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
