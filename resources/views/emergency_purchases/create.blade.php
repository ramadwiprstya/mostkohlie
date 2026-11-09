@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="emergencyPurchaseForm()">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-[#70746F]">
        <a href="{{ route('emergency-purchases.index') }}" class="hover:text-[#305785] transition">Pengadaan</a>
        <span class="text-[#B5ACA0]">></span>
        <span class="font-semibold text-[#24364A]">Pembelian Darurat</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Pembelian Darurat</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Catat pembelian mendesak di luar pengadaan rutin.</p>
        </div>

        @if(auth()->user()->isOwner())
            <div class="flex items-center gap-2">
                <a href="{{ route('emergency-purchases.index') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    <span>Lihat Rekap Pembelian</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="callout-box callout-success">
            <i data-lucide="check-circle" class="w-5 h-5 text-[#39715A] shrink-0 mt-0.5"></i>
            <div>
                <strong>Berhasil Tersimpan!</strong>
                <p class="text-xs text-[#24364A] mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="callout-box callout-danger">
            <i data-lucide="alert-circle" class="w-5 h-5 text-[#A65043] shrink-0 mt-0.5"></i>
            <div>
                <strong>Terjadi Kesalahan!</strong>
                <p class="text-xs text-[#24364A] mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="callout-box callout-danger">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-[#A65043] shrink-0 mt-0.5"></i>
            <div>
                <strong>Mohon periksa kembali input Anda:</strong>
                <ul class="list-disc list-inside text-xs text-[#24364A] mt-1 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Card: Informasi Pembelian -->
    <div class="card p-6 sm:p-8 bg-[#FFFDFA] border border-[#D8CDBD] rounded-2xl shadow-xs">
        
        <!-- Card Header inside Container -->
        <div class="pb-4 border-b border-[#D8CDBD]/70">
            <h2 class="font-serif text-lg font-bold text-[#24364A]">Informasi Pembelian</h2>
            <p class="text-xs text-[#70746F] mt-0.5">Pastikan seluruh data sudah sesuai sebelum disimpan.</p>
        </div>

        <form action="{{ route('emergency-purchases.store') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="mt-6 space-y-6"
              @submit="handleSubmit">
            @csrf
            <input type="hidden" name="client_time" x-ref="clientTime">

            <!-- Row 1: Nama Bahan & Jumlah -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                <!-- Nama Bahan -->
                <div>
                    <label class="block text-xs font-semibold text-[#24364A] mb-2">
                        Nama Bahan <span class="text-[#A65043]">*</span>
                    </label>
                    <div class="relative">
                        <select name="ingredient_id" 
                                x-model="selectedIngredientId" 
                                @change="onIngredientChange()"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition appearance-none pr-10">
                            <option value="">Pilih bahan</option>
                            @foreach($ingredients as $ing)
                                <option value="{{ $ing->id }}" 
                                        data-unit="{{ $ing->unit?->symbol ?? '' }}"
                                        data-unit-name="{{ $ing->unit?->name ?? '' }}"
                                        data-stock="{{ $ing->current_stock }}"
                                        {{ old('ingredient_id') == $ing->id ? 'selected' : '' }}>
                                    {{ $ing->name }} ({{ $ing->unit?->symbol ?? 'unit' }}) — Sisa Stok: {{ number_format($ing->current_stock, 0, ',', '.') }} {{ $ing->unit?->symbol }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#70746F]">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <!-- Jumlah -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-semibold text-[#24364A]">
                            Jumlah <span class="text-[#A65043]">*</span>
                        </label>
                        <span x-show="selectedUnit" 
                              class="text-[11px] font-bold text-[#305785] bg-[#305785]/10 px-2 py-0.5 rounded-full" 
                              x-text="'Satuan: ' + selectedUnit"></span>
                    </div>
                    <div class="relative">
                        <input type="number" 
                               name="quantity" 
                               x-model="quantity" 
                               step="any"
                               min="0.01"
                               required
                               placeholder="Masukkan jumlah"
                               value="{{ old('quantity') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition">
                        <span x-show="selectedUnit" 
                              class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-[#70746F] pointer-events-none" 
                              x-text="selectedUnit"></span>
                    </div>
                </div>
            </div>

            <!-- Row 2: Harga & Tempat Pembelian -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                <!-- Harga -->
                <div>
                    <label class="block text-xs font-semibold text-[#24364A] mb-2">
                        Harga <span class="text-[#A65043]">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               x-model="formattedPrice" 
                               @input="handlePriceInput"
                               placeholder="Rp 0" 
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition">
                        <input type="hidden" name="amount" :value="rawAmount">
                    </div>
                </div>

                <!-- Tempat Pembelian -->
                <div>
                    <label class="block text-xs font-semibold text-[#24364A] mb-2">
                        Tempat Pembelian <span class="text-[#A65043]">*</span>
                    </label>
                    <input type="text" 
                           name="purchase_location" 
                           value="{{ old('purchase_location') }}"
                           required
                           placeholder="Nama toko"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition">
                </div>
            </div>

            <!-- Row 3: Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-semibold text-[#24364A] mb-2">
                        Tanggal <span class="text-[#A65043]">*</span>
                    </label>
                    <div class="relative">
                        <input type="date" 
                               name="purchase_date" 
                               x-model="purchaseDate"
                               max="{{ date('Y-m-d') }}"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition">
                    </div>
                </div>
            </div>

            <!-- Row 4: Keterangan -->
            <div>
                <label class="block text-xs font-semibold text-[#24364A] mb-2">
                    Keterangan
                </label>
                <textarea name="notes" 
                          rows="3" 
                          placeholder="Alasan pembelian darurat"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition leading-relaxed">{{ old('notes') }}</textarea>
            </div>

            <!-- Row 5: Bukti Pembelian (Drag-and-Drop Area) -->
            <div>
                <label class="block text-xs font-semibold text-[#24364A] mb-2">
                    Bukti Pembelian
                </label>
                
                <!-- Dropzone Box -->
                <div @click="$refs.fileInput.click()"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="handleDrop($event)"
                     :class="isDragging ? 'border-[#305785] bg-[#305785]/5 ring-2 ring-[#305785]/20' : 'border-[#D8CDBD] bg-[#FAF8F5]/50 hover:bg-[#FAF8F5]'"
                     class="border-2 border-dashed rounded-2xl p-7 text-center transition cursor-pointer flex flex-col items-center justify-center relative">
                    
                    <input type="file" 
                           x-ref="fileInput" 
                           name="receipt_proof" 
                           @change="handleFileSelect($event)" 
                           accept=".jpg,.jpeg,.png,.pdf" 
                           class="hidden">

                    <template x-if="!fileInfo">
                        <div class="space-y-1.5 pointer-events-none">
                            <div class="w-10 h-10 rounded-full bg-[#E8E1D6]/60 text-[#305785] flex items-center justify-center mx-auto mb-1">
                                <i data-lucide="plus" class="w-5 h-5"></i>
                            </div>
                            <p class="text-xs sm:text-sm font-semibold text-[#24364A]">Unggah foto atau PDF</p>
                            <p class="text-[11px] text-[#70746F]">JPG, PNG, atau PDF - Maks. 5 MB</p>
                        </div>
                    </template>

                    <!-- Preview if file selected -->
                    <template x-if="fileInfo">
                        <div class="flex items-center gap-4 w-full max-w-md p-3 bg-[#FFFDFA] rounded-xl border border-[#D8CDBD] shadow-xs text-left" @click.stop>
                            <!-- Thumbnail / Icon -->
                            <template x-if="filePreviewUrl">
                                <img :src="filePreviewUrl" alt="Preview Bukti" class="w-12 h-12 object-cover rounded-lg border border-[#D8CDBD]">
                            </template>
                            <template x-if="!filePreviewUrl">
                                <div class="w-12 h-12 rounded-lg bg-[#EAF4EF] text-[#39715A] flex items-center justify-center">
                                    <i data-lucide="file-text" class="w-6 h-6"></i>
                                </div>
                            </template>

                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-[#24364A] truncate" x-text="fileInfo.name"></p>
                                <p class="text-[11px] text-[#70746F]" x-text="fileInfo.sizeFormatted"></p>
                            </div>

                            <button type="button" 
                                    @click="removeFile()" 
                                    class="p-1.5 text-[#70746F] hover:text-[#A65043] rounded-lg transition" 
                                    title="Hapus file">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Action Buttons (Bottom Right) -->
            <div class="pt-4 border-t border-[#D8CDBD]/70 flex items-center justify-end gap-3">
                <a href="{{ auth()->user()->isOwner() ? route('emergency-purchases.index') : route('dashboard') }}" 
                   class="btn btn-secondary px-6 py-2.5 text-xs font-semibold">
                    Batal
                </a>
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="btn btn-primary px-6 py-2.5 text-xs font-semibold flex items-center gap-2 shadow-sm disabled:opacity-50">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Pembelian'"></span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function emergencyPurchaseForm() {
        return {
            selectedIngredientId: '{{ old('ingredient_id', '') }}',
            selectedUnit: '',
            quantity: '{{ old('quantity', '') }}',
            formattedPrice: '{{ old('amount') ? 'Rp ' . number_format((float)old('amount'), 0, ',', '.') : '' }}',
            rawAmount: {{ old('amount', 0) }},
            purchaseDate: '{{ old('purchase_date', $today) }}',
            isDragging: false,
            fileInfo: null,
            filePreviewUrl: null,
            isSubmitting: false,

            init() {
                this.updateUnit();
                this.$nextTick(() => {
                    if (window.createIcons) window.createIcons();
                });
            },

            onIngredientChange() {
                this.updateUnit();
            },

            updateUnit() {
                const selectEl = document.querySelector('select[name="ingredient_id"]');
                if (selectEl && selectEl.selectedIndex > 0) {
                    const opt = selectEl.options[selectEl.selectedIndex];
                    this.selectedUnit = opt.getAttribute('data-unit') || '';
                } else {
                    this.selectedUnit = '';
                }
            },

            handlePriceInput(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value === '') {
                    this.rawAmount = 0;
                    this.formattedPrice = '';
                    return;
                }
                const num = parseInt(value, 10);
                this.rawAmount = num;
                this.formattedPrice = 'Rp ' + num.toLocaleString('id-ID');
            },

            handleFileSelect(e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    this.processFile(files[0]);
                }
            },

            handleDrop(e) {
                this.isDragging = false;
                const files = e.dataTransfer.files;
                if (files && files.length > 0) {
                    this.$refs.fileInput.files = files;
                    this.processFile(files[0]);
                }
            },

            processFile(file) {
                const sizeInKb = (file.size / 1024).toFixed(1);
                const sizeFormatted = sizeInKb > 1024 ? (sizeInKb / 1024).toFixed(2) + ' MB' : sizeInKb + ' KB';

                this.fileInfo = {
                    name: file.name,
                    sizeFormatted: sizeFormatted,
                    type: file.type
                };

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.filePreviewUrl = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.filePreviewUrl = null;
                }

                this.$nextTick(() => {
                    if (window.createIcons) window.createIcons();
                });
            },

            removeFile() {
                this.fileInfo = null;
                this.filePreviewUrl = null;
                this.$refs.fileInput.value = '';
                this.$nextTick(() => {
                    if (window.createIcons) window.createIcons();
                });
            },

            handleSubmit() {
                this.isSubmitting = true;
                const now = new Date();
                const hh = String(now.getHours()).padStart(2, '0');
                const mm = String(now.getMinutes()).padStart(2, '0');
                const ss = String(now.getSeconds()).padStart(2, '0');
                if (this.$refs.clientTime) {
                    this.$refs.clientTime.value = `${hh}:${mm}:${ss}`;
                }
            }
        };
    }
</script>
@endpush

