@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#70746F]">
        <span>Lainnya</span>
        <span>/</span>
        <span class="font-semibold text-[#24364A]">Laporan Persediaan & Operasional</span>
    </nav>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A]">Laporan Persediaan & Operasional</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Ringkasan valuasi stok gudang, tren pemakaian bahan baku, dan riwayat realisasi pengadaan.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('reports.export', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}" 
               class="px-4 py-2.5 rounded-xl border border-[#D8CDBD] bg-[#FFFDFA] hover:bg-[#E8E1D6]/40 text-[#24364A] text-xs font-bold transition shadow-xs flex items-center gap-2">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#39715A]"></i>
                <span>Export Excel</span>
            </a>
            <button type="button" onclick="window.print()"
                    class="px-4 py-2.5 rounded-xl border border-[#D8CDBD] bg-[#FFFDFA] hover:bg-[#E8E1D6]/40 text-[#24364A] text-xs font-bold transition shadow-xs flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4 text-[#A65043]"></i>
                <span>Cetak / PDF</span>
            </button>
        </div>
    </div>

    <!-- 3 Summary KPIs (Valuasi, COGS, Realisasi Pengadaan) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[#FFFDFA] p-5 rounded-2xl border border-[#D8CDBD] shadow-xs relative overflow-hidden" style="border-top: 4px solid #305785;">
            <span class="text-xs font-bold text-[#70746F] block mb-1">Total Nilai Persediaan Aktif</span>
            <div class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] mb-1">Rp 48.650.000</div>
            <div class="text-[11px] text-[#70746F] flex items-center gap-1.5">
                <i data-lucide="boxes" class="w-3.5 h-3.5 text-[#305785]"></i>
                <span>Valuasi 45 item di chiller & gudang</span>
            </div>
        </div>

        <div class="bg-[#FFFDFA] p-5 rounded-2xl border border-[#D8CDBD] shadow-xs relative overflow-hidden" style="border-top: 4px solid #A16A28;">
            <span class="text-xs font-bold text-[#70746F] block mb-1">Total Pemakaian Bahan (COGS)</span>
            <div class="font-serif text-2xl sm:text-3xl font-bold text-[#A16A28] mb-1">Rp 14.820.000</div>
            <div class="text-[11px] text-[#39715A] font-semibold flex items-center gap-1.5">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                <span>Efisiensi resep 97.4%</span>
            </div>
        </div>

        <div class="bg-[#FFFDFA] p-5 rounded-2xl border border-[#D8CDBD] shadow-xs relative overflow-hidden" style="border-top: 4px solid #39715A;">
            <span class="text-xs font-bold text-[#70746F] block mb-1">Total Realisasi Pengadaan</span>
            <div class="font-serif text-2xl sm:text-3xl font-bold text-[#39715A] mb-1">Rp 18.500.000</div>
            <div class="text-[11px] text-[#70746F] flex items-center gap-1.5">
                <i data-lucide="file-check" class="w-3.5 h-3.5 text-[#39715A]"></i>
                <span>4 invoice supplier terlunasi</span>
            </div>
        </div>
    </div>

    <!-- Trend Chart Card -->
    <div class="bg-[#FFFDFA] p-6 rounded-3xl border border-[#D8CDBD] shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-serif text-base font-bold text-[#24364A]">Pergerakan Nilai Persediaan & Pengeluaran Bahan</h3>
                <p class="text-xs text-[#70746F] mt-0.5">Tren fluktuasi mingguan bulan Oktober 2026</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#E8E1D6]/60 text-[#305785]">
                Bulan Ini
            </span>
        </div>

        <div class="h-48 w-full relative">
            <svg viewBox="0 0 700 180" class="w-full h-full">
                <line x1="50" y1="30" x2="680" y2="30" stroke="#E8E1D6" stroke-dasharray="3" />
                <line x1="50" y1="75" x2="680" y2="75" stroke="#E8E1D6" stroke-dasharray="3" />
                <line x1="50" y1="120" x2="680" y2="120" stroke="#E8E1D6" stroke-dasharray="3" />

                <!-- Bars for Weekly COGS -->
                <rect x="110" y="55" width="40" height="75" fill="#305785" rx="4" opacity="0.9" />
                <rect x="250" y="40" width="40" height="90" fill="#305785" rx="4" opacity="0.9" />
                <rect x="390" y="32" width="40" height="98" fill="#305785" rx="4" opacity="0.9" />
                <rect x="530" y="48" width="40" height="82" fill="#4C7499" rx="4" opacity="0.75" />

                <!-- Value Annotations -->
                <text x="115" y="47" font-size="11" font-weight="bold" fill="#305785">3.4jt</text>
                <text x="255" y="32" font-size="11" font-weight="bold" fill="#305785">4.1jt</text>
                <text x="395" y="24" font-size="11" font-weight="bold" fill="#305785">4.6jt</text>
                <text x="535" y="40" font-size="11" font-weight="bold" fill="#4C7499">3.8jt</text>

                <!-- Labels -->
                <text x="110" y="150" font-size="11.5" font-weight="600" fill="#70746F">Minggu 1</text>
                <text x="250" y="150" font-size="11.5" font-weight="600" fill="#70746F">Minggu 2</text>
                <text x="390" y="150" font-size="11.5" font-weight="600" fill="#70746F">Minggu 3</text>
                <text x="525" y="150" font-size="11.5" font-weight="600" fill="#70746F">Minggu 4 (Est)</text>
            </svg>
        </div>
    </div>

    <!-- Report Type Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        <a href="{{ route('reports.index', ['type' => 'current_stock', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="px-4 py-2.5 rounded-xl transition font-bold whitespace-nowrap flex items-center gap-2 {{ $type === 'current_stock' ? 'bg-[#305785] text-white shadow-xs' : 'bg-[#FFFDFA] text-[#70746F] border border-[#D8CDBD] hover:bg-[#E8E1D6]/40' }}">
            <i data-lucide="boxes" class="w-4 h-4"></i>
            <span>Stok Saat Ini</span>
        </a>

        <a href="{{ route('reports.index', ['type' => 'critical_stock', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="px-4 py-2.5 rounded-xl transition font-bold whitespace-nowrap flex items-center gap-2 {{ $type === 'critical_stock' ? 'bg-[#305785] text-white shadow-xs' : 'bg-[#FFFDFA] text-[#70746F] border border-[#D8CDBD] hover:bg-[#E8E1D6]/40' }}">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            <span>Menipis & Habis</span>
        </a>

        <a href="{{ route('reports.index', ['type' => 'stock_in', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="px-4 py-2.5 rounded-xl transition font-bold whitespace-nowrap flex items-center gap-2 {{ $type === 'stock_in' ? 'bg-[#305785] text-white shadow-xs' : 'bg-[#FFFDFA] text-[#70746F] border border-[#D8CDBD] hover:bg-[#E8E1D6]/40' }}">
            <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
            <span>Stok Masuk (Restock)</span>
        </a>

        <a href="{{ route('reports.index', ['type' => 'usage', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="px-4 py-2.5 rounded-xl transition font-bold whitespace-nowrap flex items-center gap-2 {{ $type === 'usage' ? 'bg-[#305785] text-white shadow-xs' : 'bg-[#FFFDFA] text-[#70746F] border border-[#D8CDBD] hover:bg-[#E8E1D6]/40' }}">
            <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
            <span>Pemakaian Bahan</span>
        </a>

        <a href="{{ route('reports.index', ['type' => 'top_used', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="px-4 py-2.5 rounded-xl transition font-bold whitespace-nowrap flex items-center gap-2 {{ $type === 'top_used' ? 'bg-[#305785] text-white shadow-xs' : 'bg-[#FFFDFA] text-[#70746F] border border-[#D8CDBD] hover:bg-[#E8E1D6]/40' }}">
            <i data-lucide="award" class="w-4 h-4"></i>
            <span>Bahan Terbanyak</span>
        </a>

        <a href="{{ route('reports.index', ['type' => 'recaps', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="px-4 py-2.5 rounded-xl transition font-bold whitespace-nowrap flex items-center gap-2 {{ $type === 'recaps' ? 'bg-[#305785] text-white shadow-xs' : 'bg-[#FFFDFA] text-[#70746F] border border-[#D8CDBD] hover:bg-[#E8E1D6]/40' }}">
            <i data-lucide="clipboard-check" class="w-4 h-4"></i>
            <span>Rekap Pesanan</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-[#FFFDFA] rounded-2xl p-4 border border-[#D8CDBD] shadow-xs">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <input type="hidden" name="type" value="{{ $type }}">

            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#70746F]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Pencarian kode atau nama bahan laporan..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#D8CDBD] text-xs sm:text-sm text-[#24364A] bg-[#F4F1EB]/40 focus:outline-none focus:ring-2 focus:ring-[#305785]">
            </div>

            @if(in_array($type, ['stock_in', 'usage', 'top_used', 'recaps']))
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="date" name="start_date" value="{{ $startDate }}"
                           class="w-full sm:w-36 py-2.5 px-3 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                    <span class="text-xs text-[#70746F]">s/d</span>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                           class="w-full sm:w-36 py-2.5 px-3 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#F4F1EB]/40 focus:ring-2 focus:ring-[#305785]">
                </div>
            @endif

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#305785] hover:bg-[#274A70] text-white text-xs font-bold transition shrink-0 shadow-xs flex items-center gap-2">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Terapkan Filter</span>
            </button>
        </form>
    </div>

    <!-- Report Table Content -->
    <div class="bg-[#FFFDFA] rounded-3xl border border-[#D8CDBD] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            
            @if(in_array($type, ['current_stock', 'critical_stock']))
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F4F1EB] border-b border-[#D8CDBD] text-[11px] font-bold text-[#70746F] uppercase tracking-wider">
                            <th class="py-3.5 px-4">Kode Bahan</th>
                            <th class="py-3.5 px-4">Nama Bahan Baku</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4 text-right">Stok Saat Ini</th>
                            <th class="py-3.5 px-4 text-right">Batas Minimum</th>
                            <th class="py-3.5 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E1D6]">
                        @forelse($data as $ing)
                            <tr class="hover:bg-[#F4F1EB]/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-[#305785]">{{ $ing->code }}</td>
                                <td class="py-3.5 px-4 font-bold text-[#24364A]">{{ $ing->name }}</td>
                                <td class="py-3.5 px-4 text-[#70746F]">{{ $ing->category?->name ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-right font-black text-[#24364A]">
                                    {{ number_format($ing->current_stock, 0) }} {{ $ing->unit?->symbol }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-[#70746F] font-medium">
                                    {{ number_format($ing->min_stock, 0) }} {{ $ing->unit?->symbol }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ing->status_badge_class }}">
                                        {{ $ing->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-xs text-[#70746F]">Tidak ada data untuk laporan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($type === 'stock_in')
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F4F1EB] border-b border-[#D8CDBD] text-[11px] font-bold text-[#70746F] uppercase tracking-wider">
                            <th class="py-3.5 px-4">Kode Mutasi</th>
                            <th class="py-3.5 px-4">Tanggal & Jam</th>
                            <th class="py-3.5 px-4">Bahan Baku</th>
                            <th class="py-3.5 px-4">Jenis Masuk</th>
                            <th class="py-3.5 px-4 text-right">Jumlah Masuk</th>
                            <th class="py-3.5 px-4 text-right">Stok Sebelum</th>
                            <th class="py-3.5 px-4 text-right">Stok Sesudah</th>
                            <th class="py-3.5 px-4">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E1D6]">
                        @forelse($data as $m)
                            <tr class="hover:bg-[#F4F1EB]/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-[#305785]">{{ $m->movement_code }}</td>
                                <td class="py-3.5 px-4 text-[#70746F]">{{ $m->movement_date->format('d/m/Y H:i') }}</td>
                                <td class="py-3.5 px-4 font-bold text-[#24364A]">{{ $m->ingredient?->name }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $m->type_badge_class }}">
                                        {{ $m->type_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-[#39715A]">
                                    + {{ number_format($m->quantity_in, 0) }} {{ $m->ingredient?->unit?->symbol }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-[#70746F]">{{ number_format($m->stock_before, 0) }}</td>
                                <td class="py-3.5 px-4 text-right font-bold text-[#24364A]">{{ number_format($m->stock_after, 0) }}</td>
                                <td class="py-3.5 px-4 text-[#70746F]">{{ $m->user?->name ?? 'Sistem' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-xs text-[#70746F]">Tidak ada data stok masuk pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif(in_array($type, ['usage', 'top_used']))
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F4F1EB] border-b border-[#D8CDBD] text-[11px] font-bold text-[#70746F] uppercase tracking-wider">
                            <th class="py-3.5 px-4">Peringkat</th>
                            <th class="py-3.5 px-4">Kode Bahan</th>
                            <th class="py-3.5 px-4">Nama Bahan Baku</th>
                            <th class="py-3.5 px-4 text-center">Jumlah Transaksi Terkait</th>
                            <th class="py-3.5 px-4 text-right">Total Kuantitas Terpakai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E1D6]">
                        @forelse($data as $index => $u)
                            <tr class="hover:bg-[#F4F1EB]/50 transition">
                                <td class="py-3.5 px-4 font-bold text-[#70746F]">
                                    #{{ $index + 1 }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-[#305785]">{{ $u->ingredient_code_snapshot }}</td>
                                <td class="py-3.5 px-4 font-bold text-[#24364A]">{{ $u->ingredient_name_snapshot }}</td>
                                <td class="py-3.5 px-4 text-center text-[#70746F] font-semibold">
                                    {{ $u->total_transactions }} Transaksi
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-[#A65043]">
                                    {{ number_format($u->total_quantity, 0) }} {{ $u->unit_symbol_snapshot }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-xs text-[#70746F]">Tidak ada pemakaian bahan tercatat pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($type === 'recaps')
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F4F1EB] border-b border-[#D8CDBD] text-[11px] font-bold text-[#70746F] uppercase tracking-wider">
                            <th class="py-3.5 px-4">Kode Transaksi</th>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Barista</th>
                            <th class="py-3.5 px-4">Total Menu</th>
                            <th class="py-3.5 px-4">Total Cup Terjual</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Waktu Submit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E1D6]">
                        @forelse($data as $r)
                            <tr class="hover:bg-[#F4F1EB]/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-[#305785]">{{ $r->transaction_code }}</td>
                                <td class="py-3.5 px-4 font-medium text-[#24364A]">{{ $r->transaction_date->format('d/m/Y') }}</td>
                                <td class="py-3.5 px-4 font-bold text-[#24364A]">{{ $r->user?->name ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-[#70746F]">{{ $r->total_menus_count }} jenis</td>
                                <td class="py-3.5 px-4 font-black text-[#24364A]">{{ $r->total_items_quantity }} cup</td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $r->status_badge_class }}">
                                        {{ $r->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-[#70746F]">{{ $r->submitted_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-xs text-[#70746F]">Tidak ada rekap pesanan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

        </div>

        @if(method_exists($data, 'links'))
            <div class="p-4 border-t border-[#E8E1D6]">
                {{ $data->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
