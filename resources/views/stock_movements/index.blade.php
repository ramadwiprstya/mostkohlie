@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <span class="text-xs text-[#70746F]">Persediaan</span>
        <span class="separator text-[#B5ACA0] mx-1">/</span>
        <span class="text-xs font-semibold text-[#24364A]">Riwayat Stok</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Riwayat Mutasi Stok</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Buku log keluar-masuk persediaan: pemakaian resep, penerimaan supplier, dan penyesuaian fisik.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="alert('Mengunduh data riwayat mutasi stok (CSV/Excel)...');" class="btn btn-secondary btn-sm">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export Riwayat</span>
            </button>
        </div>
    </div>

    <!-- Summary Statistics Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="stat-card p-4">
            <span class="stat-label">Aktivitas Tercatat</span>
            <div class="stat-value text-2xl mt-1">{{ $movements->total() }} <span class="text-xs font-normal text-[#70746F]">kali</span></div>
        </div>

        <div class="stat-card p-4 border-l-4 border-l-[#39715A]">
            <span class="stat-label text-[#39715A]">Total Stok Masuk</span>
            <div class="stat-value text-2xl text-[#39715A] mt-1">+420 L <span class="text-xs font-normal text-[#70746F]">/ 95 Kg</span></div>
        </div>

        <div class="stat-card p-4 border-l-4 border-l-[#A65043]">
            <span class="stat-label text-[#A65043]">Total Stok Keluar</span>
            <div class="stat-value text-2xl text-[#A65043] mt-1">-380 L <span class="text-xs font-normal text-[#70746F]">/ 82 Kg</span></div>
        </div>

        <div class="stat-card p-4 border-l-4 border-l-[#A16A28]">
            <span class="stat-label text-[#A16A28]">Penyesuaian Fisik</span>
            <div class="stat-value text-2xl text-[#A16A28] mt-1">+5 L <span class="text-xs font-normal text-[#70746F]">koreksi</span></div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4">
        <form method="GET" action="{{ route('stock-movements.index') }}" class="flex flex-col lg:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#70746F]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode mutasi, nama bahan, atau referensi..."
                       class="input-text pl-10 pr-4 py-2 text-xs sm:text-sm">
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 w-full lg:w-auto">
                <select name="ingredient_id" class="input-select py-2 text-xs">
                    <option value="">Semua Bahan</option>
                    @foreach($ingredients as $ing)
                        <option value="{{ $ing->id }}" {{ $ingredientId == $ing->id ? 'selected' : '' }}>
                            {{ $ing->name }}
                        </option>
                    @endforeach
                </select>

                <select name="type" class="input-select py-2 text-xs">
                    <option value="">Semua Aktivitas</option>
                    <option value="recap_usage" {{ $type === 'recap_usage' ? 'selected' : '' }}>Pemakaian Resep</option>
                    <option value="restock" {{ $type === 'restock' ? 'selected' : '' }}>Penerimaan Restok</option>
                    <option value="adjustment_in" {{ $type === 'adjustment_in' ? 'selected' : '' }}>Koreksi (Masuk)</option>
                    <option value="adjustment_out" {{ $type === 'adjustment_out' ? 'selected' : '' }}>Koreksi (Keluar)</option>
                </select>

                <input type="date" name="start_date" value="{{ $startDate }}" placeholder="Dari tanggal"
                       class="input-text py-2 text-xs">

                <input type="date" name="end_date" value="{{ $endDate }}" placeholder="Sampai tanggal"
                       class="input-text py-2 text-xs">
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter</span>
                </button>
                @if($search || $ingredientId || $type || $startDate || $endDate)
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-ghost btn-sm" title="Reset filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Stock Movements -->
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Tanggal & Waktu</th>
                    <th>Nama Bahan</th>
                    <th>Jenis Aktivitas</th>
                    <th>Perubahan (+ / -)</th>
                    <th>Stok Akhir</th>
                    <th>Dicatat Oleh</th>
                    <th>Referensi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $mov)
                    <tr>
                        <td>
                            <span class="text-xs text-[#24364A] font-semibold block">{{ $mov->movement_date->format('d M Y') }}</span>
                            <span class="text-[11px] text-[#70746F]">{{ $mov->movement_date->format('H:i') }} WIB</span>
                        </td>
                        <td>
                            <strong class="text-xs text-[#24364A] block">{{ $mov->ingredient?->name }}</strong>
                            <span class="text-[10px] text-[#70746F] font-mono">{{ $mov->ingredient?->code }}</span>
                        </td>
                        <td>
                            @if($mov->type === 'restock' || $mov->type === 'adjustment_in')
                                <span class="badge badge-safe">Penerimaan</span>
                            @elseif($mov->type === 'recap_usage')
                                <span class="badge badge-warning">Pemakaian Resep</span>
                            @else
                                <span class="badge badge-neutral">Penyesuaian</span>
                            @endif
                        </td>
                        <td>
                            @if($mov->quantity_in > 0)
                                <strong class="text-xs font-bold text-[#39715A]">
                                    +{{ number_format($mov->quantity_in, 0, ',', '.') }} {{ $mov->ingredient?->unit?->symbol }}
                                </strong>
                            @else
                                <strong class="text-xs font-bold text-[#A65043]">
                                    -{{ number_format($mov->quantity_out, 0, ',', '.') }} {{ $mov->ingredient?->unit?->symbol }}
                                </strong>
                            @endif
                        </td>
                        <td>
                            <span class="text-xs font-bold text-[#24364A]">
                                {{ number_format($mov->stock_after, 0, ',', '.') }} {{ $mov->ingredient?->unit?->symbol }}
                            </span>
                        </td>
                        <td>
                            <span class="text-xs text-[#24364A] block">{{ $mov->user?->name ?? 'Sistem' }}</span>
                            <span class="text-[10px] text-[#70746F] capitalize">{{ $mov->user?->role }}</span>
                        </td>
                        <td>
                            <code class="text-[11px] bg-[#FAF8F5] px-1.5 py-0.5 rounded border border-[#D8CDBD]">
                                {{ $mov->reference_type ? class_basename($mov->reference_type) . ' #' . $mov->reference_id : $mov->movement_code }}
                            </code>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-[#70746F] text-xs">
                            <i data-lucide="history" class="w-8 h-8 text-[#B5ACA0] mx-auto mb-2"></i>
                            <p class="font-bold">Belum ada riwayat mutasi stok yang tercatat.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($movements->hasPages())
            <div class="pagination-wrapper">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
