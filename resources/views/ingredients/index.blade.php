@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="penerimaanBahanApp()">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-[#70746F]">
        <span class="text-[#70746F]">Persediaan</span>
        <span class="text-[#B5ACA0]">></span>
        <span class="font-semibold text-[#24364A]">Penerimaan Bahan</span>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Rekap Penerimaan Bahan</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Pencatatan barang masuk dari request pengadaan, verifikasi faktur supplier, dan rekap mutasi stok.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Tombol Catat Penerimaan Langsung -->
            <button type="button" 
                    @click="directReceiveModal = true"
                    class="btn btn-secondary btn-sm flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="package-plus" class="w-4 h-4 text-[#39715A]"></i>
                <span>Penerimaan Langsung</span>
            </button>

            <!-- Tombol Buat Request Pengadaan -->
            <a href="{{ route('procurement-requests.create') }}" 
               class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-xs">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Buat Request Pengadaan</span>
            </a>
        </div>
    </div>

    <!-- Alert Success / Error / Warning -->
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
                <strong>Periksa kembali isian formulir:</strong>
                <ul class="list-disc list-inside text-xs text-[#24364A] mt-1 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- KPI 1: Total Penerimaan Tercatat -->
        <div class="stat-card">
            <div class="stat-card-top">
                <span class="stat-label">Total Penerimaan Tercatat</span>
                <div class="stat-icon bg-[#EAF4EF] text-[#39715A]">
                    <i data-lucide="package-check" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="stat-value text-xl sm:text-2xl font-bold text-[#24364A]">
                {{ $totalReceiptsCount }} <span class="text-xs font-normal text-[#70746F]">faktur</span>
            </div>
            <div class="stat-trend text-[#39715A]">
                <span>Telah masuk ke stok fisik</span>
            </div>
        </div>

        <!-- KPI 2: Menunggu Kedatangan (Siap Diterima) -->
        <a href="{{ route('ingredients.index', ['tab' => 'menunggu']) }}" 
           class="stat-card hover:border-[#305785] transition {{ $activeTab === 'menunggu' ? 'ring-2 ring-[#305785]/20 border-[#305785]' : '' }}">
            <div class="stat-card-top">
                <span class="stat-label">Menunggu Kedatangan</span>
                <div class="stat-icon bg-[#FAF0DD] text-[#A16A28]">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="stat-value text-xl sm:text-2xl font-bold text-[#A16A28]">
                {{ $waitingArrivalCount }} <span class="text-xs font-normal text-[#70746F]">pengadaan</span>
            </div>
            <div class="stat-trend text-[#A16A28]">
                <span>Siap diverifikasi & diterima</span>
            </div>
        </a>

        <!-- KPI 3: Penerimaan Bulan Ini -->
        <div class="stat-card">
            <div class="stat-card-top">
                <span class="stat-label">Penerimaan Bulan Ini</span>
                <div class="stat-icon bg-[#305785]/10 text-[#305785]">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="stat-value text-xl sm:text-2xl font-bold text-[#24364A]">
                {{ $thisMonthReceiptsCount }} <span class="text-xs font-normal text-[#70746F]">transaksi</span>
            </div>
            <div class="stat-trend text-[#70746F]">
                <span>Bulan {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('F Y') }}</span>
            </div>
        </div>

        <!-- KPI 4: Penerimaan Hari Ini -->
        <div class="stat-card">
            <div class="stat-card-top">
                <span class="stat-label">Penerimaan Hari Ini</span>
                <div class="stat-icon bg-[#F4F1EB] text-[#24364A]">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="stat-value text-xl sm:text-2xl font-bold text-[#24364A]">
                {{ $todayReceiptsCount }} <span class="text-xs font-normal text-[#70746F]">faktur</span>
            </div>
            <div class="stat-trend text-[#39715A]">
                <span>Barang masuk hari ini</span>
            </div>
        </div>

    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-[#D8CDBD]">
        <div class="flex items-center gap-2 sm:gap-6 overflow-x-auto text-xs sm:text-sm font-semibold">
            
            <!-- Tab 1: Rekap Penerimaan Bahan (Selesai) -->
            <a href="{{ route('ingredients.index', ['tab' => 'rekap']) }}" 
               class="pb-3 px-1 transition relative whitespace-nowrap {{ $activeTab === 'rekap' ? 'text-[#24364A] font-bold border-b-2 border-[#24364A]' : 'text-[#70746F] hover:text-[#24364A]' }}">
                Rekap Penerimaan Bahan 
                <span class="ml-1 text-xs px-2 py-0.5 rounded-full {{ $activeTab === 'rekap' ? 'bg-[#24364A]/10 text-[#24364A]' : 'bg-[#E8E1D6]/50 text-[#70746F]' }}">
                    {{ $totalReceiptsCount }}
                </span>
            </a>

            <!-- Tab 2: Menunggu Kedatangan / Siap Diterima -->
            <a href="{{ route('ingredients.index', ['tab' => 'menunggu']) }}" 
               class="pb-3 px-1 transition relative whitespace-nowrap {{ $activeTab === 'menunggu' ? 'text-[#24364A] font-bold border-b-2 border-[#24364A]' : 'text-[#70746F] hover:text-[#24364A]' }}">
                Menunggu Kedatangan 
                @if($waitingArrivalCount > 0)
                    <span class="ml-1 text-xs px-2 py-0.5 rounded-full bg-[#A16A28] text-white">
                        {{ $waitingArrivalCount }}
                    </span>
                @else
                    <span class="ml-1 text-xs px-2 py-0.5 rounded-full bg-[#E8E1D6]/50 text-[#70746F]">0</span>
                @endif
            </a>

            <!-- Tab 3: Master Data Bahan Baku -->
            <a href="{{ route('ingredients.index', ['tab' => 'master']) }}" 
               class="pb-3 px-1 transition relative whitespace-nowrap {{ $activeTab === 'master' ? 'text-[#24364A] font-bold border-b-2 border-[#24364A]' : 'text-[#70746F] hover:text-[#24364A]' }}">
                Master Data Bahan Baku 
                <span class="ml-1 text-xs px-2 py-0.5 rounded-full {{ $activeTab === 'master' ? 'bg-[#24364A]/10 text-[#24364A]' : 'bg-[#E8E1D6]/50 text-[#70746F]' }}">
                    {{ $ingredients->total() }}
                </span>
            </a>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: REKAP PENERIMAAN BAHAN (HISTORI FAKTUR & STOK MASUK)     -->
    <!-- ============================================================== -->
    @if($activeTab === 'rekap')
        
        <!-- Filter Bar -->
        <div class="card p-4 sm:p-5 bg-[#FFFDFA] border border-[#D8CDBD] rounded-2xl shadow-xs">
            <form method="GET" action="{{ route('ingredients.index') }}" class="space-y-4">
                <input type="hidden" name="tab" value="rekap">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search Input -->
                    <div class="relative lg:col-span-2">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#70746F]">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Cari no. faktur, nama supplier, bahan, atau barista..."
                               class="w-full pl-10 pr-4 py-2 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]">
                    </div>

                    <!-- Filter Tanggal Mulai -->
                    <div>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]"
                               title="Tanggal Penerimaan Dari">
                    </div>

                    <!-- Filter Tanggal Selesai -->
                    <div>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]"
                               title="Tanggal Penerimaan Sampai">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-[#D8CDBD]/50 text-xs">
                    <span class="text-[#70746F]">Menampilkan <strong>{{ $completedReceipts->total() }}</strong> catatan penerimaan bahan</span>
                    <div class="flex items-center gap-2">
                        @if($search || $startDate || $endDate)
                            <a href="{{ route('ingredients.index', ['tab' => 'rekap']) }}" 
                               class="px-3 py-1.5 rounded-lg border border-[#D8CDBD] text-[#70746F] hover:bg-[#E8E1D6] transition">
                                Reset Filter
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary py-1.5 px-4 text-xs font-semibold">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Rekap Penerimaan Bahan -->
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Waktu / Tanggal Terima</th>
                        <th>No. Faktur / Invoice</th>
                        <th>Ref. Pengadaan</th>
                        <th>Bahan Baku</th>
                        <th class="text-right">Jumlah Masuk</th>
                        <th>Supplier / Pengirim</th>
                        <th>Barista Penerima</th>
                        <th>Kedaluwarsa (Exp)</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedReceipts as $rcv)
                        <tr class="hover:bg-[#F4F1EB]/40 transition">
                            <!-- Waktu & Tanggal Real-Time -->
                            <td class="whitespace-nowrap">
                                <strong class="text-xs text-[#24364A] block">
                                    {{ $rcv->received_at ? $rcv->received_at->format('d M Y') : $rcv->updated_at->format('d M Y') }}
                                </strong>
                                <div class="flex items-center gap-1.5 text-[11px] text-[#70746F] mt-0.5">
                                    <i data-lucide="clock" class="w-3 h-3 text-[#305785]"></i>
                                    <span class="font-mono font-bold text-[#305785]">
                                        {{ $rcv->received_at ? $rcv->received_at->format('H:i:s') : $rcv->updated_at->format('H:i:s') }}
                                    </span>
                                    <span>WIB</span>
                                </div>
                            </td>

                            <!-- Nomor Invoice -->
                            <td class="whitespace-nowrap">
                                @if($rcv->invoice_number)
                                    <span class="font-mono font-bold text-xs text-[#24364A] bg-[#FAF8F5] border border-[#D8CDBD] px-2 py-0.5 rounded-md">
                                        {{ $rcv->invoice_number }}
                                    </span>
                                @else
                                    <span class="text-xs text-[#B5ACA0] italic">-</span>
                                @endif
                            </td>

                            <!-- Referensi Request Pengadaan -->
                            <td class="whitespace-nowrap">
                                @if(str_starts_with($rcv->request_code, 'REQ-'))
                                    <span class="font-mono font-bold text-xs text-[#305785] bg-[#305785]/10 px-2 py-0.5 rounded-md">
                                        {{ $rcv->request_code }}
                                    </span>
                                @else
                                    <span class="font-mono text-xs text-[#70746F] bg-[#E8E1D6]/60 px-2 py-0.5 rounded-md" title="Penerimaan Langsung">
                                        {{ $rcv->request_code }}
                                    </span>
                                @endif
                            </td>

                            <!-- Bahan Baku & Kategori -->
                            <td>
                                <strong class="text-xs text-[#24364A] block">{{ $rcv->ingredient?->name ?? 'Bahan Dihapus' }}</strong>
                                <span class="badge badge-neutral text-[10px] mt-0.5">
                                    {{ $rcv->ingredient?->category?->name ?? 'Umum' }}
                                </span>
                            </td>

                            <!-- Jumlah Masuk -->
                            <td class="text-right whitespace-nowrap">
                                <span class="font-bold text-xs text-[#39715A] bg-[#EAF4EF] px-2.5 py-1 rounded-lg border border-[#CDE5D8]">
                                    +{{ $rcv->formatted_quantity_received ?? $rcv->formatted_quantity_requested }} {{ $rcv->ingredient?->unit?->symbol ?? '' }}
                                </span>
                            </td>

                            <!-- Supplier -->
                            <td class="whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-xs text-[#24364A]">
                                    <i data-lucide="store" class="w-3.5 h-3.5 text-[#70746F]"></i>
                                    <span>{{ $rcv->supplier_name ?: 'Supplier Langganan' }}</span>
                                </div>
                            </td>

                            <!-- Barista Penerima -->
                            <td class="whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-xs text-[#24364A]">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-[#305785]"></i>
                                    <span class="font-medium">{{ $rcv->receiver?->name ?? ($rcv->user?->name ?? 'Staff') }}</span>
                                </div>
                            </td>

                            <!-- Exp Date -->
                            <td class="whitespace-nowrap text-xs">
                                @if($rcv->expiry_date)
                                    <span class="text-[#24364A] font-semibold">{{ $rcv->expiry_date->format('d M Y') }}</span>
                                @else
                                    <span class="text-[#B5ACA0] italic">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#EAF4EF] text-[#39715A] border border-[#CDE5D8]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#39715A]"></span>
                                    <span>Stok Masuk</span>
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="text-center whitespace-nowrap">
                                <button type="button" 
                                        @click="openReceiptDetailModal({{ json_encode($rcv) }})"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-[#305785] hover:text-[#274A70] bg-[#305785]/10 hover:bg-[#305785]/20 px-2.5 py-1.5 rounded-lg transition">
                                    <i data-lucide="receipt-text" class="w-3.5 h-3.5"></i>
                                    <span>Detail Faktur</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-xs text-[#70746F]">
                                <i data-lucide="package-search" class="w-9 h-9 text-[#B5ACA0] mx-auto mb-2"></i>
                                <strong class="block text-[#24364A] text-sm font-bold mb-1">Belum Ada Riwayat Penerimaan Bahan</strong>
                                <span>Seluruh barang yang telah diterima dari alur request pengadaan maupun penerimaan langsung akan tercatat rapi di sini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($completedReceipts->hasPages())
            <div class="pt-2">
                {{ $completedReceipts->links() }}
            </div>
        @endif

    <!-- ============================================================== -->
    <!-- TAB 2: MENUNGGU KEDATANGAN (PENGADAAN BERJALAN SIAP DITERIMA)  -->
    <!-- ============================================================== -->
    @elseif($activeTab === 'menunggu')

        <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-[#FAF0DD] border border-[#ECDCC7] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#ECDCC7] text-[#A16A28] flex items-center justify-center shrink-0">
                        <i data-lucide="truck" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-serif font-bold text-sm text-[#24364A]">Pengadaan Berjalan Sedang Dalam Pengiriman</h3>
                        <p class="text-xs text-[#70746F] mt-0.5">Daftar pesanan bahan baku yang telah disetujui Owner. Saat kurir / supplier tiba, klik tombol <strong>"Terima Barang"</strong> untuk verifikasi dan pembaruan stok otomatis.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3.5">
                @forelse($waitingArrivals as $item)
                    <div class="card p-5 bg-[#FFFDFA] border border-[#D8CDBD] rounded-2xl shadow-2xs hover:border-[#305785]/50 transition flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        
                        <!-- Left: Avatar + Item Name + Subtitle -->
                        <div class="flex items-center gap-3.5 min-w-[240px]">
                            <div class="w-12 h-12 rounded-xl bg-[#E8E1D6]/70 flex items-center justify-center font-bold text-sm tracking-wider text-[#70746F] shrink-0 border border-[#D8CDBD]/60">
                                {{ $item->initials }}
                            </div>
                            <div>
                                <h3 class="font-bold text-sm sm:text-base text-[#24364A] leading-snug">
                                    {{ $item->ingredient?->name ?? 'Bahan Baku' }}
                                </h3>
                                <p class="text-xs text-[#70746F] mt-0.5">
                                    Pesanan: <strong>{{ $item->formatted_quantity_requested }} {{ $item->ingredient?->unit?->symbol ?? '' }}</strong>
                                </p>
                            </div>
                        </div>

                        <!-- Middle Rincian Pengadaan -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 flex-1 items-center text-xs">
                            <div>
                                <span class="block text-[11px] text-[#70746F]">Kode Request</span>
                                <strong class="font-mono text-[#305785] bg-[#305785]/10 px-2 py-0.5 rounded mt-0.5 inline-block">
                                    {{ $item->request_code }}
                                </strong>
                            </div>

                            <div>
                                <span class="block text-[11px] text-[#70746F]">Supplier</span>
                                <strong class="text-[#24364A] block mt-0.5 truncate">
                                    {{ $item->supplier_name ?: 'Belum ditentukan' }}
                                </strong>
                            </div>

                            <div>
                                <span class="block text-[11px] text-[#70746F]">Estimasi Tiba</span>
                                <strong class="text-[#24364A] block mt-0.5">
                                    {{ $item->estimated_arrival ? $item->estimated_arrival->format('d M Y') : 'Segera' }}
                                </strong>
                            </div>

                            <div>
                                <span class="block text-[11px] text-[#70746F]">Disetujui Oleh</span>
                                <span class="text-[#24364A] font-semibold block mt-0.5">
                                    {{ $item->approver?->name ?? 'Owner' }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Button: Terima Barang -->
                        <div class="flex items-center justify-end gap-2 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-[#D8CDBD]/40">
                            <button type="button" 
                                    @click="openReceiveModal({{ json_encode($item) }})"
                                    class="btn btn-sm bg-[#39715A] hover:bg-[#2F5E4B] text-white px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 shadow-xs transition">
                                <i data-lucide="package-check" class="w-4 h-4"></i>
                                <span>Terima Barang</span>
                            </button>
                        </div>

                    </div>
                @empty
                    <div class="card p-12 text-center bg-[#FFFDFA] border border-[#D8CDBD] rounded-2xl">
                        <i data-lucide="check-circle-2" class="w-10 h-10 text-[#39715A] mx-auto mb-3"></i>
                        <h3 class="font-serif font-bold text-base text-[#24364A]">Tidak Ada Pengadaan Yang Menunggu Kedatangan</h3>
                        <p class="text-xs text-[#70746F] mt-1">Semua pesanan pengadaan telah selesai diterima dan tercatat pada rekapitulasi persediaan.</p>
                        <div class="mt-4">
                            <a href="{{ route('procurement-requests.create') }}" class="btn btn-primary btn-sm inline-flex items-center gap-1.5">
                                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                <span>Buat Request Pengadaan Baru</span>
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

    <!-- ============================================================== -->
    <!-- TAB 3: MASTER DATA BAHAN BAKU (INVENTARIS & AKSI OWNER)       -->
    <!-- ============================================================== -->
    @elseif($activeTab === 'master')

        <!-- Table Master Bahan Baku Container -->
        <div class="bg-[#FFFDFA] rounded-3xl border border-[#D8CDBD] shadow-xs overflow-hidden">
            <div class="p-4 border-b border-[#E8E1D6] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="font-serif font-bold text-sm text-[#24364A]">Daftar Master Bahan Baku</h3>
                    <p class="text-xs text-[#70746F]">Total <strong>{{ $ingredients->total() }}</strong> item bahan baku terdaftar di sistem.</p>
                </div>

                <div class="flex items-center gap-2">
                    @if(auth()->user()->isOwner())
                        <button type="button" 
                                @click="createModal = true"
                                class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-2xs">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Tambah Bahan Baku</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Search Master -->
            <div class="p-4 bg-[#FAF8F5] border-b border-[#D8CDBD]/70">
                <form method="GET" action="{{ route('ingredients.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <input type="hidden" name="tab" value="master">

                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#70746F]">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode bahan..."
                               class="w-full pl-10 pr-4 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <select name="category_id" class="input-select py-2 text-xs w-full sm:w-40">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-secondary btn-sm shrink-0">Filter</button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F4F1EB] border-b border-[#D8CDBD] text-[11px] font-bold text-[#70746F] uppercase tracking-wider">
                            <th class="py-3.5 px-4">Kode</th>
                            <th class="py-3.5 px-4">Nama Bahan Baku</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Satuan Dasar</th>
                            <th class="py-3.5 px-4 text-right">Stok Saat Ini</th>
                            <th class="py-3.5 px-4 text-right">Batas Minimum</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E1D6]">
                        @forelse($ingredients as $ing)
                            <tr class="hover:bg-[#F4F1EB]/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-[#305785]">{{ $ing->code }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-[#24364A] text-sm block">{{ $ing->name }}</span>
                                    @if($ing->notes)
                                        <span class="text-[11px] text-[#70746F] block truncate max-w-xs">{{ $ing->notes }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full bg-[#E8E1D6]/60 text-[#24364A] font-semibold text-[11px]">
                                        {{ $ing->category?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-[#70746F] font-medium">
                                    {{ $ing->unit?->name }} ({{ $ing->unit?->symbol }})
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-sm text-[#24364A]">
                                    {{ number_format($ing->current_stock, 0, ',', '.') }} {{ $ing->unit?->symbol }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-[#70746F] font-medium">
                                    {{ number_format($ing->min_stock, 0, ',', '.') }} {{ $ing->unit?->symbol }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ing->status_badge_class }}">
                                        {{ $ing->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <!-- Tombol Buat Request (Hanya jika stok Menipis atau Habis) -->
                                        @if($ing->current_stock <= $ing->min_stock)
                                            <a href="{{ route('procurement-requests.create', ['ingredient_id' => $ing->id]) }}" 
                                               class="px-2.5 py-1 rounded-lg border border-[#305785]/30 bg-[#305785]/10 text-[#305785] hover:bg-[#305785] hover:text-white transition text-[11px] font-bold flex items-center gap-1 whitespace-nowrap shadow-2xs" 
                                               title="Buat Request Pengadaan untuk {{ $ing->name }}">
                                                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                                <span>Buat Request</span>
                                            </a>
                                        @endif

                                        @if(auth()->user()->isOwner())
                                            <!-- Restock -->
                                            <button type="button" 
                                                    @click="selectedIngredient = {{ json_encode($ing->load('unit')) }}; restockModal = true"
                                                    class="px-2 py-1 rounded-lg border border-[#39715A]/30 bg-[#39715A]/10 text-[#39715A] hover:bg-[#39715A]/20 transition text-[11px] font-bold flex items-center gap-1"
                                                    title="Tambah stok masuk">
                                                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                                <span>Restok</span>
                                            </button>

                                            <!-- Koreksi Opname -->
                                            <button type="button" 
                                                    @click="selectedIngredient = {{ json_encode($ing->load('unit')) }}; adjustModal = true"
                                                    class="p-1.5 rounded-lg border border-[#D8CDBD] text-[#70746F] hover:bg-[#E8E1D6]/40 transition" 
                                                    title="Koreksi stok fisik">
                                                <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                                            </button>

                                            <!-- Edit -->
                                            <button type="button" 
                                                    @click="selectedIngredient = {{ json_encode($ing) }}; editModal = true"
                                                    class="p-1.5 rounded-lg border border-[#D8CDBD] text-[#70746F] hover:bg-[#E8E1D6]/40 transition" 
                                                    title="Edit bahan">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                            </button>

                                            <!-- Hapus -->
                                            <form action="{{ route('ingredients.destroy', $ing) }}" method="POST" onsubmit="return confirm('Hapus bahan {{ $ing->name }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg border border-[#D8CDBD] text-[#A65043] hover:bg-[#A65043]/10 transition" title="Hapus bahan">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-xs text-[#70746F]">
                                    Belum ada data bahan baku yang sesuai dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-[#E8E1D6]">
                {{ $ingredients->links() }}
            </div>
        </div>

    @endif

    <!-- ============================================================== -->
    <!-- MODAL A: PENERIMAAN LANGSUNG (DIRECT GOODS RECEIPT)            -->
    <!-- ============================================================== -->
    <div x-show="directReceiveModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="directReceiveModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl border border-[#D8CDBD]">
            
            <div class="flex items-center justify-between pb-3 border-b border-[#D8CDBD]">
                <div>
                    <h3 class="font-serif text-base font-bold text-[#24364A]">Catat Penerimaan Bahan Langsung</h3>
                    <p class="text-xs text-[#70746F]">Pencatatan pasokan bahan masuk di luar alur pengadaan.</p>
                </div>
                <button @click="directReceiveModal = false" class="p-1.5 text-[#70746F] hover:text-[#24364A] rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('ingredients.direct-receive') }}" method="POST" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-[#24364A] mb-1.5">
                        Pilih Bahan Baku <span class="text-[#A65043]">*</span>
                    </label>
                    <select name="ingredient_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]">
                        <option value="">-- Pilih Bahan Baku --</option>
                        @foreach($allActiveIngredients as $itemIng)
                            <option value="{{ $itemIng->id }}">
                                {{ $itemIng->name }} (Stok saat ini: {{ number_format($itemIng->current_stock, 0, ',', '.') }} {{ $itemIng->unit?->symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-1.5">
                            Jumlah Diterima <span class="text-[#A65043]">*</span>
                        </label>
                        <input type="number" name="quantity_received" step="any" min="0.01" required placeholder="Contoh: 10"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-1.5">
                            Supplier / Toko <span class="text-[#A65043]">*</span>
                        </label>
                        <input type="text" name="supplier_name" required placeholder="Nama supplier / toko pemasok"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-1.5">No. Faktur / Invoice</label>
                        <input type="text" name="invoice_number" placeholder="Contoh: INV-2026/11/001"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Tanggal Kedaluwarsa (Exp)</label>
                        <input type="date" name="expiry_date"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Catatan Penerimaan</label>
                    <textarea name="receipt_notes" rows="2" placeholder="Kondisi barang, tanggal kemasan, dsb..."
                              class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]"></textarea>
                </div>

                <div class="p-3 bg-[#EAF4EF] border border-[#CDE5D8] rounded-xl text-xs text-[#24364A] flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-[#39715A] shrink-0"></i>
                    <span>Stok fisik bahan akan otomatis bertambah saat penerimaan ini disimpan.</span>
                </div>

                <div class="pt-3 border-t border-[#D8CDBD] flex items-center justify-end gap-2">
                    <button type="button" @click="directReceiveModal = false" class="btn btn-secondary btn-sm">Batal</button>
                    <button type="submit" class="btn btn-sm bg-[#305785] text-white hover:bg-[#24364A] flex items-center gap-1.5 font-semibold rounded-xl px-4 py-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Simpan Penerimaan & Tambah Stok</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL B: TERIMA BARANG DARI REQUEST PENGADAAN                  -->
    <!-- ============================================================== -->
    <div x-show="receiveModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="receiveModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl border border-[#D8CDBD]">
            
            <div class="flex items-center justify-between pb-3 border-b border-[#D8CDBD]">
                <div>
                    <h3 class="font-serif text-base font-bold text-[#39715A]">Verifikasi Penerimaan Bahan Pengadaan</h3>
                    <p class="text-xs text-[#70746F]" x-text="activeItem ? activeItem.request_code : ''"></p>
                </div>
                <button @click="receiveModal = false" class="p-1.5 text-[#70746F] hover:text-[#24364A] rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeItem">
                <form :action="'/procurement-requests/' + activeItem.id + '/receive'" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <!-- Alert Info Stok Otomatis Bertambah -->
                    <div class="p-3.5 bg-[#EAF4EF] border border-[#CDE5D8] rounded-xl flex items-center gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-[#39715A] shrink-0"></i>
                        <p class="text-xs text-[#24364A]">
                            Menyimpan penerimaan ini akan <strong>langsung menambah stok fisik</strong> bahan ke sistem dan menandai pengadaan <strong>selesai</strong>.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#24364A] mb-1.5">
                                Jumlah Diterima <span class="text-[#A65043]">*</span>
                            </label>
                            <input type="number" name="quantity_received" step="any" min="0.01" required
                                   :value="activeItem.quantity_requested"
                                   class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#39715A]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Supplier / Pemasok</label>
                            <input type="text" name="supplier_name" :value="activeItem.supplier_name || ''" placeholder="Nama toko / distributor"
                                   class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#39715A]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Nomor Faktur / Invoice</label>
                            <input type="text" name="invoice_number" placeholder="Contoh: INV-2026/11/045"
                                   class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#39715A]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Tanggal Kedaluwarsa (Exp)</label>
                            <input type="date" name="expiry_date"
                                   class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#39715A]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Catatan Penerimaan</label>
                        <textarea name="receipt_notes" rows="2" placeholder="Kondisi kemasan utuh, segel rapi, kualitas baik..."
                                  class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#39715A]"></textarea>
                    </div>

                    <div class="pt-3 border-t border-[#D8CDBD] flex items-center justify-end gap-2">
                        <button type="button" @click="receiveModal = false" class="btn btn-secondary btn-sm">Batal</button>
                        <button type="submit" class="btn btn-sm bg-[#39715A] text-white hover:bg-[#2F5E4B] flex items-center gap-1.5 font-semibold rounded-xl px-4 py-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Simpan Penerimaan & Tambah Stok</span>
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL C: DETAIL FAKTUR PENERIMAAN                              -->
    <!-- ============================================================== -->
    <div x-show="receiptDetailModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="receiptDetailModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-[#D8CDBD]">
            
            <div class="flex items-center justify-between pb-3 border-b border-[#D8CDBD]">
                <div>
                    <h3 class="font-serif text-base font-bold text-[#24364A]">Detail Faktur Penerimaan Bahan</h3>
                    <p class="text-xs text-[#70746F]" x-text="activeReceipt ? activeReceipt.invoice_number || activeReceipt.request_code : ''"></p>
                </div>
                <button @click="receiptDetailModal = false" class="p-1.5 text-[#70746F] hover:text-[#24364A] rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeReceipt">
                <div class="mt-4 space-y-3 text-xs">
                    <div class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl space-y-2">
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Bahan Baku:</span>
                            <strong class="text-[#24364A]" x-text="activeReceipt.ingredient ? activeReceipt.ingredient.name : ''"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Jumlah Masuk:</span>
                            <strong class="text-[#39715A] font-bold" x-text="'+' + (activeReceipt.formatted_quantity_received || activeReceipt.quantity_received) + ' ' + (activeReceipt.ingredient && activeReceipt.ingredient.unit ? activeReceipt.ingredient.unit.symbol : '')"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Supplier:</span>
                            <span class="text-[#24364A]" x-text="activeReceipt.supplier_name || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Nomor Faktur:</span>
                            <span class="font-mono font-bold text-[#24364A]" x-text="activeReceipt.invoice_number || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Barista Penerima:</span>
                            <span class="text-[#24364A] font-medium" x-text="activeReceipt.receiver ? activeReceipt.receiver.name : (activeReceipt.user ? activeReceipt.user.name : 'Staff')"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Tanggal Kedaluwarsa:</span>
                            <span class="text-[#24364A]" x-text="activeReceipt.expiry_date ? activeReceipt.expiry_date.split('T')[0] : '-'"></span>
                        </div>
                    </div>

                    <div x-show="activeReceipt.receipt_notes" class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl">
                        <span class="text-[#70746F] block font-semibold mb-1">Catatan Penerimaan:</span>
                        <p class="text-[#24364A] leading-relaxed" x-text="activeReceipt.receipt_notes"></p>
                    </div>

                    <div class="pt-3 border-t border-[#D8CDBD] flex items-center justify-end">
                        <button type="button" @click="receiptDetailModal = false" class="btn btn-primary btn-sm">Tutup</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODALS MASTER DATA BAHAN BAKU (PRESERVED FOR OWNER)             -->
    <!-- ============================================================== -->
    <!-- Modal Tambah Bahan Baru -->
    <div x-show="createModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="createModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-[#D8CDBD]">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8E1D6]">
                <h3 class="font-serif font-bold text-base text-[#24364A]">Tambah Bahan Baku Baru</h3>
                <button @click="createModal = false" class="text-[#70746F] hover:text-[#24364A]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('ingredients.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Kode Bahan *</label>
                        <input type="text" name="code" required placeholder="Contoh: ING-UHT"
                               class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs font-mono uppercase bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Kategori *</label>
                        <select name="category_id" required class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Nama Bahan Baku *</label>
                    <input type="text" name="name" required placeholder="Contoh: Susu UHT Fresh Milk"
                           class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Satuan Dasar *</label>
                        <select name="unit_id" required class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Stok Awal *</label>
                        <input type="number" step="any" min="0" name="current_stock" value="0" required
                               class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Batas Minimum *</label>
                        <input type="number" step="any" min="0" name="min_stock" value="1000" required
                               class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Catatan</label>
                    <textarea name="notes" rows="2" placeholder="Spesifikasi merk atau penyimpanan..."
                              class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="createModal = false" class="btn btn-secondary btn-sm">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Bahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Bahan -->
    <div x-show="editModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="editModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-[#D8CDBD]">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8E1D6]">
                <h3 class="font-serif font-bold text-base text-[#24364A]">Edit Data Bahan Baku</h3>
                <button @click="editModal = false" class="text-[#70746F] hover:text-[#24364A]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'/ingredients/' + selectedIngredient?.id" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Kode Bahan *</label>
                        <input type="text" name="code" :value="selectedIngredient?.code" required
                               class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs font-mono uppercase bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Kategori *</label>
                        <select name="category_id" x-model="selectedIngredient.category_id" required class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Nama Bahan Baku *</label>
                    <input type="text" name="name" :value="selectedIngredient?.name" required
                            class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Satuan Dasar *</label>
                        <select name="unit_id" x-model="selectedIngredient.unit_id" required class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Batas Minimum Stok *</label>
                        <input type="number" step="any" min="0" name="min_stock" :value="selectedIngredient?.min_stock" required
                               class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Catatan</label>
                    <textarea name="notes" rows="2" :value="selectedIngredient?.notes"
                              class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="editModal = false" class="btn btn-secondary btn-sm">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Restok / Tambah Stok Manual -->
    <div x-show="restockModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="restockModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#D8CDBD]">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8E1D6]">
                <h3 class="font-serif font-bold text-base text-[#24364A]">Tambah Stok Masuk</h3>
                <button @click="restockModal = false" class="text-[#70746F] hover:text-[#24364A]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'/ingredients/' + selectedIngredient?.id + '/restock'" method="POST" class="mt-4 space-y-4">
                @csrf
                <div class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl text-xs space-y-1">
                    <span class="text-[#70746F]">Bahan Baku:</span>
                    <strong class="text-[#24364A] block" x-text="selectedIngredient?.name"></strong>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Jumlah Stok Masuk *</label>
                    <input type="number" step="any" min="0.01" name="quantity" required
                           class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan opsional..."
                              class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="restockModal = false" class="btn btn-secondary btn-sm">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Konfirmasi Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Koreksi Opname -->
    <div x-show="adjustModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="adjustModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#D8CDBD]">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8E1D6]">
                <h3 class="font-serif font-bold text-base text-[#24364A]">Koreksi Stok Fisik (Stock Opname)</h3>
                <button @click="adjustModal = false" class="text-[#70746F] hover:text-[#24364A]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'/ingredients/' + selectedIngredient?.id + '/adjust'" method="POST" class="mt-4 space-y-4">
                @csrf
                <div class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl text-xs space-y-1">
                    <span class="text-[#70746F]">Bahan Baku:</span>
                    <strong class="text-[#24364A] block" x-text="selectedIngredient?.name"></strong>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Stok Aktual Hasil Opname *</label>
                    <input type="number" step="any" min="0" name="target_stock" required
                           class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Alasan Penyesuaian *</label>
                    <textarea name="reason" rows="2" required placeholder="Contoh: Selisih penghitungan fisik opname..."
                              class="w-full px-3 py-2 rounded-xl border border-[#D8CDBD] text-xs bg-[#FFFDFA] focus:ring-2 focus:ring-[#305785]"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="adjustModal = false" class="btn btn-secondary btn-sm">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Koreksi</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function penerimaanBahanApp() {
        return {
            directReceiveModal: false,
            receiveModal: false,
            receiptDetailModal: false,
            createModal: false,
            editModal: false,
            restockModal: false,
            adjustModal: false,
            activeItem: null,
            activeReceipt: null,
            selectedIngredient: null,

            openReceiveModal(item) {
                this.activeItem = item;
                this.receiveModal = true;
                this.$nextTick(() => { if (window.createIcons) window.createIcons(); });
            },

            openReceiptDetailModal(rcv) {
                this.activeReceipt = rcv;
                this.receiptDetailModal = true;
                this.$nextTick(() => { if (window.createIcons) window.createIcons(); });
            }
        };
    }
</script>
@endpush
