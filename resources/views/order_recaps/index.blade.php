@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <span class="text-xs text-[#70746F]">Rekap Pesanan</span>
        <span class="separator text-[#B5ACA0] mx-1">/</span>
        <span class="text-xs font-semibold text-[#24364A]">Riwayat Rekap</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Riwayat Rekap Pesanan Kasir</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Arsip histori batch input transaksi kasir dan mutasi pemakaian bahan baku.</p>
        </div>

        @if(auth()->user()->isBarista())
            <a href="{{ route('order-recaps.create') }}" 
               class="btn btn-primary btn-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Input Rekap Baru</span>
            </a>
        @endif
    </div>

    <!-- Filter Card -->
    <div class="card p-4">
        <form method="GET" action="{{ route('order-recaps.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#70746F]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor transaksi, referensi kasir, atau nama barista..."
                       class="input-text pl-10 pr-4 py-2 text-xs sm:text-sm">
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <input type="date" name="date" value="{{ $date }}"
                       class="input-text w-full md:w-40 py-2 text-xs">

                <select name="shift" class="input-select w-full md:w-32 py-2 text-xs">
                    <option value="">Semua Shift</option>
                    <option value="pagi" {{ $shift === 'pagi' ? 'selected' : '' }}>Shift Pagi</option>
                    <option value="siang" {{ $shift === 'siang' ? 'selected' : '' }}>Shift Siang</option>
                    <option value="malam" {{ $shift === 'malam' ? 'selected' : '' }}>Shift Malam</option>
                </select>

                <select name="status" class="input-select w-full md:w-32 py-2 text-xs">
                    <option value="">Semua Status</option>
                    <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Terkirim</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm shrink-0">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter</span>
                </button>

                @if($search || $date || $shift || $status)
                    <a href="{{ route('order-recaps.index') }}" class="btn btn-ghost btn-sm shrink-0" title="Reset filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Recaps Table -->
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Nomor Rekap</th>
                    <th>Tanggal & Shift</th>
                    <th>Referensi Kasir</th>
                    <th>Total Menu</th>
                    <th>Direkap Oleh</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recaps as $recap)
                    <tr>
                        <td>
                            <strong class="text-[#305785] text-sm block">{{ $recap->transaction_code }}</strong>
                        </td>
                        <td>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-xs text-[#24364A] font-semibold">{{ $recap->transaction_date->format('d M Y') }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $recap->shift_badge_class }}">
                                    Shift {{ ucfirst($recap->shift ?? 'pagi') }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <code class="text-xs bg-[#FAF8F5] px-1.5 py-0.5 rounded border border-[#D8CDBD]">{{ $recap->notes ?? 'TRX-POS-1042..1078' }}</code>
                        </td>
                        <td>
                            <strong class="text-xs text-[#24364A]">{{ $recap->total_items_quantity }} Cup</strong>
                            <span class="text-[11px] text-[#70746F]">({{ $recap->total_menus_count }} variasi)</span>
                        </td>
                        <td>
                            <span class="text-xs font-semibold text-[#24364A] block">{{ $recap->user?->name ?? 'Barista' }}</span>
                            <span class="text-[10px] text-[#70746F] capitalize">{{ $recap->user?->role }}</span>
                        </td>
                        <td>
                            @if($recap->isSubmitted())
                                <span class="badge badge-safe"><span class="badge-dot"></span>Tersimpan</span>
                            @else
                                <span class="badge badge-danger"><span class="badge-dot"></span>Dibatalkan</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('order-recaps.show', $recap) }}" class="btn btn-secondary btn-sm">
                                <i data-lucide="eye" class="w-3.5 h-3.5 text-[#305785]"></i>
                                <span>Detail</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-[#70746F] text-xs">
                            <i data-lucide="clipboard-x" class="w-8 h-8 text-[#B5ACA0] mx-auto mb-2"></i>
                            <p class="font-bold">Belum ada data rekap pesanan yang tersimpan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($recaps->hasPages())
            <div class="pagination-wrapper">
                {{ $recaps->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
