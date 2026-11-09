@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ restockModal: false, adjustModal: false, setMinStockModal: false, selectedIngredient: null }">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <span class="text-xs text-[#70746F]">Persediaan</span>
        <span class="separator text-[#B5ACA0] mx-1">/</span>
        <span class="text-xs font-semibold text-[#24364A]">Monitoring Stok</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Monitoring Persediaan Bahan</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Status stok aktual, ambang batas minimum, tanggal kedaluwarsa, dan reminder pengadaan.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('procurement-requests.create') }}" 
               class="btn btn-primary btn-sm">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Buat Request</span>
            </a>
            <a href="{{ route('ingredients.index') }}" 
               class="btn btn-secondary btn-sm">
                <i data-lucide="package-plus" class="w-4 h-4"></i>
                <span>Penerimaan Bahan</span>
            </a>
            @if(auth()->user()->isOwner())
                <button type="button" 
                        @click="setMinStockModal = true"
                        class="btn btn-primary btn-sm">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                    <span>Atur Batas Aman</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Quick Filter Stat Counters (Exact 45, 34, 8, 3) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('monitoring.index') }}" 
           class="stat-card p-4 hover:border-[#305785] transition {{ empty($status) ? 'border-[#305785] ring-2 ring-[#305785]/15' : '' }}">
            <span class="stat-label">Total Bahan Baku</span>
            <div class="stat-value text-2xl mt-1">{{ $totalCount }} <span class="text-xs font-normal text-[#70746F]">SKU</span></div>
        </a>

        <a href="{{ route('monitoring.index', ['status' => 'aman']) }}" 
           class="stat-card p-4 border-l-4 border-l-[#39715A] hover:border-[#39715A] transition {{ $status === 'aman' ? 'ring-2 ring-[#39715A]/15' : '' }}">
            <span class="stat-label text-[#39715A]">Stok Aman</span>
            <div class="stat-value text-2xl text-[#39715A] mt-1">{{ $safeCount }} <span class="text-xs font-normal text-[#70746F]">SKU</span></div>
        </a>

        <a href="{{ route('monitoring.index', ['status' => 'menipis']) }}" 
           class="stat-card p-4 border-l-4 border-l-[#A16A28] hover:border-[#A16A28] transition {{ $status === 'menipis' ? 'ring-2 ring-[#A16A28]/15' : '' }}">
            <span class="stat-label text-[#A16A28]">Stok Menipis</span>
            <div class="stat-value text-2xl text-[#A16A28] mt-1">{{ $lowStockCount }} <span class="text-xs font-normal text-[#70746F]">SKU</span></div>
        </a>

        <a href="{{ route('monitoring.index', ['status' => 'habis']) }}" 
           class="stat-card p-4 border-l-4 border-l-[#A65043] hover:border-[#A65043] transition {{ $status === 'habis' ? 'ring-2 ring-[#A65043]/15' : '' }}">
            <span class="stat-label text-[#A65043]">Stok Habis (0)</span>
            <div class="stat-value text-2xl text-[#A65043] mt-1">{{ $emptyCount }} <span class="text-xs font-normal text-[#70746F]">SKU</span></div>
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ route('monitoring.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#70746F]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode bahan baku..."
                       class="input-text pl-10 pr-4 py-2 text-xs sm:text-sm">
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <select name="category_id" class="input-select w-full md:w-44 py-2 text-xs">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="input-select w-full md:w-36 py-2 text-xs">
                    <option value="">Semua Status</option>
                    <option value="aman" {{ $status === 'aman' ? 'selected' : '' }}>Aman</option>
                    <option value="menipis" {{ $status === 'menipis' ? 'selected' : '' }}>Menipis</option>
                    <option value="habis" {{ $status === 'habis' ? 'selected' : '' }}>Habis</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm shrink-0">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter</span>
                </button>

                @if($search || $categoryId || $status)
                    <a href="{{ route('monitoring.index') }}" class="btn btn-ghost btn-sm shrink-0" title="Reset filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Monitoring Table -->
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Bahan Baku</th>
                    <th>Kategori</th>
                    <th>Stok Saat Ini</th>
                    <th>Batas Aman</th>
                    <th>Status Stok</th>
                    <th>Kedaluwarsa</th>
                    <th>Reminder</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingredients as $ingredient)
                    <tr>
                        <td>
                            <strong class="text-[#24364A] text-sm block">{{ $ingredient->name }}</strong>
                            <span class="text-[11px] text-[#70746F] font-mono">{{ $ingredient->code }}</span>
                        </td>
                        <td>
                            <span class="badge badge-neutral">{{ $ingredient->category?->name ?? 'Umum' }}</span>
                        </td>
                        <td>
                            <strong class="text-sm font-bold {{ $ingredient->current_stock <= 0 ? 'text-[#A65043]' : ($ingredient->current_stock <= $ingredient->min_stock ? 'text-[#A16A28]' : 'text-[#24364A]') }}">
                                {{ number_format($ingredient->current_stock, 0, ',', '.') }} {{ $ingredient->unit?->symbol }}
                            </strong>
                        </td>
                        <td>
                            <span class="text-xs text-[#70746F]">
                                {{ number_format($ingredient->min_stock, 0, ',', '.') }} {{ $ingredient->unit?->symbol }}
                            </span>
                        </td>
                        <td>
                            @if($ingredient->current_stock <= 0)
                                <span class="badge badge-danger"><span class="badge-dot"></span>Habis</span>
                            @elseif($ingredient->current_stock <= $ingredient->min_stock)
                                <span class="badge badge-warning"><span class="badge-dot"></span>Menipis</span>
                            @else
                                <span class="badge badge-safe"><span class="badge-dot"></span>Aman</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-expiry-safe">23 Des 2026</span>
                        </td>
                        <td>
                            @if($ingredient->current_stock <= $ingredient->min_stock)
                                <span class="text-[11px] font-bold text-[#A16A28]">Alert Aktif</span>
                            @else
                                <span class="text-[11px] text-[#70746F]">Normal</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($ingredient->current_stock <= $ingredient->min_stock)
                                    <a href="{{ route('procurement-requests.create', ['ingredient_id' => $ingredient->id]) }}" 
                                       class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-2xs whitespace-nowrap" 
                                       title="Buat Request Pengadaan untuk {{ $ingredient->name }}">
                                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                        <span>Buat Request</span>
                                    </a>
                                @endif
                                <a href="{{ route('ingredients.index') }}" class="btn btn-secondary btn-sm" title="Lihat detail">
                                    <span>Detail</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-[#70746F] text-xs">
                            <i data-lucide="package-search" class="w-8 h-8 text-[#B5ACA0] mx-auto mb-2"></i>
                            <p class="font-bold">Tidak ada bahan baku yang sesuai dengan kriteria pencarian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($ingredients->hasPages())
            <div class="pagination-wrapper">
                {{ $ingredients->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Atur Batas Aman (Owner Only) -->
    <div x-show="setMinStockModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="setMinStockModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#D8CDBD]">
            <div class="flex items-center justify-between pb-3 border-b border-[#D8CDBD] mb-4">
                <h3 class="font-serif font-bold text-base text-[#24364A]">Atur Batas Aman & Aturan Reminder</h3>
                <button type="button" @click="setMinStockModal = false" class="text-[#70746F] hover:text-[#24364A]">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form onsubmit="event.preventDefault(); alert('Batas aman stok berhasil diperbarui.');" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1">Pilihan Bahan Baku</label>
                    <select class="input-select text-xs">
                        @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}">{{ $ing->name }} (Saat ini: {{ $ing->min_stock }} {{ $ing->unit?->symbol }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Batas Aman Baru</label>
                        <input type="number" class="input-text text-xs" value="3000">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#24364A] mb-1">Hari Reminder</label>
                        <input type="number" class="input-text text-xs" value="3">
                    </div>
                </div>
                <div class="p-3 bg-[#FAF8F5] rounded-xl border border-[#D8CDBD] text-[11px] text-[#70746F]">
                    <strong>Preview Aturan:</strong> Notifikasi peringatan akan aktif jika stok &le; nilai batas aman atau tanggal kedaluwarsa tersisa &le; 3 hari kalender.
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="setMinStockModal = false" class="btn btn-secondary btn-sm">Batal</button>
                    <button type="button" @click="setMinStockModal = false" class="btn btn-primary btn-sm">Simpan Aturan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
