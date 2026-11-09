@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="procurementRequestForm()">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-[#70746F]">
        <a href="{{ route('procurement-requests.index') }}" class="hover:text-[#305785] transition">Request Pengadaan</a>
        <span class="text-[#B5ACA0]">></span>
        <span class="font-semibold text-[#24364A]">Buat Request Pengadaan</span>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Buat Request Pengadaan</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Ajukan kebutuhan bahan baku untuk mendapat persetujuan owner.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-[#70746F] bg-[#FAF8F5] border border-[#D8CDBD] px-3 py-1.5 rounded-full shadow-2xs">
                <i data-lucide="file-plus" class="w-3.5 h-3.5 text-[#305785]"></i>
                <span>Draft baru</span>
            </span>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="callout-box callout-success">
            <i data-lucide="check-circle" class="w-5 h-5 text-[#39715A] shrink-0 mt-0.5"></i>
            <div>
                <strong>Berhasil!</strong>
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

    <!-- Main Layout: 2 Columns (Form + Dark Blue Summary Card) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Form Card -->
        <div class="lg:col-span-8">
            <div class="card p-6 sm:p-8 bg-[#FFFDFA] border border-[#D8CDBD] rounded-2xl shadow-xs">
                
                <!-- Card Header -->
                <div class="pb-4 border-b border-[#D8CDBD]/70">
                    <h2 class="font-serif text-lg font-bold text-[#24364A]">Informasi Kebutuhan</h2>
                    <p class="text-xs text-[#70746F] mt-0.5">Lengkapi bahan dan jumlah yang diperlukan untuk operasional.</p>
                </div>

                <!-- Stock Condition Highlight Banner (Matches Image 2) -->
                <div class="mt-5 p-4 sm:p-5 rounded-2xl border transition-all duration-200 flex items-center justify-between"
                     :class="stockStatus === 'habis' ? 'bg-[#FCEDEB] border-[#F2C0B8]' : (stockStatus === 'menipis' ? 'bg-[#FAF3E8] border-[#ECDCC7]' : 'bg-[#FAF8F5] border-[#D8CDBD]')">
                    
                    <div class="flex items-center gap-4">
                        <!-- Initials Box -->
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center font-bold text-sm tracking-wider shrink-0 transition"
                             :class="stockStatus === 'habis' ? 'bg-[#F2C0B8] text-[#A65043]' : (stockStatus === 'menipis' ? 'bg-[#ECDCC7] text-[#A16A28]' : 'bg-[#E8E1D6] text-[#70746F]')"
                             x-text="initials">
                        </div>

                        <div>
                            <span class="block text-[10px] font-bold tracking-widest uppercase"
                                  :class="stockStatus === 'habis' ? 'text-[#A65043]' : (stockStatus === 'menipis' ? 'text-[#A16A28]' : 'text-[#70746F]')">
                                STOK SAAT INI
                            </span>
                            <div class="font-serif text-xl sm:text-2xl font-bold text-[#24364A] mt-0.5">
                                <span x-text="formattedStock"></span>
                                <span class="text-sm font-sans font-medium text-[#70746F]" x-text="unit"></span>
                            </div>
                            <span class="text-xs text-[#70746F] block mt-0.5">
                                Batas aman <span class="font-semibold text-[#24364A]" x-text="formattedMinStock + ' ' + unit"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Right Status Pill -->
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold border transition"
                              :class="stockStatus === 'habis' ? 'bg-[#F9D7D3] text-[#A65043] border-[#F2C0B8]' : (stockStatus === 'menipis' ? 'bg-[#F7E7CD] text-[#A16A28] border-[#ECDCC7]' : 'bg-[#EAF4EF] text-[#39715A] border-[#CDE5D8]')"
                              x-text="statusLabel">
                        </span>
                    </div>
                </div>

                <!-- Form Inputs -->
                <form action="{{ route('procurement-requests.store') }}" 
                      method="POST" 
                      class="mt-6 space-y-6"
                      @submit="isSubmitting = true">
                    @csrf

                    <!-- Row 1: Nama Bahan & Jumlah yang Dibutuhkan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                        <!-- Nama Bahan -->
                        <div>
                            <label class="block text-xs font-semibold text-[#24364A] mb-2">
                                Nama Bahan <span class="text-[#A65043]">*</span>
                            </label>
                            <div class="relative">
                                <select name="ingredient_id" 
                                        x-model="selectedIngredientId" 
                                        @change="updateIngredient()"
                                        required
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition">
                                    @foreach($ingredients as $ing)
                                        @php
                                            $init = $ing->procurementRequests()->getModel()->getInitialsAttribute();
                                            // Fallback helper for initials
                                            $words = preg_split('/\s+/', trim($ing->name));
                                            if (count($words) === 1) {
                                                $calcInit = strtoupper(mb_substr($ing->name, 0, 2));
                                            } elseif (str_starts_with(strtolower($ing->name), 'biji')) {
                                                $calcInit = 'BI';
                                            } else {
                                                $calcInit = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
                                            }
                                            $fStock = number_format($ing->current_stock, floor($ing->current_stock) == $ing->current_stock ? 0 : 1, ',', '.');
                                            $fMin = number_format($ing->min_stock, floor($ing->min_stock) == $ing->min_stock ? 0 : 1, ',', '.');
                                        @endphp
                                        <option value="{{ $ing->id }}" 
                                                data-name="{{ $ing->name }}"
                                                data-initials="{{ $calcInit }}"
                                                data-stock="{{ (float)$ing->current_stock }}"
                                                data-formatted-stock="{{ $fStock }}"
                                                data-min-stock="{{ (float)$ing->min_stock }}"
                                                data-formatted-min-stock="{{ $fMin }}"
                                                data-unit="{{ $ing->unit?->symbol ?? '' }}"
                                                data-status="{{ $ing->status }}"
                                                data-status-label="{{ $ing->status_label }}"
                                                data-usage-7="{{ (float)($ing->usage_7_days ?? 0) }}"
                                                data-usage-30="{{ (float)($ing->usage_30_days ?? 0) }}"
                                                data-daily-avg="{{ (float)($ing->daily_avg ?? 0) }}"
                                                data-rec-week="{{ (int)($ing->recommended_week ?? 10) }}"
                                                data-rec-month="{{ (int)($ing->recommended_month ?? 40) }}"
                                                data-deficit="{{ (float)($ing->deficit ?? 0) }}"
                                                {{ (string)old('ingredient_id', $selectedIngredientId) === (string)$ing->id ? 'selected' : '' }}>
                                            {{ $ing->name }} (Sisa: {{ $fStock }} {{ $ing->unit?->symbol }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Jumlah yang Dibutuhkan -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold text-[#24364A]">
                                    Jumlah yang Dibutuhkan <span class="text-[#A65043]">*</span>
                                </label>
                                <span class="text-[10px] text-[#305785] font-semibold" x-text="selectedRecLabel"></span>
                            </div>
                            <div class="relative">
                                <input type="number" 
                                       name="quantity_requested" 
                                       x-model="quantityRequested" 
                                       @input="handleQuantityInput()"
                                       step="any"
                                       min="0.01"
                                       required
                                       placeholder="10"
                                       value="{{ old('quantity_requested', 10) }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition">
                                <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-[#70746F] pointer-events-none" 
                                      x-text="unit"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Rekomendasi Kebutuhan Berdasarkan Penggunaan (1 Minggu & 1 Bulan) -->
                    <div class="p-4 sm:p-4.5 rounded-2xl bg-[#FAF8F5] border border-[#D8CDBD] space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 pb-2.5 border-b border-[#E8E1D6]">
                            <div class="flex items-center gap-2">
                                <span class="p-1.5 rounded-lg bg-[#305785]/10 text-[#305785]">
                                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                                </span>
                                <div>
                                    <h4 class="text-xs font-bold text-[#24364A]">Rekomendasi Jumlah Pengadaan (Berdasarkan Penggunaan)</h4>
                                    <p class="text-[11px] text-[#70746F]">Klik pilihan periode di bawah untuk langsung mengisi jumlah kebutuhan:</p>
                                </div>
                            </div>
                            <span class="text-[11px] text-[#70746F] font-medium" x-show="dailyAvg > 0">
                                Rata-rata: <strong class="text-[#24364A]" x-text="formatNumber(dailyAvg) + ' ' + unit + '/hari'"></strong>
                            </span>
                        </div>

                        <!-- 2 Quick Action Cards: 1 Minggu & 1 Bulan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            
                            <!-- Card Rekomendasi 1 Minggu -->
                            <button type="button" 
                                    @click="applyRecommendation('week')"
                                    class="p-3.5 rounded-xl border text-left transition-all duration-150 flex items-start justify-between group relative overflow-hidden"
                                    :class="selectedRecType === 'week' ? 'bg-[#305785] text-white border-[#305785] shadow-md shadow-[#305785]/20 ring-2 ring-[#305785]/30' : 'bg-[#FFFDFA] hover:bg-[#F4F1EB] border-[#D8CDBD] text-[#24364A]'">
                                <div>
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5" :class="selectedRecType === 'week' ? 'text-[#FFD188]' : 'text-[#305785]'"></i>
                                        <span class="text-[11px] font-bold uppercase tracking-wider" :class="selectedRecType === 'week' ? 'text-white/90' : 'text-[#70746F]'">
                                            Kebutuhan 1 Minggu (7 Hari)
                                        </span>
                                    </div>
                                    <div class="font-serif text-xl font-bold mt-1">
                                        <span x-text="formatNumber(recWeek)"></span>
                                        <span class="text-xs font-sans font-normal" :class="selectedRecType === 'week' ? 'text-white/80' : 'text-[#70746F]'" x-text="unit"></span>
                                    </div>
                                    <span class="text-[10px] block mt-1" :class="selectedRecType === 'week' ? 'text-white/80' : 'text-[#70746F]'">
                                        Estimasi pemakaian 7 hari: <strong x-text="`${formatNumber(usage7 > 0 ? usage7 : recWeek)} ${unit}`"></strong>
                                    </span>
                                </div>
                                <div class="shrink-0 mt-1">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold transition"
                                          :class="selectedRecType === 'week' ? 'bg-white text-[#305785]' : 'bg-[#305785]/10 text-[#305785] group-hover:bg-[#305785] group-hover:text-white'">
                                        <span>Pilih</span>
                                    </span>
                                </div>
                            </button>

                            <!-- Card Rekomendasi 1 Bulan -->
                            <button type="button" 
                                    @click="applyRecommendation('month')"
                                    class="p-3.5 rounded-xl border text-left transition-all duration-150 flex items-start justify-between group relative overflow-hidden"
                                    :class="selectedRecType === 'month' ? 'bg-[#305785] text-white border-[#305785] shadow-md shadow-[#305785]/20 ring-2 ring-[#305785]/30' : 'bg-[#FFFDFA] hover:bg-[#F4F1EB] border-[#D8CDBD] text-[#24364A]'">
                                <div>
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <i data-lucide="calendar-range" class="w-3.5 h-3.5" :class="selectedRecType === 'month' ? 'text-[#FFD188]' : 'text-[#305785]'"></i>
                                        <span class="text-[11px] font-bold uppercase tracking-wider" :class="selectedRecType === 'month' ? 'text-white/90' : 'text-[#70746F]'">
                                            Kebutuhan 1 Bulan (30 Hari)
                                        </span>
                                    </div>
                                    <div class="font-serif text-xl font-bold mt-1">
                                        <span x-text="formatNumber(recMonth)"></span>
                                        <span class="text-xs font-sans font-normal" :class="selectedRecType === 'month' ? 'text-white/80' : 'text-[#70746F]'" x-text="unit"></span>
                                    </div>
                                    <span class="text-[10px] block mt-1" :class="selectedRecType === 'month' ? 'text-white/80' : 'text-[#70746F]'">
                                        Estimasi pemakaian 30 hari: <strong x-text="`${formatNumber(usage30 > 0 ? usage30 : recMonth)} ${unit}`"></strong>
                                    </span>
                                </div>
                                <div class="shrink-0 mt-1">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold transition"
                                          :class="selectedRecType === 'month' ? 'bg-white text-[#305785]' : 'bg-[#305785]/10 text-[#305785] group-hover:bg-[#305785] group-hover:text-white'">
                                        <span>Pilih</span>
                                    </span>
                                </div>
                            </button>

                        </div>

                        <!-- Deficit Note if Stock Below Safe Threshold -->
                        <div x-show="deficit > 0" class="pt-1 text-[11px] text-[#A16A28] flex items-center gap-1.5">
                            <i data-lucide="info" class="w-3.5 h-3.5 shrink-0"></i>
                            <span>Rekomendasi di atas telah disesuaikan dengan defisit <strong><span x-text="formatNumber(deficit) + ' ' + unit"></span></strong> agar persediaan kembali aman di atas batas minimum.</span>
                        </div>
                    </div>

                    <!-- Row 2: Tanggal Dibutuhkan & Prioritas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                        <!-- Tanggal Dibutuhkan -->
                        <div>
                            <label class="block text-xs font-semibold text-[#24364A] mb-2">
                                Tanggal Dibutuhkan <span class="text-[#A65043]">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" 
                                       name="required_date" 
                                       value="{{ old('required_date', $defaultDate) }}"
                                       required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition">
                            </div>
                        </div>

                        <!-- Prioritas -->
                        <div>
                            <label class="block text-xs font-semibold text-[#24364A] mb-2">
                                Prioritas <span class="text-[#A65043]">*</span>
                            </label>
                            <select name="priority" 
                                    x-model="priority" 
                                    @change="updatePriority()"
                                    required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition">
                                <option value="tinggi" {{ old('priority', 'tinggi') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                                <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="rendah" {{ old('priority') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Alasan Request -->
                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-2">
                            Alasan Request
                        </label>
                        <textarea name="reason" 
                                  x-model="reason"
                                  rows="3" 
                                  placeholder="Stok berada di bawah batas aman dan dibutuhkan untuk operasional shift berikutnya."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-[#305785] transition leading-relaxed"></textarea>
                    </div>

                    <!-- Row 4: Info Callout Box (Matches Image 2) -->
                    <div class="p-4 rounded-xl bg-[#FAF8F5] border border-[#D8CDBD] flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-full bg-[#E8E1D6]/60 text-[#305785] flex items-center justify-center shrink-0">
                            <i data-lucide="bell" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-[#24364A]">Request akan dikirim ke Owner</h4>
                            <p class="text-[11px] text-[#70746F] mt-0.5">Owner akan menerima notifikasi untuk meninjau dan menyetujui pengadaan ini.</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-[#D8CDBD]/70 flex items-center justify-end gap-3">
                        <a href="{{ route('procurement-requests.index') }}" 
                           class="btn btn-secondary px-6 py-2.5 text-xs font-semibold">
                            Batal
                        </a>
                        <button type="submit" 
                                :disabled="isSubmitting"
                                class="btn btn-primary px-6 py-2.5 text-xs font-semibold flex items-center gap-2 shadow-sm disabled:opacity-50">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                            <span x-text="isSubmitting ? 'Mengajukan...' : 'Ajukan Request'"></span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Right Column: Navy Blue Summary Card (Matches Image 2) -->
        <div class="lg:col-span-4">
            <div class="bg-[#24364A] text-white rounded-2xl p-6 sm:p-7 shadow-lg border border-[#305785]/30">
                
                <!-- Section Title -->
                <div class="text-[10px] font-bold tracking-widest text-[#D8CDBD]/70 uppercase">
                    RINGKASAN REQUEST
                </div>

                <!-- Selected Item Header -->
                <div class="flex items-center gap-3.5 mt-4 pb-5 border-b border-white/10">
                    <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center font-bold text-sm tracking-wider text-white shrink-0 border border-white/15"
                         x-text="initials">
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-base text-white truncate" x-text="ingredientName"></h3>
                        <p class="text-xs text-[#D8CDBD]/80 mt-0.5" 
                           x-text="stockStatus === 'habis' || stockStatus === 'menipis' ? 'Perlu segera diadakan' : 'Pengadaan berkala'">
                        </p>
                    </div>
                </div>

                <!-- Key-Value Specifications -->
                <div class="py-4 space-y-3.5 text-xs border-b border-white/10">
                    <div class="flex items-center justify-between">
                        <span class="text-[#D8CDBD]/80">Jumlah request</span>
                        <strong class="font-bold text-white" x-text="quantityRequested + ' ' + unit"></strong>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[#D8CDBD]/80">Dasar estimasi</span>
                        <span class="font-semibold text-[#FFD188] text-[11px]" x-text="selectedRecLabel"></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[#D8CDBD]/80">Prioritas</span>
                        <span class="font-bold" 
                              :class="priority === 'tinggi' ? 'text-[#FF8A7A]' : (priority === 'rendah' ? 'text-[#8AD1B8]' : 'text-white')"
                              x-text="priorityLabel"></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[#D8CDBD]/80">Diajukan oleh</span>
                        <strong class="font-bold text-white">{{ auth()->user()->name }}</strong>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[#D8CDBD]/80">Status awal</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#A16A28]/30 text-[#FFD188] border border-[#A16A28]/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#FFD188]"></span>
                            <span>Menunggu</span>
                        </span>
                    </div>
                </div>

                <!-- Next Steps Workflow Stepper (Matches Image 2) -->
                <div class="mt-5">
                    <div class="text-[10px] font-bold tracking-widest text-[#D8CDBD]/70 uppercase mb-4">
                        ALUR SELANJUTNYA
                    </div>

                    <div class="space-y-4 relative">
                        <!-- Connecting Line Behind Steps -->
                        <div class="absolute left-3 top-3 bottom-3 w-0.5 bg-white/15 -z-0"></div>

                        <!-- Step 1 -->
                        <div class="flex items-start gap-3 relative z-10">
                            <div class="w-6 h-6 rounded-full bg-white/20 text-white flex items-center justify-center text-xs font-bold shrink-0 ring-4 ring-[#24364A]">
                                1
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Review Owner</h4>
                                <p class="text-[11px] text-[#D8CDBD]/70 mt-0.5">Request ditinjau dan disetujui</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex items-start gap-3 relative z-10">
                            <div class="w-6 h-6 rounded-full bg-white/15 text-[#D8CDBD] flex items-center justify-center text-xs font-bold shrink-0 ring-4 ring-[#24364A]">
                                2
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Proses pengadaan</h4>
                                <p class="text-[11px] text-[#D8CDBD]/70 mt-0.5">Pemesanan kepada supplier</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex items-start gap-3 relative z-10">
                            <div class="w-6 h-6 rounded-full bg-white/15 text-[#D8CDBD] flex items-center justify-center text-xs font-bold shrink-0 ring-4 ring-[#24364A]">
                                3
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Penerimaan bahan</h4>
                                <p class="text-[11px] text-[#D8CDBD]/70 mt-0.5">Stok otomatis diperbarui</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    function procurementRequestForm() {
        return {
            selectedIngredientId: '{{ old('ingredient_id', $selectedIngredientId ?? ($ingredients->first()?->id ?? '')) }}',
            ingredientName: '',
            initials: '',
            currentStock: 0,
            formattedStock: '0',
            minStock: 0,
            formattedMinStock: '0',
            unit: '',
            stockStatus: 'aman',
            statusLabel: 'Aman',
            quantityRequested: '{{ old('quantity_requested', '') }}',
            priority: '{{ old('priority', 'tinggi') }}',
            priorityLabel: 'Tinggi',
            reason: '{{ old('reason', '') }}',
            isSubmitting: false,

            // Usage & recommendation fields
            usage7: 0,
            usage30: 0,
            dailyAvg: 0,
            recWeek: 10,
            recMonth: 40,
            deficit: 0,
            selectedRecType: 'week',
            selectedRecLabel: 'Rekomendasi 1 Minggu',

            init() {
                this.updateIngredient(true);
                this.updatePriority();
                this.$nextTick(() => {
                    if (window.createIcons) window.createIcons();
                });
            },

            // Format angka ke format desimal/ribuan Indonesia (contoh: 1.500)
            formatNumber(val) {
                if (!val && val !== 0) return '0';
                return Number(val).toLocaleString('id-ID');
            },

            // Terapkan pilihan kartu rekomendasi (1 Minggu atau 1 Bulan) ke input form
            applyRecommendation(type) {
                this.selectedRecType = type;
                if (type === 'week') {
                    this.quantityRequested = this.recWeek;
                    this.selectedRecLabel = 'Rekomendasi 1 Minggu';
                    if (this.stockStatus === 'habis') {
                        this.reason = `Stok bahan ${this.ingredientName} telah habis (0 ${this.unit}). Dibutuhkan pengadaan segera sebesar ${this.formatNumber(this.recWeek)} ${this.unit} untuk operasional 1 minggu kedepan.`;
                    } else if (this.stockStatus === 'menipis') {
                        this.reason = `Stok ${this.ingredientName} tersisa ${this.formattedStock} ${this.unit} (di bawah batas aman). Mengajukan pengadaan ${this.formatNumber(this.recWeek)} ${this.unit} untuk estimasi pemakaian 1 minggu.`;
                    } else {
                        this.reason = `Pengajuan pengadaan bahan ${this.ingredientName} sebesar ${this.formatNumber(this.recWeek)} ${this.unit} berdasarkan estimasi pemakaian operasional 1 minggu kedepan.`;
                    }
                } else if (type === 'month') {
                    this.quantityRequested = this.recMonth;
                    this.selectedRecLabel = 'Rekomendasi 1 Bulan';
                    if (this.stockStatus === 'habis' || this.stockStatus === 'menipis') {
                        this.reason = `Stok ${this.ingredientName} kritis. Mengajukan pengadaan ${this.formatNumber(this.recMonth)} ${this.unit} untuk menutupi defisit dan mengamankan stok selama 1 bulan kedepan.`;
                    } else {
                        this.reason = `Pengajuan pengadaan bahan ${this.ingredientName} sebesar ${this.formatNumber(this.recMonth)} ${this.unit} untuk cadangan kebutuhan operasional rutin selama 1 bulan kedepan.`;
                    }
                }
            },

            // Deteksi jika user mengetik angka manual di luar rekomendasi
            handleQuantityInput() {
                const entered = parseFloat(this.quantityRequested);
                if (entered === this.recWeek) {
                    this.selectedRecType = 'week';
                    this.selectedRecLabel = 'Rekomendasi 1 Minggu';
                } else if (entered === this.recMonth) {
                    this.selectedRecType = 'month';
                    this.selectedRecLabel = 'Rekomendasi 1 Bulan';
                } else {
                    this.selectedRecType = 'manual';
                    this.selectedRecLabel = 'Input Manual';
                }
            },

            // Sinkronisasi data bahan baku saat dropdown bahan berubah
            updateIngredient(isInitial = false) {
                const selectEl = document.querySelector('select[name="ingredient_id"]');
                if (!selectEl) return;

                let opt = null;
                if (this.selectedIngredientId) {
                    opt = Array.from(selectEl.options).find(o => String(o.value) === String(this.selectedIngredientId));
                }
                if (!opt && selectEl.selectedIndex >= 0) {
                    opt = selectEl.options[selectEl.selectedIndex];
                }

                if (opt) {
                    this.selectedIngredientId = opt.value;
                    this.ingredientName = opt.getAttribute('data-name') || '';
                    this.initials = opt.getAttribute('data-initials') || 'BK';
                    this.currentStock = parseFloat(opt.getAttribute('data-stock') || 0);
                    this.formattedStock = opt.getAttribute('data-formatted-stock') || '0';
                    this.minStock = parseFloat(opt.getAttribute('data-min-stock') || 0);
                    this.formattedMinStock = opt.getAttribute('data-formatted-min-stock') || '0';
                    this.unit = opt.getAttribute('data-unit') || '';
                    this.stockStatus = opt.getAttribute('data-status') || 'aman';
                    this.statusLabel = opt.getAttribute('data-status-label') || 'Aman';

                    // Data penggunaan & rekomendasi
                    this.usage7 = parseFloat(opt.getAttribute('data-usage-7') || 0);
                    this.usage30 = parseFloat(opt.getAttribute('data-usage-30') || 0);
                    this.dailyAvg = parseFloat(opt.getAttribute('data-daily-avg') || 0);
                    this.recWeek = parseInt(opt.getAttribute('data-rec-week') || 10, 10);
                    this.recMonth = parseInt(opt.getAttribute('data-rec-month') || 40, 10);
                    this.deficit = parseFloat(opt.getAttribute('data-deficit') || 0);

                    // Prioritas otomatis sesuai status stok
                    if (this.stockStatus === 'habis' || this.stockStatus === 'menipis') {
                        this.priority = 'tinggi';
                    } else if (!isInitial) {
                        this.priority = 'normal';
                    }
                    this.updatePriority();

                    // Terapkan rekomendasi
                    const initialRequested = '{{ old('quantity_requested', '') }}';
                    if (isInitial && initialRequested && initialRequested !== '10') {
                        this.quantityRequested = initialRequested;
                        this.handleQuantityInput();
                    } else if (this.selectedRecType === 'month') {
                        this.applyRecommendation('month');
                    } else {
                        this.applyRecommendation('week');
                    }
                }
            },

            updatePriority() {
                if (this.priority === 'tinggi') {
                    this.priorityLabel = 'Tinggi';
                } else if (this.priority === 'rendah') {
                    this.priorityLabel = 'Rendah';
                } else {
                    this.priorityLabel = 'Normal';
                }
            }
        };
    }
</script>
@endpush

