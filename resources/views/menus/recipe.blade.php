@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="recipeEditor()">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <a href="{{ route('menus.index') }}" class="text-xs text-[#70746F] hover:text-[#305785]">Daftar Menu</a>
        <span class="separator text-[#B5ACA0] mx-1">/</span>
        <span class="text-xs font-semibold text-[#24364A]">Resep: {{ $menu->name }}</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Komposisi Resep: {{ $menu->name }}</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Master takaran bahan baku per serving cup untuk perhitungan otomatis pemotongan stok.</p>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
            @if(isset($allMenus) && $allMenus->isNotEmpty())
                <!-- Search Bar untuk Pilih Menu -->
                <div class="relative w-full sm:w-72 md:w-80 lg:w-96" 
                     x-data="{
                         search: '',
                         open: false,
                         currentId: {{ $menu->id }},
                         menus: {{ json_encode($allMenus->map(fn($m) => [
                             'id' => $m->id,
                             'name' => $m->name,
                             'code' => $m->code,
                             'category' => $m->category?->name ?? 'Minuman',
                             'photo_url' => $m->photo_url,
                             'url' => route('menus.recipe', $m)
                         ])) }},
                         get filtered() {
                             if (!this.search.trim()) return this.menus;
                             const q = this.search.toLowerCase().trim();
                             return this.menus.filter(m => 
                                 m.name.toLowerCase().includes(q) || 
                                 m.code.toLowerCase().includes(q) ||
                                 m.category.toLowerCase().includes(q)
                             );
                         },
                         selectMenu(item) {
                             if (item.id === this.currentId) {
                                 this.open = false;
                                 return;
                             }
                             window.location.href = item.url;
                         },
                         selectFirst() {
                             if (this.filtered.length > 0) {
                                 this.selectMenu(this.filtered[0]);
                             }
                         }
                     }"
                     @click.outside="open = false"
                     x-init="$watch('open', val => { if(val && window.lucide) $nextTick(() => lucide.createIcons()); })">
                    
                    <div class="relative flex items-center">
                        <!-- Ikon Kaca Pembesar di Dalam Input (Sejajar Vertikal di Kiri) -->
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#70746F]">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </span>

                        <!-- Input Pencarian Menu (Tinggi 44px, Rounded-Full, Padding Kiri 46px Bebas Tumpang Tindih) -->
                        <input type="text"
                               x-ref="searchInput"
                               x-model="search"
                               @focus="open = true"
                               @click="open = true"
                               @keydown.escape="open = false"
                               @keydown.enter.prevent="selectFirst()"
                               placeholder="Cari & pilih menu (nama/kode)..."
                               class="w-full rounded-full border border-[#D8CDBD] bg-white focus:bg-[#FFFDFA] focus:border-[#305785] focus:ring-2 focus:ring-[#305785]/15 text-xs sm:text-sm font-medium text-[#24364A] placeholder-[#8C8F8A] transition shadow-xs outline-none"
                               style="height: 44px; padding-left: 46px; padding-right: 40px;">
                        
                        <!-- Tombol Clear (Hanya muncul jika ada teks, tanpa panah atau spinner di kanan) -->
                        <button type="button" 
                                x-show="search.length > 0" 
                                @click="search = ''; $nextTick(() => $refs.searchInput.focus())"
                                class="absolute right-3.5 inset-y-0 my-auto h-7 w-7 flex items-center justify-center text-[#70746F] hover:text-[#24364A] rounded-full hover:bg-[#FAF8F5] transition cursor-pointer"
                                title="Hapus teks pencarian">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    <!-- Dropdown List Hasil Pencarian -->
                    <div x-show="open" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute right-0 w-full min-w-[300px] max-w-sm mt-2 bg-[#FFFDFA] border border-[#D8CDBD] rounded-2xl shadow-xl z-50 overflow-hidden divide-y divide-[#E8E1D6]">
                        
                        <!-- Header Info Dropdown -->
                        <div class="px-3.5 py-2 bg-[#FAF8F5] flex items-center justify-between text-[11px] text-[#70746F]">
                            <span>Daftar Menu (<strong class="text-[#24364A]" x-text="filtered.length"></strong>)</span>
                            <span class="text-[10px] text-[#8C8F8A]">Pilih untuk ganti resep</span>
                        </div>

                        <!-- Scrollable Menu Items -->
                        <div class="max-h-72 overflow-y-auto p-1.5 space-y-1">
                            <template x-for="item in filtered" :key="item.id">
                                <button type="button" 
                                        @click="selectMenu(item)"
                                        class="w-full flex items-center gap-3 p-2 rounded-xl text-left transition cursor-pointer"
                                        :class="item.id === currentId 
                                            ? 'bg-[#FAF0DD] border border-[#A16A28]/30 font-semibold' 
                                            : 'hover:bg-[#F4F1EB] text-[#24364A]'">
                                    <img :src="item.photo_url" :alt="item.name" class="w-9 h-9 rounded-lg object-cover border border-[#D8CDBD] shrink-0 bg-white">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-bold text-[#24364A] truncate" x-text="item.name"></span>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-[#305785]/10 text-[#305785] shrink-0" x-text="item.code"></span>
                                        </div>
                                        <div class="text-[11px] text-[#70746F] truncate" x-text="item.category"></div>
                                    </div>
                                    <template x-if="item.id === currentId">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#A16A28] text-white shrink-0">
                                            Aktif
                                        </span>
                                    </template>
                                </button>
                            </template>

                            <!-- Empty State -->
                            <div x-show="filtered.length === 0" class="py-6 px-4 text-center text-xs text-[#70746F]">
                                <i data-lucide="coffee" class="w-6 h-6 text-[#B5ACA0] mx-auto mb-1.5"></i>
                                <p class="font-bold text-[#24364A]">Menu tidak ditemukan</p>
                                <p class="text-[11px] text-[#70746F] mt-0.5">Coba gunakan nama atau kode menu lain.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->isOwner())
                <span class="badge badge-safe shrink-0">Mode Edit Owner</span>
            @else
                <span class="badge badge-neutral shrink-0">
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    <span>Barista: Akses Baca (Read-Only)</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Top Card: Visual & Capacity Calculation -->
    <div class="card p-6">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <img src="{{ $menu->photo_url }}" alt="{{ $menu->name }}" class="w-32 h-32 rounded-2xl object-cover border border-[#D8CDBD] shrink-0">
            <div class="flex-1 text-center sm:text-left">
                <div class="flex items-center justify-center sm:justify-start gap-2 mb-2 flex-wrap">
                    <span class="badge badge-safe">Resep Aktif v{{ $menu->activeRecipe?->version ?? 1 }}</span>
                    <span class="badge badge-blue">{{ $menu->category?->name ?? 'Minuman' }}</span>
                </div>
                <h2 class="font-serif text-2xl font-bold text-[#24364A] mb-1">{{ $menu->name }}</h2>
                <p class="text-xs text-[#70746F] max-w-xl leading-relaxed mb-3">
                    {{ $menu->description ?? 'Racikan kopi spesial dengan kualitas rasa terstandarisasi master resep.' }}
                </p>
                <div class="text-sm font-bold text-[#305785]">
                    Harga Jual: Rp {{ number_format($menu->price, 0, ',', '.') }}
                </div>
            </div>

            <!-- KPI: Estimated Remaining Cups Capacity -->
            <div class="bg-[#FAF0DD] border border-[#A16A28]/30 rounded-2xl p-5 text-center min-w-[200px] shrink-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A16A28] block">Estimasi Kapasitas</span>
                <div class="font-serif text-3xl font-bold text-[#24364A] my-1">
                    16 Cup
                </div>
                <div class="text-[11px] font-semibold text-[#A65043]">
                    Pembatas: Susu UHT Plain
                </div>
            </div>
        </div>
    </div>

    <!-- Recipe Table & Editor Form -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Tabel Takaran Bahan Per Serving (1 Cup)</h3>
                <p class="card-subtitle">Takaran baku yang dipakai sistem saat barista merekap penjualan</p>
            </div>
            @if(auth()->user()->isOwner())
                <button type="button" @click="addRow()" class="btn btn-primary btn-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Bahan Resep</span>
                </button>
            @endif
        </div>

        @if(auth()->user()->isOwner())
            <form action="{{ route('menus.update-recipe', $menu) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-3">
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="p-4 rounded-xl bg-[#FAF8F5] border border-[#D8CDBD] flex flex-col sm:flex-row items-center gap-3">
                            <div class="flex-1 w-full">
                                <label class="block text-[11px] font-bold text-[#70746F] mb-1">Bahan Baku *</label>
                                <select :name="'items[' + index + '][ingredient_id]'" 
                                        x-model="row.ingredient_id" 
                                        @change="updateRowUnit(row)"
                                        required
                                        class="input-select text-xs font-semibold bg-white">
                                    <option value="">-- Pilih Bahan Baku --</option>
                                    @foreach($ingredients as $ing)
                                        <option value="{{ $ing->id }}" data-symbol="{{ $ing->unit?->symbol }}">
                                            {{ $ing->name }} ({{ $ing->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-full sm:w-44">
                                <label class="block text-[11px] font-bold text-[#70746F] mb-1">Takaran / Cup *</label>
                                <div class="relative">
                                    <input type="number" step="any" min="0.01" 
                                           :name="'items[' + index + '][quantity]'" 
                                           x-model="row.quantity" 
                                           required placeholder="150"
                                           class="input-text text-xs font-bold pr-12 bg-white">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-[#70746F]" x-text="row.unit_symbol || 'satuan'"></span>
                                </div>
                            </div>

                            <div class="self-end sm:self-center pt-2 sm:pt-4">
                                <button type="button" 
                                        @click="removeRow(index)"
                                        :disabled="rows.length <= 1"
                                        class="p-2 rounded-lg border border-[#A65043]/30 text-[#A65043] hover:bg-[#F8E8E5] transition disabled:opacity-30">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-4 border-t border-[#D8CDBD] flex justify-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Perubahan Resep</span>
                    </button>
                </div>
            </form>
        @else
            <!-- Read-Only View for Barista -->
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Bahan Baku</th>
                            <th>Takaran Per Cup</th>
                            <th>Stok Tersedia</th>
                            <th>Status Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menu->activeRecipe?->items ?? [] as $item)
                            <tr>
                                <td><strong>{{ $item->ingredient->name }}</strong></td>
                                <td>{{ number_format($item->quantity, 0) }} {{ $item->unit?->symbol }}</td>
                                <td>{{ number_format($item->ingredient->current_stock, 0) }} {{ $item->ingredient->unit?->symbol }}</td>
                                <td>
                                    @if($item->ingredient->current_stock <= 0)
                                        <span class="badge badge-danger">Habis</span>
                                    @elseif($item->ingredient->current_stock <= $item->ingredient->min_stock)
                                        <span class="badge badge-warning">Menipis</span>
                                    @else
                                        <span class="badge badge-safe">Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-xs text-[#70746F]">Belum ada komposisi bahan dalam resep ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function recipeEditor() {
        return {
            rows: [
                @if($menu->activeRecipe && $menu->activeRecipe->items->isNotEmpty())
                    @foreach($menu->activeRecipe->items as $item)
                        {
                            ingredient_id: '{{ $item->ingredient_id }}',
                            quantity: '{{ $item->quantity }}',
                            unit_symbol: '{{ $item->unit?->symbol }}'
                        },
                    @endforeach
                @else
                    { ingredient_id: '', quantity: '', unit_symbol: '' }
                @endif
            ],

            addRow() {
                this.rows.push({ ingredient_id: '', quantity: '', unit_symbol: '' });
                this.$nextTick(() => {
                    if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
                });
            },

            removeRow(index) {
                if (this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
            },

            updateRowUnit(row) {
                const select = document.querySelector(`select[name="items[${this.rows.indexOf(row)}][ingredient_id]"]`);
                if (select) {
                    const option = select.options[select.selectedIndex];
                    row.unit_symbol = option ? option.getAttribute('data-symbol') : '';
                }
            }
        };
    }
</script>
@endpush
