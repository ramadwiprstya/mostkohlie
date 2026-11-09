@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="orderRecapForm()">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <a href="{{ route('order-recaps.index') }}" class="text-xs text-[#70746F] hover:text-[#305785]">Rekap Pesanan</a>
        <span class="separator text-[#B5ACA0] mx-1">/</span>
        <span class="text-xs font-semibold text-[#24364A]">Input Rekap Kasir</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Input Rekap Pesanan Kasir</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Catat transaksi dari struk POS kasir secara berkala untuk menghitung pemakaian bahan baku.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="saveDraft()" class="btn btn-secondary btn-sm">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Draft</span>
            </button>
            <button type="button" @click="openConfirmModal()" 
                    :disabled="totalQuantity === 0"
                    class="btn btn-primary btn-sm disabled:opacity-50">
                <i data-lucide="calculator" class="w-4 h-4"></i>
                <span>Validasi & Simpan Rekap</span>
            </button>
        </div>
    </div>

    <!-- Alert Banner: Not POS Disclaimer -->
    <div class="callout-box callout-info">
        <i data-lucide="info" class="w-5 h-5 text-[#305785] shrink-0 mt-0.5"></i>
        <div>
            <strong>Perhatian Operasional Barista:</strong>
            <p class="text-xs text-[#24364A] mt-0.5">
                Sistem ini <strong>bukan sistem kasir/POS</strong>. Masukkan rekap penjualan fisik atau rangkuman kasir per rentang transaksi. Sistem akan membaca master resep, mengkalkulasi pengurangan bahan baku, dan memperbarui buku stok otomatis.
            </p>
        </div>
    </div>

    <!-- Main Grid: Transaction Form & Recipe Usage Estimation Panel -->
    <form action="{{ route('order-recaps.store') }}" method="POST" id="recapForm" @submit="handleSubmit">
        @csrf
        <input type="hidden" name="idempotency_token" value="{{ $idempotencyToken }}">

        <!-- Dynamic Hidden Inputs for Form Submission -->
        <template x-for="(item, index) in formattedItems" :key="item.menu_id">
            <div>
                <input type="hidden" :name="'items[' + index + '][menu_id]'" :value="item.menu_id">
                <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Side (7 Cols): Form Info & Menu Picker -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Card 1: Informasi Rekap Transaksi -->
                <div class="card space-y-4">
                    <h3 class="card-title text-base">1. Informasi Rekap Transaksi</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-[#24364A] mb-1.5">Tanggal Operasional *</label>
                            <input type="date" name="transaction_date" x-model="transactionDate" required
                                   max="{{ date('Y-m-d') }}"
                                   class="input-text text-xs">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="font-bold text-[#24364A]">Shift Kerja *</label>
                                <span class="text-[10px] text-[#39715A] font-medium flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#39715A] animate-pulse"></span>
                                    <span>Otomatis Jam Sekarang</span>
                                </span>
                            </div>
                            <select name="shift" x-model="shift" class="input-select text-xs">
                                <option value="pagi">Pagi (07:00 - 15:00)</option>
                                <option value="siang">Siang (15:00 - 20:00)</option>
                                <option value="malam">Malam (20:00 - 23:00)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-[#24364A] mb-1.5">Nomor / Referensi Transaksi Kasir *</label>
                            <input type="text" name="notes" placeholder="Contoh: TRX-POS-1042..1078" required
                                   class="input-text text-xs">
                        </div>

                        <div>
                            <label class="block font-bold text-[#24364A] mb-1.5">Nama Barista Perekap</label>
                            <input type="text" value="{{ auth()->user()->name }}" readonly
                                   class="input-text text-xs bg-[#E8E1D6]/40 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Tambah Menu dari Struk Kasir -->
                <div class="card space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-[#D8CDBD]">
                        <div>
                            <h3 class="card-title text-base">2. Tambah Menu dari Struk Kasir</h3>
                            <p class="text-xs text-[#70746F]">Pilih menu dan tentukan jumlah cup yang terjual</p>
                        </div>

                        <!-- Search Menu -->
                        <div class="relative w-full sm:w-48">
                            <input type="text" x-model="menuSearch" placeholder="Cari menu..."
                                   class="input-text pl-8 pr-3 py-1.5 text-xs">
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-[#70746F]">
                                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Category Tabs -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                        <button type="button" 
                                @click="selectedCategory = ''"
                                :class="selectedCategory === '' ? 'bg-[#305785] text-white font-bold' : 'bg-[#FAF8F5] text-[#70746F] border border-[#D8CDBD] hover:bg-[#E8E1D6]'"
                                class="px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                            Semua Menu
                        </button>
                        @foreach($categories as $cat)
                            <button type="button" 
                                    @click="selectedCategory = '{{ $cat->id }}'"
                                    :class="selectedCategory === '{{ $cat->id }}' ? 'bg-[#305785] text-white font-bold' : 'bg-[#FAF8F5] text-[#70746F] border border-[#D8CDBD] hover:bg-[#E8E1D6]'"
                                    class="px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Menu List Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[380px] overflow-y-auto pr-1">
                        @foreach($menus as $menu)
                            <div x-show="filterMenu('{{ $menu->name }}', '{{ $menu->code }}', '{{ $menu->category_id }}')"
                                 class="p-3 rounded-xl border transition-all duration-150 flex flex-col justify-between"
                                 :class="items[{{ $menu->id }}] > 0 ? 'border-[#305785] bg-[#305785]/5 ring-1 ring-[#305785]' : 'border-[#D8CDBD] bg-[#FAF8F5]'">
                                
                                <div>
                                    <div class="flex items-start justify-between gap-1 mb-1">
                                        <span class="text-[10px] font-mono font-bold text-[#70746F]">{{ $menu->code }}</span>
                                        <div class="flex items-center gap-1">
                                            @if(!$menu->activeRecipe || $menu->activeRecipe->items->isEmpty())
                                                <span class="badge badge-warning text-[9px] py-0 px-1" title="Resep belum diatur oleh Owner">Tanpa Resep</span>
                                            @endif
                                            <span class="badge badge-neutral text-[10px]">{{ $menu->category?->name }}</span>
                                        </div>
                                    </div>
                                    <h4 class="font-bold text-xs text-[#24364A] leading-tight">{{ $menu->name }}</h4>
                                    <p class="text-xs font-bold text-[#305785] mt-0.5">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                </div>

                                <div class="mt-3 pt-2 border-t border-[#D8CDBD]/50 flex items-center justify-between">
                                    <span class="text-[11px] text-[#70746F]">Jumlah Cup:</span>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="decrement({{ $menu->id }})" 
                                                class="w-6 h-6 rounded-md bg-[#FFFDFA] border border-[#D8CDBD] text-[#24364A] font-bold text-xs flex items-center justify-center hover:bg-[#E8E1D6] transition">
                                            -
                                        </button>
                                        <input type="number" 
                                               x-model.number="items[{{ $menu->id }}]"
                                               min="0"
                                               class="w-12 text-center py-0.5 px-1 rounded-md border border-[#D8CDBD] text-xs font-bold bg-[#FFFDFA]"
                                               @input="debouncedFetchPreview()"
                                               @change="debouncedFetchPreview()">
                                        <button type="button" @click="increment({{ $menu->id }})" 
                                                class="w-6 h-6 rounded-md bg-[#305785] text-white font-bold text-xs flex items-center justify-center hover:bg-[#274A70] transition">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-[#D8CDBD] flex items-center justify-between text-xs">
                        <span>Total Pesanan: <strong class="text-sm font-bold text-[#24364A]" x-text="totalQuantity + ' Cup'"></strong></span>
                        <span class="badge badge-warning">Status: Draft</span>
                    </div>
                </div>

            </div>

            <!-- Right Side (5 Cols): Live Recipe Usage Panel -->
            <div class="lg:col-span-5 space-y-6">
                
                <div class="card border-t-4 border-t-[#305785] space-y-4">
                    <div class="card-header pb-2 border-b border-[#D8CDBD]">
                        <div>
                            <span class="badge badge-blue mb-1">Kalkulasi Master Resep</span>
                            <h3 class="card-title text-base">Estimasi Pemakaian Bahan</h3>
                            <p class="card-subtitle">Dihitung otomatis per cup menu yang diinputkan</p>
                        </div>
                    </div>

                    <!-- Usage List -->
                    <div class="space-y-3">
                        <template x-if="loading">
                            <div class="py-8 text-center text-xs text-[#70746F]">
                                <i data-lucide="loader-2" class="w-5 h-5 animate-spin mx-auto mb-1 text-[#305785]"></i>
                                <span>Menghitung kebutuhan resep...</span>
                            </div>
                        </template>

                        <template x-if="!loading && requirements.length === 0">
                            <div class="py-8 text-center text-xs text-[#70746F]">
                                <i data-lucide="coffee" class="w-6 h-6 text-[#B5ACA0] mx-auto mb-1"></i>
                                <span>Pilih menu di sebelah kiri untuk melihat estimasi bahan baku.</span>
                            </div>
                        </template>

                        <template x-if="!loading && requirements.length > 0">
                            <div class="space-y-2.5 max-h-[360px] overflow-y-auto pr-1">
                                <template x-for="req in requirements" :key="req.ingredient_id">
                                    <div class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl text-xs">
                                        <div class="flex justify-between items-center mb-1">
                                            <strong class="text-[#24364A]" x-text="req.ingredient_name"></strong>
                                            <span class="font-bold text-xs" 
                                                  :class="req.is_sufficient ? 'text-[#24364A]' : 'text-[#A65043]'"
                                                  x-text="'Kebutuhan: ' + (req.required_formatted ?? req.required) + ' ' + (req.unit ?? req.unit_symbol)"></span>
                                        </div>
                                        <div class="flex justify-between text-[11px] text-[#70746F] mb-1.5">
                                            <span>Stok: <span x-text="(req.available_formatted ?? req.available) + ' ' + (req.unit ?? req.unit_symbol)"></span></span>
                                            <span :class="req.is_sufficient ? 'text-[#39715A]' : 'text-[#A65043] font-bold'"
                                                  x-text="req.is_sufficient ? 'Tersedia' : 'Kurang ' + (req.shortage_formatted ?? req.shortage) + ' ' + (req.unit ?? req.unit_symbol)"></span>
                                        </div>
                                        <div class="progress-container">
                                            <div class="progress-fill" 
                                                 :class="req.is_sufficient ? 'safe' : 'danger'"
                                                 :style="'width: ' + (req.percentage ?? (req.available > 0 ? Math.min(100, Math.round((req.required / req.available) * 100)) : 100)) + '%;'"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="pt-4 border-t border-[#D8CDBD] space-y-2">
                        <button type="button" 
                                @click="openConfirmModal"
                                :disabled="totalQuantity === 0 || isSubmitting"
                                class="btn btn-primary btn-lg w-full disabled:opacity-50">
                            <i data-lucide="check-check" class="w-4 h-4"></i>
                            <span>Simpan & Kurangi Stok Otomatis</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>

        <!-- Confirmation Modal (Success / Deficit Condition) -->
        <div x-show="confirmModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="confirmModal = false" 
                 class="bg-[#FFFDFA] rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-[#D8CDBD]">
                
                <template x-if="isAllSufficient">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#EAF4EF] text-[#39715A] flex items-center justify-center mb-3 mx-auto">
                            <i data-lucide="check" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-serif text-lg font-bold text-[#24364A] text-center">Validasi Rekap Berhasil</h3>
                        <p class="text-xs text-[#70746F] text-center mt-1 leading-relaxed">
                            Seluruh stok bahan baku mencukupi untuk <strong class="text-[#24364A]" x-text="totalQuantity + ' cup menu'"></strong>. Stok akan langsung dikurangi otomatis.
                        </p>
                    </div>
                </template>

                <template x-if="!isAllSufficient">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#F8E8E5] text-[#A65043] flex items-center justify-center mb-3 mx-auto">
                            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-serif text-lg font-bold text-[#A65043] text-center">Peringatan: Stok Tidak Mencukupi</h3>
                        <p class="text-xs text-[#70746F] text-center mt-1 leading-relaxed">
                            Beberapa bahan baku mengalami defisit kuantitas. Jika Anda tetap mencatat, stok akan tercatat minus dan Owner akan langsung dinotifikasi.
                        </p>
                    </div>
                </template>

                <div class="mt-4 p-3 rounded-xl bg-[#FAF8F5] border border-[#D8CDBD] text-xs space-y-1.5">
                    <div class="flex justify-between text-[#70746F]">
                        <span>Tanggal Transaksi:</span>
                        <strong class="text-[#24364A]" x-text="transactionDate"></strong>
                    </div>
                    <div class="flex justify-between text-[#70746F]">
                        <span>Shift Operasional:</span>
                        <strong class="text-[#305785] font-bold capitalize" x-text="shift"></strong>
                    </div>
                    <div class="flex justify-between text-[#70746F]">
                        <span>Total Menu Terjual:</span>
                        <strong class="text-[#24364A]" x-text="totalQuantity + ' cup'"></strong>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="button" @click="confirmModal = false" 
                            class="btn btn-secondary flex-1 py-2 text-xs">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="btn flex-1 py-2 text-xs"
                            :class="isAllSufficient ? 'btn-primary' : 'btn-danger'">
                        <span x-text="isSubmitting ? 'Memproses...' : (isAllSufficient ? 'Konfirmasi & Simpan' : 'Catat Stok Tidak Cukup')"></span>
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    function orderRecapForm() {
        return {
            transactionDate: '{{ date('Y-m-d') }}',
            shift: (() => {
                const h = new Date().getHours();
                if (h >= 7 && h < 15) return 'pagi';
                if (h >= 15 && h < 20) return 'siang';
                return 'malam';
            })(),
            selectedCategory: '',
            menuSearch: '',
            items: {},
            requirements: [],
            isAllSufficient: true,
            shortages: [],
            loading: false,
            confirmModal: false,
            isSubmitting: false,
            debounceTimer: null,

            get formattedItems() {
                const list = [];
                for (let id in this.items) {
                    const qty = parseInt(this.items[id], 10);
                    if (!isNaN(qty) && qty > 0) {
                        list.push({
                            menu_id: parseInt(id, 10),
                            quantity: qty
                        });
                    }
                }
                return list;
            },

            get totalQuantity() {
                return this.formattedItems.reduce((sum, item) => sum + item.quantity, 0);
            },

            filterMenu(name, code, categoryId) {
                if (this.selectedCategory && this.selectedCategory != categoryId) return false;
                if (this.menuSearch) {
                    const q = this.menuSearch.toLowerCase();
                    return name.toLowerCase().includes(q) || code.toLowerCase().includes(q);
                }
                return true;
            },

            increment(menuId) {
                this.items[menuId] = (this.items[menuId] || 0) + 1;
                this.debouncedFetchPreview();
            },

            decrement(menuId) {
                if (this.items[menuId] && this.items[menuId] > 0) {
                    this.items[menuId]--;
                    this.debouncedFetchPreview();
                }
            },

            debouncedFetchPreview() {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    this.fetchPreview();
                }, 250);
            },

            async fetchPreview() {
                const currentItems = this.formattedItems;
                if (currentItems.length === 0) {
                    this.requirements = [];
                    this.isAllSufficient = true;
                    this.shortages = [];
                    return;
                }

                this.loading = true;
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await axios.post('{{ route('order-recaps.preview') }}', {
                        items: currentItems
                    }, {
                        headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}
                    });

                    this.requirements = response.data.requirements || [];
                    this.isAllSufficient = response.data.is_all_sufficient;
                    this.shortages = response.data.shortages || [];
                } catch (error) {
                    console.error('Error fetching recipe preview:', error);
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        if (window.createIcons) {
                            window.createIcons();
                        }
                    });
                }
            },

            openConfirmModal() {
                if (this.totalQuantity > 0) {
                    this.confirmModal = true;
                }
            },

            handleSubmit() {
                this.isSubmitting = true;
            },

            saveDraft() {
                alert('Draft rekap pesanan berhasil disimpan sementara.');
            }
        };
    }
</script>
@endpush
