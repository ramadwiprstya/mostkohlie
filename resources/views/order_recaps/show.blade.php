@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ cancelModal: false }">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <a href="{{ route('order-recaps.index') }}" class="text-xs text-[#70746F] hover:text-[#305785]">Riwayat Rekap</a>
        <span class="separator text-[#B5ACA0] mx-1">/</span>
        <span class="text-xs font-semibold text-[#24364A]">Detail Rekap #{{ $orderRecap->transaction_code }}</span>
    </nav>

    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Detail Rekap #{{ $orderRecap->transaction_code }}</h1>
                <span class="badge {{ $orderRecap->isSubmitted() ? 'badge-safe' : 'badge-danger' }}">
                    <span class="badge-dot"></span>{{ $orderRecap->isSubmitted() ? 'Selesai Divalidasi' : 'Dibatalkan' }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">
                Rincian pesanan kasir dan mutasi pengurangan bahan baku otomatis berdasarkan master resep.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="history" class="w-4 h-4"></i>
                <span>Lihat Riwayat Stok</span>
            </a>
            @if($orderRecap->isSubmitted() && auth()->user()->isOwner())
                <button type="button" 
                        @click="cancelModal = true"
                        class="btn btn-danger btn-sm">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span>Batalkan & Balikkan Stok</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Cancellation Notice If Cancelled -->
    @if($orderRecap->isCancelled())
        <div class="callout-box callout-danger">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-[#A65043] shrink-0 mt-0.5"></i>
            <div class="space-y-1 text-xs">
                <p class="font-bold text-sm">Transaksi Telah Dibatalkan</p>
                <p>Dibatalkan oleh: <strong>{{ $orderRecap->cancelledByUser?->name ?? 'Owner' }}</strong> pada {{ $orderRecap->cancelled_at?->format('d/m/Y H:i') }} WIB.</p>
                <p class="bg-white/80 p-2.5 rounded-lg border border-[#A65043]/20 mt-1">
                    <strong>Alasan Pembatalan:</strong> {{ $orderRecap->cancellation_reason }}
                </p>
                <p class="text-[11px]">Seluruh bahan baku telah otomatis dikembalikan (reversed) ke stok aktif pada saat pembatalan.</p>
            </div>
        </div>
    @endif

    <!-- 2 Columns: Items & Ingredient Usages -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Side: Detail Transaksi & Menu (5 Cols) -->
        <div class="lg:col-span-5 card space-y-4">
            <div class="card-header pb-2 border-b border-[#D8CDBD]">
                <div>
                    <h3 class="card-title text-base">Informasi Batch Transaksi Kasir</h3>
                    <p class="card-subtitle">Dicatat dari printer POS kasir</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-[11px] text-[#70746F] block">Nomor Rekap</span>
                    <strong class="text-[#24364A]">{{ $orderRecap->transaction_code }}</strong>
                </div>
                <div>
                    <span class="text-[11px] text-[#70746F] block">Referensi Kasir</span>
                    <code>{{ $orderRecap->notes ?? 'TRX-POS-1042..1078' }}</code>
                </div>
                <div>
                    <span class="text-[11px] text-[#70746F] block">Tanggal & Waktu</span>
                    <span class="text-xs font-semibold text-[#24364A]">{{ $orderRecap->transaction_date->format('d M Y') }} &bull; {{ $orderRecap->submitted_at?->format('H:i') }} WIB</span>
                </div>
                <div>
                    <span class="text-[11px] text-[#70746F] block">Shift Operasional</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $orderRecap->shift_badge_class }}">
                        Shift {{ ucfirst($orderRecap->shift ?? 'pagi') }}
                    </span>
                </div>
                <div>
                    <span class="text-[11px] text-[#70746F] block">Barista Perekap</span>
                    <strong>{{ $orderRecap->user?->name ?? 'Barista' }}</strong>
                </div>
            </div>

            <div class="pt-3 border-t border-[#D8CDBD]">
                <h4 class="font-bold text-xs text-[#24364A] mb-2">Daftar Menu Terjual:</h4>
                <div class="table-responsive">
                    <table class="table-custom text-xs">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @foreach($orderRecap->items as $item)
                                @php 
                                    $sub = $item->quantity * $item->price_per_item_snapshot;
                                    $grandTotal += $sub;
                                @endphp
                                <tr>
                                    <td>
                                        <strong class="text-[#24364A]">{{ $item->menu_name_snapshot }}</strong>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }} Cup</td>
                                    <td class="text-right text-[#70746F]">Rp {{ number_format($item->price_per_item_snapshot, 0, ',', '.') }}</td>
                                    <td class="text-right font-bold text-[#24364A]">Rp {{ number_format($sub, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-[#FAF8F5] font-bold">
                                <td>Total</td>
                                <td class="text-center">{{ $orderRecap->total_items_quantity }} Cup</td>
                                <td></td>
                                <td class="text-right text-[#305785]">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side: Ingredient Usages & Recipe Formula (7 Cols) -->
        <div class="lg:col-span-7 card border-t-4 border-t-[#39715A] space-y-4">
            <div class="card-header pb-2 border-b border-[#D8CDBD]">
                <div>
                    <h3 class="card-title text-base">Pemakaian Bahan Berdasarkan Master Resep</h3>
                    <p class="card-subtitle">Formula otomatis pemotongan inventaris gudang</p>
                </div>
                <span class="badge badge-safe">Stok Diperbarui</span>
            </div>

            <div class="space-y-3">
                @foreach($orderRecap->ingredientUsages as $usage)
                    <div class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl text-xs">
                        <div class="flex justify-between items-center mb-1">
                            <strong class="text-sm text-[#24364A]">{{ $usage->ingredient_name_snapshot }}</strong>
                            <span class="badge badge-danger">
                                -{{ number_format($usage->quantity_used, 0, ',', '.') }} {{ $usage->unit_symbol_snapshot }}
                            </span>
                        </div>
                        <div class="text-[11px] text-[#70746F]">
                            Formula: Dihitung dari takaran per cup dikalikan total porsi menu terkait dalam batch rekap ini.
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="callout-box callout-success text-xs mt-4 mb-0">
                <i data-lucide="check-circle" class="w-4 h-4 text-[#39715A] shrink-0 mt-0.5"></i>
                <div>Pemakaian ini telah tervalidasi dan tersimpan permanen dalam Riwayat Mutasi Stok.</div>
            </div>
        </div>

    </div>

    <!-- Cancellation Modal for Owner -->
    <div x-show="cancelModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="cancelModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-[#D8CDBD]">
            
            <div class="w-12 h-12 rounded-2xl bg-[#F8E8E5] text-[#A65043] flex items-center justify-center mb-3 mx-auto">
                <i data-lucide="rotate-ccw" class="w-6 h-6"></i>
            </div>

            <h3 class="font-serif text-lg font-bold text-[#A65043] text-center">Konfirmasi Pembatalan Rekap</h3>
            <p class="text-xs text-[#70746F] text-center mt-1 leading-relaxed">
                Tindakan ini akan <strong>mengembalikan (restock)</strong> seluruh bahan baku yang sebelumnya terpotong ke inventaris aktif.
            </p>

            <form action="{{ route('order-recaps.cancel', $orderRecap) }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#24364A] mb-1.5">Alasan Pembatalan *</label>
                    <textarea name="cancellation_reason" required rows="3" 
                              placeholder="Contoh: Salah input tanggal transaksi atau dobel input nota..."
                              class="input-textarea text-xs"></textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" @click="cancelModal = false" 
                            class="btn btn-secondary flex-1 py-2 text-xs">
                        Kembali
                    </button>
                    <button type="submit" 
                            class="btn btn-danger flex-1 py-2 text-xs">
                        Ya, Batalkan Rekap
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
