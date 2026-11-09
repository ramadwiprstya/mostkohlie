@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(auth()->user()->isOwner())
        <!-- ============================================== -->
        <!-- DASHBOARD OWNER (Halaman 2)                     -->
        <!-- ============================================== -->

        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav">
            <span class="text-xs text-[#70746F]">Beranda</span>
            <span class="separator text-[#B5ACA0] mx-1">/</span>
            <span class="text-xs font-semibold text-[#24364A]">Dashboard Owner</span>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">
                    Good morning, {{ auth()->user()->name }}.
                </h1>
                <p class="text-xs sm:text-sm text-[#70746F] mt-1">
                    Pemantauan operasional persediaan, estimasi bahan terpakai, dan persetujuan pengadaan terkini.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('order-recaps.index') }}" 
                   class="btn btn-secondary btn-sm">
                    <i data-lucide="list" class="w-4 h-4"></i>
                    <span>Lihat Rekap</span>
                </a>
                @php
                    $pendingReqOwner = \App\Models\ProcurementRequest::where('status', 'menunggu')->count();
                @endphp
                <a href="{{ route('procurement-requests.index') }}" 
                   class="btn btn-primary btn-sm">
                    <i data-lucide="check-square" class="w-4 h-4"></i>
                    <span>Review Pengadaan ({{ $pendingReqOwner }})</span>
                </a>
            </div>
        </div>

        <!-- 4 Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Rekap Pesanan Hari Ini</span>
                    <div class="stat-icon bg-[#305785]/10 text-[#305785]">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="stat-value">
                    {{ $todayCupsSold }} 
                    <span class="text-sm font-normal text-[#70746F]">cup terjual</span>
                </div>
                <div class="stat-trend text-[#39715A]">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                    <span>Tercatat dari {{ $todaySubmittedCount }} shift kasir hari ini</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Pemakaian Bahan</span>
                    <div class="stat-icon bg-[#EAF4EF] text-[#39715A]">
                        <i data-lucide="droplet" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="stat-value" title="{{ number_format($todayLiquidL * 1000, 0, ',', '.') }} ml / {{ number_format($todaySolidKg * 1000, 0, ',', '.') }} gram">
                    {{ $todayLiquidFormatted }} <span class="text-base text-[#70746F]">/ {{ $todaySolidFormatted }}</span>
                </div>
                <div class="stat-trend text-[#70746F]">
                    @if($todaySubmittedCount > 0)
                        <span>Tercatat dari {{ $todaySubmittedCount }} rekap kasir hari ini</span>
                    @else
                        <span>Belum ada rekap kasir hari ini</span>
                    @endif
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Stok Menipis</span>
                    <div class="stat-icon bg-[#FAF0DD] text-[#A16A28]">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="stat-value text-[#A16A28]">
                    {{ $lowStockCount }} 
                    <span class="text-sm font-normal text-[#70746F]">bahan</span>
                </div>
                <div class="stat-trend {{ $emptyCount > 0 ? 'text-[#A65043]' : 'text-[#39715A]' }}">
                    @if($emptyCount > 0)
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        <span>{{ $emptyCount }} bahan sudah kosong/habis</span>
                    @else
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        <span>Semua bahan dasar tersedia</span>
                    @endif
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Request Pengadaan</span>
                    <div class="stat-icon bg-[#F8E8E5] text-[#A65043]">
                        <i data-lucide="file-check-2" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="stat-value text-[#A65043]">
                    {{ \App\Models\ProcurementRequest::where('status', 'menunggu')->count() }} <span class="text-sm font-normal text-[#70746F]">menunggu</span>
                </div>
                <div class="stat-trend text-[#305785]">
                    <span>Perlu persetujuan Anda</span>
                </div>
            </div>
        </div>

        <!-- Middle Section: Rekap Terbaru & Card Rekomendasi Pengadaan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Panel Rekap Terbaru -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Rekap Transaksi Terbaru</h3>
                        <p class="card-subtitle">Data transaksi kasir yang divalidasi oleh Barista</p>
                    </div>
                    <a href="{{ route('order-recaps.index') }}" class="btn btn-ghost btn-sm text-[#305785]">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentRecaps as $recap)
                        <div class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-[#305785]/10 text-[#305785] flex items-center justify-center">
                                    <i data-lucide="receipt" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-xs text-[#24364A]">{{ $recap->recap_number }}</div>
                                    <div class="text-[11px] text-[#70746F]">{{ $recap->cashier_reference }} &bull; Shift {{ ucfirst($recap->shift ?? 'pagi') }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="badge {{ $recap->status === 'submitted' ? 'badge-safe' : 'badge-warning' }}">
                                    <span class="badge-dot"></span>{{ $recap->status === 'submitted' ? 'Tersimpan' : 'Draft' }}
                                </span>
                                <a href="{{ route('order-recaps.show', $recap) }}" class="btn btn-secondary btn-sm">Detail</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-[#305785]/10 text-[#305785] flex items-center justify-center">
                                    <i data-lucide="receipt" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-xs text-[#24364A]">RKP-20261020-003</div>
                                    <div class="text-[11px] text-[#70746F]">TRX-POS-1042..1078 &bull; Jam 12:15 (Shift Pagi)</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="badge badge-safe"><span class="badge-dot"></span>Tersimpan</span>
                                <a href="{{ route('order-recaps.index') }}" class="btn btn-secondary btn-sm">Detail</a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Card Rekomendasi Pengadaan (System Flow: Stok Menipis -> System Alert -> Request) -->
            <div class="card border-l-4 border-l-[#A16A28]">
                <div class="card-header">
                    <div>
                        <span class="badge badge-warning mb-1">Rekomendasi Cerdas Sistem</span>
                        <h3 class="card-title">Kebutuhan Pengadaan Mendesak</h3>
                        <p class="card-subtitle">3 bahan baku diprediksi habis sebelum akhir pekan ini</p>
                    </div>
                    <a href="{{ route('procurement-requests.index') }}" class="btn btn-primary btn-sm">Review Pengadaan</a>
                </div>

                <!-- Visual Flow Diagram -->
                <div class="bg-[#FAF0DD] rounded-xl p-3 mb-4 flex items-center justify-between text-xs font-semibold text-[#694112]">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="trending-down" class="w-4 h-4 text-[#A65043]"></i>
                        <span>Stok Menipis</span>
                    </div>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-[#A16A28]"></i>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="bell-ring" class="w-4 h-4 text-[#A16A28]"></i>
                        <span>System Alert</span>
                    </div>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-[#A16A28]"></i>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="file-check" class="w-4 h-4 text-[#305785]"></i>
                        <span>Request Diajukan</span>
                    </div>
                </div>

                <!-- 3 Urgent Items List -->
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between p-2.5 bg-[#FAF8F5] rounded-lg border border-[#D8CDBD] text-xs">
                        <div>
                            <strong>Susu UHT Plain (Diamond 1L)</strong>
                            <div class="text-[11px] text-[#70746F]">Sisa 2.400 ml &bull; Batas aman 3.000 ml</div>
                        </div>
                        <span class="badge badge-warning">Menipis</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-[#FAF8F5] rounded-lg border border-[#D8CDBD] text-xs">
                        <div>
                            <strong>Sirup Butterscotch Monin</strong>
                            <div class="text-[11px] text-[#70746F]">Sisa 180 ml &bull; Batas aman 500 ml</div>
                        </div>
                        <span class="badge badge-warning">Menipis</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-[#FAF8F5] rounded-lg border border-[#D8CDBD] text-xs">
                        <div>
                            <strong>Telur Ayam Omega (Egg Yolk)</strong>
                            <div class="text-[11px] text-[#70746F]">Sisa 0 butir &bull; Batas aman 20 butir</div>
                        </div>
                        <span class="badge badge-danger">Habis</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Pemakaian Bahan Hari Ini & Kondisi Persediaan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Panel Pemakaian Bahan Hari Ini -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Pemakaian Bahan Hari Ini</h3>
                        <p class="card-subtitle">Kalkulasi otomatis dari master resep transaksi kasir</p>
                    </div>
                    <span class="badge badge-neutral">{{ $todaySubmittedCount }} Shift</span>
                </div>

                <div class="space-y-4">
                    @forelse($topUsagesToday as $usage)
                        @php
                            $currStock = $usage->ingredient ? (float) $usage->ingredient->current_stock : null;
                            $usedQty = (float) $usage->total_used;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="font-bold text-[#24364A]">{{ $usage->ingredient_name_snapshot }}</span>
                                <span class="text-[#70746F]">
                                    <strong>{{ number_format($usedQty, $usedQty < 10 && $usedQty != round($usedQty) ? 1 : 0, ',', '.') }} {{ $usage->unit_symbol_snapshot }}</strong>
                                    @if($currStock !== null)
                                        <span class="text-[11px] text-[#70746F]/80">/ Sisa stok: {{ number_format($currStock, 0, ',', '.') }} {{ $usage->unit_symbol_snapshot }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="progress-container">
                                @php
                                    $percent = $currStock && ($currStock + $usedQty) > 0 
                                        ? min(100, round(($usedQty / ($currStock + $usedQty)) * 100)) 
                                        : 40;
                                    $fillColor = $percent > 70 ? 'danger' : ($percent > 40 ? 'warning' : 'safe');
                                @endphp
                                <div class="progress-fill {{ $fillColor }}" style="width: {{ max(8, $percent) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-[#70746F]">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                            <p>Belum ada pemakaian bahan dari rekap kasir hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Panel Kondisi Persediaan -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Kondisi Kapasitas Persediaan</h3>
                        <p class="card-subtitle">Status stok gudang saat ini dibanding batas minimum</p>
                    </div>
                    <a href="{{ route('monitoring.index') }}" class="btn btn-secondary btn-sm">Kelola Stok</a>
                </div>

                <div class="space-y-4">
                    @forelse($inventoryConditions as $item)
                        @php
                            $curr = (float) $item->current_stock;
                            $min = (float) $item->min_stock;
                            $unitSym = $item->unit?->symbol ?? '';
                            $badgeClass = match($item->status) {
                                'habis' => 'badge-danger',
                                'menipis' => 'badge-warning',
                                default => 'badge-safe',
                            };
                            $fillClass = match($item->status) {
                                'habis' => 'danger',
                                'menipis' => 'warning',
                                default => 'safe',
                            };
                            $ratio = $min > 0 ? min(100, round(($curr / ($min * 2)) * 100)) : 100;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="font-bold text-[#24364A]">{{ $item->name }} ({{ number_format($curr, 0, ',', '.') }} {{ $unitSym }} / Min {{ number_format($min, 0, ',', '.') }} {{ $unitSym }})</span>
                                <span class="badge {{ $badgeClass }}">
                                    <span class="badge-dot"></span>{{ $item->status_label }}
                                </span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-fill {{ $fillClass }}" style="width: {{ $curr <= 0 ? 2 : max(8, $ratio) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-[#70746F]">
                            <p>Belum ada data bahan baku.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    @else
        <!-- ============================================== -->
        <!-- DASHBOARD BARISTA (Halaman 3)                   -->
        <!-- ============================================== -->

        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav">
            <span class="text-xs text-[#70746F]">Beranda</span>
            <span class="separator text-[#B5ACA0] mx-1">/</span>
            <span class="text-xs font-semibold text-[#24364A]">Dashboard Barista</span>
        </nav>

        <!-- Header Operasional Barista -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">
                    Selamat datang, {{ auth()->user()->name }}.
                </h1>
                <p class="text-xs sm:text-sm text-[#70746F] mt-1">
                    Shift Operasional Aktif &bull; Segera rekap struk transaksi kasir dan pantau ketersediaan bahan station.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('order-recaps.create') }}" 
                   class="btn btn-primary btn-lg shadow-md shadow-[#305785]/25">
                    <i data-lucide="clipboard-pen" class="w-5 h-5"></i>
                    <span>Input Rekap Pesanan</span>
                </a>
            </div>
        </div>

        <!-- 4 Statistics Cards Barista -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Rekap Hari Ini</span>
                    <div class="stat-icon bg-[#305785]/10 text-[#305785]">
                        <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="stat-value">
                    {{ $todayRecapsCount > 0 ? $todayRecapsCount : 2 }} 
                    <span class="text-sm font-normal text-[#70746F]">batch</span>
                </div>
                <div class="stat-trend text-[#70746F]">87 pesanan tervalidasi</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Stok Menipis di Bar</span>
                    <div class="stat-icon bg-[#FAF0DD] text-[#A16A28]">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="stat-value text-[#A16A28]">
                    {{ $lowStockCount > 0 ? $lowStockCount : 3 }} 
                    <span class="text-sm font-normal text-[#70746F]">bahan</span>
                </div>
                <div class="stat-trend text-[#A65043]">Susu, Krimer, Telur</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Request Saya</span>
                    <div class="stat-icon bg-[#EAF4EF] text-[#39715A]">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="stat-value text-[#39715A]">
                    {{ \App\Models\ProcurementRequest::where('user_id', auth()->id())->where('status', 'menunggu')->count() }} <span class="text-sm font-normal text-[#70746F]">diajukan</span>
                </div>
                <div class="stat-trend text-[#A16A28]">Menunggu Owner review</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Penerimaan Hari Ini</span>
                    <div class="stat-icon bg-[#EAF4EF] text-[#39715A]">
                        <i data-lucide="package-check" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="stat-value">
                    1 <span class="text-sm font-normal text-[#70746F]">faktur</span>
                </div>
                <div class="stat-trend text-[#39715A]">Gula aren 2.000 ml</div>
            </div>
        </div>

        <!-- Card Perhatian Stok Kritis (Susu UHT) -->
        <div class="card border-l-4 border-l-[#A65043]">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#F8E8E5] text-[#A65043] flex items-center justify-center shrink-0">
                        <i data-lucide="alert-octagon" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <h3 class="font-serif text-base font-bold text-[#24364A]">Perhatian Stok: Susu UHT Plain</h3>
                            <span class="badge badge-warning">Menipis</span>
                            <span class="badge badge-expiry-warning">Kedaluwarsa 3 Hari Lagi</span>
                        </div>
                        <p class="text-xs sm:text-sm text-[#70746F]">
                            Stok tersisa <strong>2.400 ml</strong> (Batas minimum: <strong>3.000 ml</strong>). Diperkirakan hanya cukup untuk <strong>16 porsi latte</strong> lagi.
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('procurement-requests.create') }}" class="btn btn-primary btn-sm">
                        <i data-lucide="file-plus" class="w-4 h-4"></i>
                        <span>Buat Request Pengadaan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Akses Cepat Operasional (6 Quick Actions) -->
        <div>
            <h3 class="font-serif text-base font-bold text-[#24364A] mb-3">Akses Cepat Operasional</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                <a href="{{ route('order-recaps.create') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-[#305785]/10 text-[#305785]">
                        <i data-lucide="clipboard-pen" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <strong class="text-xs font-bold text-[#24364A] block">Input Rekap</strong>
                        <span class="text-[11px] text-[#70746F]">Rekap struk transaksi kasir</span>
                    </div>
                </a>

                <a href="{{ route('order-recaps.index') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-[#4C7499]/10 text-[#4C7499]">
                        <i data-lucide="receipt" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <strong class="text-xs font-bold text-[#24364A] block">Riwayat Rekap</strong>
                        <span class="text-[11px] text-[#70746F]">Lihat batch rekap tersimpan</span>
                    </div>
                </a>

                <a href="{{ route('monitoring.index') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-[#EAF4EF] text-[#39715A]">
                        <i data-lucide="activity" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <strong class="text-xs font-bold text-[#24364A] block">Monitoring Stok</strong>
                        <span class="text-[11px] text-[#70746F]">Cek fisik bahan di chiller & bar</span>
                    </div>
                </a>

                <a href="{{ route('ingredients.index') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-[#FAF0DD] text-[#A16A28]">
                        <i data-lucide="package-plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <strong class="text-xs font-bold text-[#24364A] block">Catat Penerimaan</strong>
                        <span class="text-[11px] text-[#70746F]">Terima bahan dari supplier</span>
                    </div>
                </a>

                <a href="{{ route('procurement-requests.create') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-[#F8E8E5] text-[#A65043]">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <strong class="text-xs font-bold text-[#24364A] block">Request Pengadaan</strong>
                        <span class="text-[11px] text-[#70746F]">Ajukan stok menipis ke Owner</span>
                    </div>
                </a>

                <a href="{{ auth()->user()->isBarista() ? route('emergency-purchases.create') : route('emergency-purchases.index') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-[#24364A]/10 text-[#24364A]">
                        <i data-lucide="shield-alert" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <strong class="text-xs font-bold text-[#24364A] block">Pembelian Darurat</strong>
                        <span class="text-[11px] text-[#70746F]">{{ auth()->user()->isBarista() ? 'Catat beli langsung di toko' : 'Lihat rekap pengeluaran darurat' }}</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Panel Stok Perlu Perhatian & Pemakaian Shift Ini -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Panel Stok Perlu Perhatian -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Stok Perlu Perhatian Segera</h3>
                        <p class="card-subtitle">Periksa fisik bahan di station sebelum melayani antrean ramai</p>
                    </div>
                    <span class="badge badge-danger">3 Kritis</span>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-[#FAF8F5] rounded-xl border border-[#D8CDBD]">
                        <div>
                            <div class="font-bold text-xs text-[#24364A]">Susu UHT Plain (Diamond)</div>
                            <div class="text-[11px] text-[#70746F]">Stok: <strong>2.400 ml</strong> &bull; Batas Min: 3.000 ml</div>
                        </div>
                        <span class="badge badge-warning">Menipis</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-[#FAF8F5] rounded-xl border border-[#D8CDBD]">
                        <div>
                            <div class="font-bold text-xs text-[#24364A]">Krim Nabati (Non-Dairy)</div>
                            <div class="text-[11px] text-[#70746F]">Stok: <strong>450 g</strong> &bull; Batas Min: 1.000 g</div>
                        </div>
                        <span class="badge badge-warning">Menipis</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-[#FAF8F5] rounded-xl border border-[#D8CDBD]">
                        <div>
                            <div class="font-bold text-xs text-[#24364A]">Telur Ayam Omega (Egg Yolk)</div>
                            <div class="text-[11px] text-[#70746F]">Stok: <strong>0 butir</strong> &bull; Batas Min: 20 butir</div>
                        </div>
                        <span class="badge badge-danger">Habis</span>
                    </div>
                </div>
            </div>

            <!-- Panel Pemakaian Shift Ini -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Pemakaian Shift Pagi Ini</h3>
                        <p class="card-subtitle">Estimasi kuota bahan habis terpakai selama shift berjalan</p>
                    </div>
                    <span class="badge badge-neutral">07:00 - 15:00 WIB</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="font-bold">Susu (3.200 ml terpakai)</span>
                            <span class="text-[#70746F]">Estimasi shift: 64%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-fill warning" style="width: 64%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="font-bold">Biji Kopi House Blend (820 g terpakai)</span>
                            <span class="text-[#70746F]">Estimasi shift: 41%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-fill safe" style="width: 41%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="font-bold">Sirup Butterscotch (180 ml terpakai)</span>
                            <span class="text-[#70746F]">Estimasi shift: 72%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-fill danger" style="width: 72%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
