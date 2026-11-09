@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ receiptModal: false, activeReceipt: null }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-[#70746F]">
        <span>Pengadaan</span>
        <span class="text-[#B5ACA0]">></span>
        <span class="font-semibold text-[#24364A]">Rekap Pembelian Darurat</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Rekap Pembelian Darurat</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Histori dan rekapitulasi pengeluaran pembelian bahan baku mendesak di luar jadwal rutin.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('emergency-purchases.create') }}" 
               class="btn btn-primary btn-sm flex items-center gap-2 shadow-xs">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Catat Pembelian Darurat</span>
            </a>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="callout-box callout-success">
            <i data-lucide="check-circle" class="w-5 h-5 text-[#39715A] shrink-0 mt-0.5"></i>
            <div>
                <strong>Sukses!</strong>
                <p class="text-xs text-[#24364A] mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- 4 KPI Summary Cards (Owner Dashboard Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- KPI 1: Total Pengeluaran Darurat -->
        <div class="stat-card">
            <div class="stat-card-top">
                <span class="stat-label">Total Pengeluaran Darurat</span>
                <div class="stat-icon bg-[#305785]/10 text-[#305785]">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="stat-value text-xl sm:text-2xl font-bold text-[#24364A]">
                Rp {{ number_format($totalAmount, 0, ',', '.') }}
            </div>
            <div class="stat-trend text-[#70746F]">
                <span>Dari <strong>{{ $totalCount }}</strong> transaksi tercatat</span>
            </div>
        </div>

        <!-- KPI 2: Bulan Ini -->
        <div class="stat-card">
            <div class="stat-card-top">
                <span class="stat-label">Bulan Ini ({{ date('F Y') }})</span>
                <div class="stat-icon bg-[#FAF0DD] text-[#A16A28]">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="stat-value text-xl sm:text-2xl font-bold text-[#24364A]">
                Rp {{ number_format($thisMonthAmount, 0, ',', '.') }}
            </div>
            <div class="stat-trend text-[#70746F]">
                <span>{{ $thisMonthCount }} transaksi bulan ini</span>
            </div>
        </div>

        <!-- KPI 3: Bahan Sering Dibeli -->
        <div class="stat-card">
            <div class="stat-card-top">
                <span class="stat-label">Bahan Paling Sering Dibeli</span>
                <div class="stat-icon bg-[#F8E8E5] text-[#A65043]">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="stat-value text-base sm:text-lg font-bold text-[#24364A] truncate" title="{{ $topIngredient?->name ?? 'Belum Ada' }}">
                {{ $topIngredient ? $topIngredient->name : 'Belum Ada' }}
            </div>
            <div class="stat-trend text-[#70746F]">
                <span>{{ $topIngredient ? number_format($topIngredient->total_qty, 0, ',', '.') . ' ' . ($topIngredient->unit?->symbol ?? '') . ' (' . $topIngredient->purchase_count . 'x)' : 'Belum ada pembelian' }}</span>
            </div>
        </div>

        <!-- KPI 4: Sinkronisasi Stok -->
        <div class="stat-card">
            <div class="stat-card-top">
                <span class="stat-label">Sinkronisasi Stok</span>
                <div class="stat-icon bg-[#EAF4EF] text-[#39715A]">
                    <i data-lucide="check-check" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="stat-value text-xl sm:text-2xl font-bold text-[#39715A]">
                100% Otomatis
            </div>
            <div class="stat-trend text-[#39715A]">
                <span>Stok & mutasi terbarui langsung</span>
            </div>
        </div>

    </div>

    <!-- Search & Filter Bar -->
    <div class="card p-4 bg-[#FFFDFA] border border-[#D8CDBD] rounded-2xl shadow-xs">
        <form method="GET" action="{{ route('emergency-purchases.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                
                <!-- Search text -->
                <div class="lg:col-span-2 relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#70746F]">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari kode, toko, bahan, barista..."
                           class="w-full pl-10 pr-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FAF8F5]/60 focus:outline-none focus:ring-2 focus:ring-[#305785]">
                </div>

                <!-- Bahan Baku Filter -->
                <div>
                    <select name="ingredient_id" 
                            class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FAF8F5]/60 focus:outline-none focus:ring-2 focus:ring-[#305785]">
                        <option value="">Semua Bahan Baku</option>
                        @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}" {{ $ingredientId == $ing->id ? 'selected' : '' }}>
                                {{ $ing->name }} ({{ $ing->unit?->symbol ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Barista Filter -->
                <div>
                    <select name="user_id" 
                            class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FAF8F5]/60 focus:outline-none focus:ring-2 focus:ring-[#305785]">
                        <option value="">Semua Barista / Pencatat</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ ucfirst($u->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rentang Tanggal: Start Date & End Date -->
                <div class="flex items-center gap-1.5">
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate }}" 
                           placeholder="Dari"
                           title="Dari Tanggal"
                           class="w-1/2 px-2 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FAF8F5]/60 focus:outline-none focus:ring-2 focus:ring-[#305785]">
                    <span class="text-xs text-[#70746F]">-</span>
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate }}" 
                           placeholder="Sampai"
                           title="Sampai Tanggal"
                           class="w-1/2 px-2 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FAF8F5]/60 focus:outline-none focus:ring-2 focus:ring-[#305785]">
                </div>

            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center justify-between pt-2 border-t border-[#D8CDBD]/40 text-xs">
                <span class="text-[#70746F]">Menampilkan <strong>{{ $purchases->total() }}</strong> catatan pembelian darurat</span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('emergency-purchases.index') }}" 
                       class="px-3 py-1.5 rounded-lg border border-[#D8CDBD] text-[#70746F] hover:bg-[#E8E1D6] transition">
                        Reset Filter
                    </a>
                    <button type="submit" 
                            class="btn btn-primary py-1.5 px-4 text-xs font-semibold">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table: Daftar Pembelian Darurat -->
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Waktu / Tanggal</th>
                    <th>Kode Pembelian</th>
                    <th>Bahan Baku</th>
                    <th class="text-right">Jumlah Masuk</th>
                    <th class="text-right">Total Harga</th>
                    <th>Tempat Pembelian</th>
                    <th>Barista / Pencatat</th>
                    <th>Alasan / Keterangan</th>
                    <th class="text-center">Bukti Nota</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $item)
                    <tr>
                        <!-- Waktu / Tanggal Real-Time -->
                        <td class="whitespace-nowrap">
                            <strong class="text-xs text-[#24364A] block">{{ $item->purchase_date->format('d M Y') }}</strong>
                            <div class="flex items-center gap-1.5 text-[11px] text-[#70746F] mt-0.5">
                                <i data-lucide="clock" class="w-3 h-3 text-[#305785]"></i>
                                <span class="font-mono font-bold text-[#305785]">{{ $item->purchase_time ? \Carbon\Carbon::parse($item->purchase_time)->format('H:i:s') : $item->created_at->format('H:i:s') }}</span>
                                <span>WIB</span>
                            </div>
                            @if($item->created_at->isToday())
                                <span class="text-[10px] text-[#39715A] font-medium block mt-0.5">{{ $item->created_at->diffForHumans() }}</span>
                            @endif
                        </td>

                        <!-- Kode Transaksi -->
                        <td class="whitespace-nowrap">
                            <span class="font-mono font-bold text-xs text-[#305785] bg-[#305785]/10 px-2 py-1 rounded-md">
                                {{ $item->purchase_code }}
                            </span>
                        </td>

                        <!-- Bahan Baku -->
                        <td>
                            <strong class="text-xs text-[#24364A] block">{{ $item->ingredient?->name ?? 'Bahan Dihapus' }}</strong>
                            <span class="badge badge-neutral text-[10px] mt-0.5">
                                {{ $item->ingredient?->category?->name ?? '-' }}
                            </span>
                        </td>

                        <!-- Jumlah -->
                        <td class="text-right whitespace-nowrap">
                            <span class="font-bold text-xs text-[#39715A] bg-[#EAF4EF] px-2 py-0.5 rounded-md">
                                +{{ $item->formatted_quantity }} {{ $item->ingredient?->unit?->symbol ?? '' }}
                            </span>
                        </td>

                        <!-- Total Biaya -->
                        <td class="text-right whitespace-nowrap font-bold text-xs text-[#24364A]">
                            {{ $item->formatted_amount }}
                        </td>

                        <!-- Tempat Pembelian -->
                        <td class="whitespace-nowrap">
                            <div class="flex items-center gap-1.5 text-xs text-[#24364A]">
                                <i data-lucide="store" class="w-3.5 h-3.5 text-[#70746F]"></i>
                                <span>{{ $item->purchase_location }}</span>
                            </div>
                        </td>

                        <!-- Pencatat -->
                        <td class="whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-[#E8E1D6] text-[#305785] flex items-center justify-center font-bold text-[10px]">
                                    {{ strtoupper(substr($item->user?->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-xs font-semibold text-[#24364A]">{{ $item->user?->name ?? '-' }}</span>
                            </div>
                        </td>

                        <!-- Alasan -->
                        <td class="max-w-xs">
                            <p class="text-xs text-[#70746F] line-clamp-2" title="{{ $item->notes }}">
                                {{ $item->notes ?: '-' }}
                            </p>
                        </td>

                        <!-- Bukti Struk -->
                        <td class="text-center whitespace-nowrap">
                            @if($item->receipt_proof)
                                <button type="button" 
                                        @click="activeReceipt = {
                                            code: '{{ $item->purchase_code }}',
                                            url: '{{ $item->receipt_url }}',
                                            isPdf: {{ $item->is_pdf ? 'true' : 'false' }},
                                            ingredient: '{{ addslashes($item->ingredient?->name ?? '') }}',
                                            amount: '{{ $item->formatted_amount }}',
                                            location: '{{ addslashes($item->purchase_location) }}',
                                            date: '{{ $item->purchase_date->format('d M Y') }}',
                                            time: '{{ $item->purchase_time ? \Carbon\Carbon::parse($item->purchase_time)->format('H:i:s') : $item->created_at->format('H:i:s') }} WIB',
                                            user: '{{ addslashes($item->user?->name ?? '') }}'
                                        }; receiptModal = true;"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-[#305785] hover:text-[#274A70] bg-[#305785]/10 hover:bg-[#305785]/20 px-2.5 py-1.5 rounded-lg transition">
                                    <i data-lucide="{{ $item->is_pdf ? 'file-text' : 'image' }}" class="w-3.5 h-3.5"></i>
                                    <span>Lihat Bukti</span>
                                </button>
                            @else
                                <span class="text-[11px] text-[#B5ACA0] italic">Tidak ada foto</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-xs text-[#70746F]">
                            <i data-lucide="inbox" class="w-8 h-8 text-[#B5ACA0] mx-auto mb-2"></i>
                            <strong class="block text-[#24364A] text-sm font-bold mb-1">Belum Ada Catatan Pembelian Darurat</strong>
                            <span>Seluruh transaksi pembelian mendesak di luar jadwal rutin yang dicatat barista akan muncul di sini.</span>
                            <div class="mt-4">
                                <a href="{{ route('emergency-purchases.create') }}" class="btn btn-primary btn-sm">
                                    Catat Pembelian Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($purchases->hasPages())
        <div class="pt-2">
            {{ $purchases->links() }}
        </div>
    @endif

    <!-- Receipt Preview Modal -->
    <div x-show="receiptModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="receiptModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl border border-[#D8CDBD]">
            
            <div class="flex items-center justify-between pb-3 border-b border-[#D8CDBD]">
                <div>
                    <h3 class="font-serif text-base font-bold text-[#24364A]">Bukti Pembelian Darurat</h3>
                    <p class="text-xs text-[#70746F]" x-text="activeReceipt ? activeReceipt.code : ''"></p>
                </div>
                <button @click="receiptModal = false" class="p-1.5 text-[#70746F] hover:text-[#24364A] rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="mt-4 space-y-4">
                <!-- Image or PDF View -->
                <div class="max-h-[380px] overflow-auto rounded-xl border border-[#D8CDBD] bg-[#FAF8F5] flex items-center justify-center p-2">
                    <template x-if="activeReceipt && !activeReceipt.isPdf">
                        <img :src="activeReceipt.url" alt="Bukti Struk" class="max-w-full max-h-[360px] object-contain rounded-lg">
                    </template>
                    <template x-if="activeReceipt && activeReceipt.isPdf">
                        <div class="p-8 text-center space-y-3">
                            <i data-lucide="file-text" class="w-12 h-12 text-[#305785] mx-auto"></i>
                            <p class="text-xs font-bold text-[#24364A]">Dokumen Bukti Berformat PDF</p>
                            <a :href="activeReceipt.url" target="_blank" class="btn btn-primary btn-sm inline-flex items-center gap-1.5">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                <span>Buka / Unduh PDF</span>
                            </a>
                        </div>
                    </template>
                </div>

                <!-- Info Snippet -->
                <template x-if="activeReceipt">
                    <div class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl text-xs space-y-1.5">
                        <div class="flex justify-between text-[#70746F]">
                            <span>Bahan Dibeli:</span>
                            <strong class="text-[#24364A]" x-text="activeReceipt.ingredient"></strong>
                        </div>
                        <div class="flex justify-between text-[#70746F]">
                            <span>Total Harga:</span>
                            <strong class="text-[#305785] font-bold" x-text="activeReceipt.amount"></strong>
                        </div>
                        <div class="flex justify-between text-[#70746F]">
                            <span>Toko / Tempat:</span>
                            <span class="text-[#24364A]" x-text="activeReceipt.location"></span>
                        </div>
                        <div class="flex justify-between text-[#70746F]">
                            <span>Tanggal Transaksi:</span>
                            <span class="text-[#24364A]" x-text="activeReceipt.date"></span>
                        </div>
                        <div class="flex justify-between text-[#70746F]">
                            <span>Waktu Transaksi:</span>
                            <span class="text-[#305785] font-mono font-bold" x-text="activeReceipt.time"></span>
                        </div>
                        <div class="flex justify-between text-[#70746F]">
                            <span>Barista:</span>
                            <span class="text-[#24364A]" x-text="activeReceipt.user"></span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-5 flex items-center justify-end gap-2">
                <template x-if="activeReceipt && !activeReceipt.isPdf">
                    <a :href="activeReceipt.url" target="_blank" class="btn btn-secondary btn-sm flex items-center gap-1.5">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        <span>Buka Tab Baru</span>
                    </a>
                </template>
                <button type="button" @click="receiptModal = false" class="btn btn-primary btn-sm">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>
@endsection

