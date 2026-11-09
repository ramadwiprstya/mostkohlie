@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ 
    createModal: false, 
    editModal: false, 
    deleteModal: false,
    selectedMenu: null,
    menuToDelete: null,
    createPhotoPreview: null,
    editPhotoPreview: null,
    previewPhoto(event, type) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (type === 'create') this.createPhotoPreview = e.target.result;
                if (type === 'edit') this.editPhotoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
}">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <span class="text-xs text-[#70746F]">Menu & Resep</span>
        <span class="separator text-[#B5ACA0] mx-1">/</span>
        <span class="text-xs font-semibold text-[#24364A]">Daftar Menu</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Daftar Menu Minuman</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Koleksi menu coffee shop Kopi Kohlie yang terhubung dengan master komposisi resep bahan.</p>
        </div>

        @if(auth()->user()->isOwner())
            <button type="button" 
                    @click="createPhotoPreview = null; createModal = true; $nextTick(() => window.lucide && lucide.createIcons())"
                    class="btn btn-primary btn-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Menu Baru</span>
            </button>
        @else
            <span class="badge badge-neutral" title="Hanya Owner yang dapat menambah atau mengubah master menu">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                <span>Akses Barista: Hanya Lihat (Read-Only)</span>
            </span>
        @endif
    </div>

    <!-- Search & Filter Bar -->
    <div class="card p-4">
        <form method="GET" action="{{ route('menus.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            <div class="relative flex-1 w-full flex items-center">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#70746F]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode menu..."
                       class="w-full rounded-full border border-[#D8CDBD] bg-white focus:bg-[#FFFDFA] focus:border-[#305785] focus:ring-2 focus:ring-[#305785]/15 text-xs sm:text-sm font-medium text-[#24364A] placeholder-[#8C8F8A] transition shadow-xs outline-none"
                       style="height: 44px; padding-left: 46px; padding-right: 16px;">
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <select name="category_id" class="input-select w-full md:w-48 py-2 text-xs">
                    <option value="">Semua Kategori Menu</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm shrink-0">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter</span>
                </button>

                @if($search || $categoryId)
                    <a href="{{ route('menus.index') }}" class="btn btn-ghost btn-sm shrink-0" title="Reset filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Menus Grid with Realistic Photos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($menus as $menu)
            <div class="card p-0 overflow-hidden flex flex-col justify-between group hover:-translate-y-1 hover:shadow-md transition duration-200">
                <div class="relative">
                    <img src="{{ $menu->photo_url }}" alt="{{ $menu->name }}" class="w-full h-44 object-cover bg-[#E8E1D6]">
                    <div class="absolute top-3 left-3 flex gap-1.5 flex-wrap">
                        @if(stripos($menu->name, 'Kohlie') !== false || stripos($menu->name, 'Butterscotch') !== false)
                            <span class="badge" style="background: #382718; color: #F5D38E; border: 1px solid #F5D38E;">
                                ★ Signature Kohlie
                            </span>
                        @endif
                    </div>
                    <span class="badge badge-neutral absolute top-3 right-3 bg-white/90 backdrop-blur-xs">
                        {{ $menu->category?->name ?? 'Minuman' }}
                    </span>
                </div>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] font-mono font-bold text-[#70746F]">{{ $menu->code }}</span>
                            <span class="badge badge-blue">{{ $menu->activeRecipe?->items->count() ?? 0 }} Bahan Baku</span>
                        </div>
                        <h3 class="font-serif font-bold text-base text-[#24364A] mb-1">{{ $menu->name }}</h3>
                        <p class="text-xs text-[#70746F] line-clamp-2 leading-relaxed mb-3">
                            {{ $menu->description ?? 'Racikan kopi spesial dengan kualitas rasa terstandarisasi master resep.' }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-[#D8CDBD] flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-[#70746F] block">Harga Jual</span>
                            <strong class="text-sm font-bold text-[#24364A]">Rp {{ number_format($menu->price, 0, ',', '.') }}</strong>
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if(auth()->user()->isOwner())
                                <button type="button" 
                                        @click="selectedMenu = {{ json_encode($menu) }}; editPhotoPreview = null; editModal = true; $nextTick(() => window.lucide && lucide.createIcons())"
                                        class="p-1.5 rounded-lg border border-[#D8CDBD] text-[#70746F] hover:bg-[#E8E1D6]/40 transition" 
                                        title="Edit Info Menu">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>

                                <button type="button" 
                                        @click="menuToDelete = {{ json_encode($menu->load('category')) }}; deleteModal = true; $nextTick(() => window.lucide && lucide.createIcons())"
                                        class="p-1.5 rounded-lg border border-[#D8CDBD] text-[#A65043] hover:bg-[#A65043]/10 hover:border-[#A65043]/30 transition" 
                                        title="Hapus Menu">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            @endif
                            <a href="{{ route('menus.recipe', $menu) }}" class="btn btn-secondary btn-sm">
                                <i data-lucide="flask-conical" class="w-3.5 h-3.5 text-[#305785]"></i>
                                <span>Lihat Resep</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-[#70746F]">
                <i data-lucide="coffee" class="w-10 h-10 text-[#B5ACA0] mx-auto mb-2"></i>
                <p class="font-bold text-sm">Belum ada menu yang terdata.</p>
            </div>
        @endforelse
    </div>

    @if($menus->hasPages())
        <div class="pagination-wrapper mt-6">
            {{ $menus->links() }}
        </div>
    @endif

    @if(auth()->user()->isOwner())
        <!-- Modal Tambah Menu Baru -->
        <div x-show="createModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="createModal = false" 
                 class="bg-[#FFFDFA] rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-[#D8CDBD] animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between pb-3 border-b border-[#E8E1D6]">
                    <div>
                        <h3 class="font-serif font-bold text-lg text-[#24364A]">Tambah Menu Baru</h3>
                        <p class="text-xs text-[#70746F] mt-0.5">Daftarkan item menu minuman dan tentukan harga jualnya.</p>
                    </div>
                    <button type="button" @click="createModal = false" class="p-1 rounded-lg text-[#70746F] hover:text-[#24364A] hover:bg-[#E8E1D6]/40 transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#24364A] mb-1">Kode Menu *</label>
                            <input type="text" name="code" required placeholder="Contoh: MNU-BTR"
                                   class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs font-mono uppercase bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#24364A] mb-1">Kategori Menu *</label>
                            <select name="category_id" required class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785] focus:outline-none">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Nama Menu Minuman *</label>
                        <input type="text" name="name" required placeholder="Contoh: Butterscotch Sea Salt Latte"
                               class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Harga Jual (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-[#70746F] font-bold">Rp</span>
                            <input type="number" name="price" min="0" step="1000" placeholder="28000"
                                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-[#D8CDBD] text-xs font-bold bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785] focus:outline-none">
                        </div>
                    </div>

                    <!-- Upload Foto Menu -->
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Foto Menu (Opsional)</label>
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl border-2 border-dashed border-[#D8CDBD] bg-[#F4F1EB]/50 overflow-hidden flex items-center justify-center shrink-0">
                                <template x-if="createPhotoPreview">
                                    <img :src="createPhotoPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!createPhotoPreview">
                                    <i data-lucide="image-plus" class="w-6 h-6 text-[#70746F]/60"></i>
                                </template>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="photo" accept="image/*" @change="previewPhoto($event, 'create')"
                                       class="block w-full text-xs text-[#70746F] file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#305785] file:text-white hover:file:bg-[#274A70] file:cursor-pointer cursor-pointer">
                                <p class="text-[10px] text-[#70746F] mt-1">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Deskripsi Menu</label>
                        <textarea name="description" rows="2" placeholder="Kombinasi espresso, sirup butterscotch premium, dan susu creamy..."
                                  class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785] focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_active" id="create_is_active" value="1" checked
                               class="w-4 h-4 rounded text-[#305785] border-[#D8CDBD] focus:ring-[#305785]">
                        <label for="create_is_active" class="text-xs font-semibold text-[#24364A]">Status Aktif Dijual</label>
                    </div>

                    <div class="pt-4 border-t border-[#E8E1D6] flex justify-end gap-2">
                        <button type="button" @click="createModal = false" class="btn btn-secondary btn-sm">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan & Atur Resep &rarr;</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Menu -->
        <div x-show="editModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="editModal = false" 
                 class="bg-[#FFFDFA] rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-[#D8CDBD] animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between pb-3 border-b border-[#E8E1D6]">
                    <div>
                        <h3 class="font-serif font-bold text-lg text-[#24364A]">Edit Menu Minuman</h3>
                        <p class="text-xs text-[#70746F] mt-0.5" x-text="selectedMenu?.name"></p>
                    </div>
                    <button type="button" @click="editModal = false" class="p-1 rounded-lg text-[#70746F] hover:text-[#24364A] hover:bg-[#E8E1D6]/40 transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form :action="'/menus/' + selectedMenu?.id" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#24364A] mb-1">Kode Menu *</label>
                            <input type="text" name="code" required :value="selectedMenu?.code"
                                   class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs font-mono uppercase bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#24364A] mb-1">Kategori Menu *</label>
                            <select name="category_id" required class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785] focus:outline-none">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" :selected="selectedMenu?.category_id == {{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Nama Menu Minuman *</label>
                        <input type="text" name="name" required :value="selectedMenu?.name"
                               class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Harga Jual (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-[#70746F] font-bold">Rp</span>
                            <input type="number" name="price" min="0" step="1000" :value="selectedMenu?.price"
                                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-[#D8CDBD] text-xs font-bold bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785] focus:outline-none">
                        </div>
                    </div>

                    <!-- Upload / Ganti Foto Menu -->
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Foto Menu</label>
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl border border-[#D8CDBD] bg-[#F4F1EB]/50 overflow-hidden flex items-center justify-center shrink-0">
                                <template x-if="editPhotoPreview">
                                    <img :src="editPhotoPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!editPhotoPreview">
                                    <img :src="selectedMenu?.photo_url || '/images/kohlie-latte.jpg'" class="w-full h-full object-cover">
                                </template>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="photo" accept="image/*" @change="previewPhoto($event, 'edit')"
                                       class="block w-full text-xs text-[#70746F] file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#305785] file:text-white hover:file:bg-[#274A70] file:cursor-pointer cursor-pointer">
                                <p class="text-[10px] text-[#70746F] mt-1">Pilih gambar baru jika ingin mengganti foto saat ini (Maks 2MB).</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Deskripsi Menu</label>
                        <textarea name="description" rows="2" :value="selectedMenu?.description"
                                  class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785] focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="selectedMenu?.is_active"
                               class="w-4 h-4 rounded text-[#305785] border-[#D8CDBD] focus:ring-[#305785]">
                        <label for="edit_is_active" class="text-xs font-semibold text-[#24364A]">Status Aktif Dijual</label>
                    </div>

                    <div class="pt-4 border-t border-[#E8E1D6] flex justify-between items-center">
                        <button type="button" @click="editModal = false" class="btn btn-secondary btn-sm">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus Menu (Proporsional Maks 800px) -->
        <div x-show="deleteModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="deleteModal = false" 
                 class="bg-[#FFFDFA] rounded-[28px] p-6 sm:p-8 w-[calc(100%-2rem)] max-w-[800px] mx-auto max-h-[calc(100vh-2rem)] overflow-y-auto shadow-2xl border border-[#D8CDBD] animate-in fade-in zoom-in-95 duration-200"
                 style="width: calc(100% - 32px); max-width: 800px; margin: auto;">
                
                <!-- Badge Atas -->
                <div class="mb-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-extrabold tracking-wider uppercase bg-[#F8E8E5] text-[#A65043] border border-[#A65043]/20">
                        PERINGATAN
                    </span>
                </div>

                <!-- Judul Utama -->
                <h3 class="font-serif font-bold text-xl sm:text-2xl text-[#24364A] mb-2 leading-tight">
                    Konfirmasi Hapus Menu
                </h3>

                <!-- Deskripsi -->
                <p class="text-xs sm:text-sm text-[#70746F] leading-relaxed mb-5">
                    Apakah Anda yakin ingin menghapus menu ini? Tindakan ini akan menghapus data menu beserta master komposisi resep yang terhubung secara permanen.
                </p>

                <!-- Panel Informasi Menu -->
                <div class="bg-[#FAF8F5] rounded-2xl p-4 sm:p-5 border border-[#D8CDBD] space-y-3 mb-6 text-xs sm:text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-[#70746F] font-medium shrink-0">Nama Menu</span>
                        <strong class="text-[#24364A] font-bold text-right break-words" x-text="menuToDelete?.name"></strong>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-[#70746F] font-medium shrink-0">Kode Menu</span>
                        <span class="px-2.5 py-1 rounded-lg bg-[#305785]/10 text-[#305785] font-mono font-bold text-xs shrink-0" x-text="menuToDelete?.code"></span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-[#70746F] font-medium shrink-0">Kategori</span>
                        <span class="text-[#24364A] font-semibold text-right break-words" x-text="menuToDelete?.category?.name || 'Minuman'"></span>
                    </div>
                    <div class="flex items-center justify-between gap-4 pt-2.5 border-t border-[#D8CDBD]/70">
                        <span class="text-[#70746F] font-medium shrink-0">Status Data</span>
                        <span class="text-[#A65043] font-bold text-xs flex items-center gap-1.5 shrink-0">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            <span>Resep ikut terhapus</span>
                        </span>
                    </div>
                </div>

                <!-- Tombol Aksi Bawah -->
                <form :action="'/menus/' + menuToDelete?.id" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="pt-4 border-t border-[#E8E1D6] flex flex-col-reverse sm:flex-row items-center gap-3">
                        <button type="button" @click="deleteModal = false" 
                                class="w-full sm:flex-1 py-3 px-5 rounded-xl border border-[#D8CDBD] bg-white hover:bg-[#F4F1EB] text-xs sm:text-sm font-bold text-[#24364A] transition shadow-xs text-center cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="w-full sm:flex-1 py-3 px-5 rounded-xl bg-[#A65043] hover:bg-[#8A3F33] text-white text-xs sm:text-sm font-bold transition shadow-md shadow-[#A65043]/20 flex items-center justify-center gap-2 cursor-pointer">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            <span>Hapus Menu</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
