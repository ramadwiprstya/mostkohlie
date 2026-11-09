@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="procurementIndexModal()">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-[#70746F]">
        <span class="text-[#70746F]">Pengadaan</span>
        <span class="text-[#B5ACA0]">></span>
        <span class="font-semibold text-[#24364A]">Request Pengadaan</span>
    </nav>

    <!-- Header Section (Matches Image 1) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A] tracking-tight">Request Pengadaan</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Tinjau dan kelola permintaan bahan baku dari tim operasional.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('procurement-requests.create') }}" 
               class="btn btn-primary px-5 py-2.5 text-xs font-semibold flex items-center gap-2 shadow-sm rounded-xl">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Buat request</span>
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

    @if(session('warning'))
        <div class="callout-box callout-warning">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-[#A16A28] shrink-0 mt-0.5"></i>
            <div>
                <strong>Perhatian:</strong>
                <p class="text-xs text-[#24364A] mt-0.5">{{ session('warning') }}</p>
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

    <!-- Status Tabs (Matches Image 1: Semua, Menunggu, Diproses, Selesai) -->
    <div class="border-b border-[#D8CDBD]">
        <div class="flex items-center gap-2 sm:gap-6 overflow-x-auto text-xs sm:text-sm font-semibold">
            <a href="{{ route('procurement-requests.index', ['tab' => 'semua']) }}" 
               class="pb-3 px-1 transition relative whitespace-nowrap {{ $statusTab === 'semua' ? 'text-[#24364A] font-bold border-b-2 border-[#24364A]' : 'text-[#70746F] hover:text-[#24364A]' }}">
                Semua <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full {{ $statusTab === 'semua' ? 'bg-[#24364A]/10 text-[#24364A]' : 'bg-[#E8E1D6]/50 text-[#70746F]' }}">{{ $countSemua }}</span>
            </a>

            <a href="{{ route('procurement-requests.index', ['tab' => 'menunggu']) }}" 
               class="pb-3 px-1 transition relative whitespace-nowrap {{ $statusTab === 'menunggu' ? 'text-[#24364A] font-bold border-b-2 border-[#24364A]' : 'text-[#70746F] hover:text-[#24364A]' }}">
                Menunggu <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full {{ $statusTab === 'menunggu' ? 'bg-[#A16A28]/20 text-[#A16A28]' : 'bg-[#E8E1D6]/50 text-[#70746F]' }}">{{ $countMenunggu }}</span>
            </a>

            <a href="{{ route('procurement-requests.index', ['tab' => 'diproses']) }}" 
               class="pb-3 px-1 transition relative whitespace-nowrap {{ $statusTab === 'diproses' ? 'text-[#24364A] font-bold border-b-2 border-[#24364A]' : 'text-[#70746F] hover:text-[#24364A]' }}">
                Diproses <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full {{ $statusTab === 'diproses' ? 'bg-[#305785]/20 text-[#305785]' : 'bg-[#E8E1D6]/50 text-[#70746F]' }}">{{ $countDiproses }}</span>
            </a>

            <a href="{{ route('procurement-requests.index', ['tab' => 'selesai']) }}" 
               class="pb-3 px-1 transition relative whitespace-nowrap {{ $statusTab === 'selesai' ? 'text-[#24364A] font-bold border-b-2 border-[#24364A]' : 'text-[#70746F] hover:text-[#24364A]' }}">
                Selesai <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full {{ $statusTab === 'selesai' ? 'bg-[#39715A]/20 text-[#39715A]' : 'bg-[#E8E1D6]/50 text-[#70746F]' }}">{{ $countSelesai }}</span>
            </a>

            @if($countDitolak > 0)
                <a href="{{ route('procurement-requests.index', ['tab' => 'ditolak']) }}" 
                   class="pb-3 px-1 transition relative whitespace-nowrap {{ $statusTab === 'ditolak' ? 'text-[#24364A] font-bold border-b-2 border-[#24364A]' : 'text-[#70746F] hover:text-[#24364A]' }}">
                    Ditolak <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full {{ $statusTab === 'ditolak' ? 'bg-[#A65043]/20 text-[#A65043]' : 'bg-[#E8E1D6]/50 text-[#70746F]' }}">{{ $countDitolak }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Request Cards List (Matches Image 1 Card Structure) -->
    <div class="space-y-3.5">
        @forelse($requests as $item)
            <div class="card p-4 sm:p-5 bg-[#FFFDFA] border border-[#D8CDBD] rounded-2xl shadow-2xs hover:border-[#305785]/50 transition flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <!-- Left: Avatar + Item Name + Subtitle -->
                <div class="flex items-center gap-3.5 min-w-[220px]">
                    <div class="w-12 h-12 rounded-xl bg-[#E8E1D6]/70 flex items-center justify-center font-bold text-sm tracking-wider text-[#70746F] shrink-0 border border-[#D8CDBD]/60">
                        {{ $item->initials }}
                    </div>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base text-[#24364A] leading-snug">
                            {{ $item->ingredient?->name ?? 'Bahan Baku' }}
                        </h3>
                        <p class="text-xs text-[#70746F] mt-0.5">
                            {{ $item->formatted_quantity_requested }} {{ $item->ingredient?->unit?->symbol ?? '' }} dibutuhkan
                        </p>
                    </div>
                </div>

                <!-- Middle Columns (Diajukan oleh, Tanggal, Prioritas, Status) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 flex-1 items-center">
                    
                    <!-- Diajukan oleh -->
                    <div>
                        <span class="block text-[11px] text-[#70746F]">Diajukan oleh</span>
                        <strong class="text-xs font-semibold text-[#24364A] block mt-0.5 truncate">
                            {{ $item->user?->name ?? 'Staff' }}
                        </strong>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <span class="block text-[11px] text-[#70746F]">Tanggal</span>
                        <strong class="text-xs font-semibold text-[#24364A] block mt-0.5">
                            {{ $item->created_at->format('d F Y') }}
                        </strong>
                    </div>

                    <!-- Prioritas -->
                    <div>
                        <span class="block text-[11px] text-[#70746F]">Prioritas</span>
                        <strong class="text-xs font-semibold block mt-0.5 {{ $item->priority === 'tinggi' ? 'text-[#A65043]' : ($item->priority === 'rendah' ? 'text-[#39715A]' : 'text-[#70746F]') }}">
                            {{ $item->priority_label }}
                        </strong>
                    </div>

                    <!-- Status Pill with Dot -->
                    <div>
                        @if($item->status === 'menunggu')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF0DD] text-[#A16A28] border border-[#ECDCC7]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#A16A28]"></span>
                                <span>Menunggu</span>
                            </span>
                        @elseif($item->status === 'diproses')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E6EDF5] text-[#305785] border border-[#B5CBE0]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#305785]"></span>
                                <span>Diproses</span>
                            </span>
                        @elseif($item->status === 'selesai')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#EAF4EF] text-[#39715A] border border-[#CDE5D8]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#39715A]"></span>
                                <span>Selesai</span>
                            </span>
                        @elseif($item->status === 'ditolak')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#FCEDEB] text-[#A65043] border border-[#F2C0B8]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#A65043]"></span>
                                <span>Ditolak</span>
                            </span>
                        @endif
                    </div>

                </div>

                <!-- Right: Action Buttons (Matches Image 1 Tolak & Setujui) -->
                <div class="flex items-center justify-end gap-2 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-[#D8CDBD]/40">
                    
                    @if($item->status === 'menunggu')
                        @if(auth()->user()->isOwner())
                            <!-- Tolak Button -->
                            <button type="button" 
                                    @click="openRejectModal({{ json_encode($item) }})"
                                    class="px-4 py-2 text-xs font-semibold rounded-xl border border-[#D8CDBD] text-[#24364A] hover:bg-[#FAF8F5] transition">
                                Tolak
                            </button>

                            <!-- Setujui Button -->
                            <button type="button" 
                                    @click="openApproveModal({{ json_encode($item) }})"
                                    class="px-5 py-2 text-xs font-semibold rounded-xl bg-[#24364A] text-white hover:bg-[#1A2837] shadow-xs transition">
                                Setujui
                            </button>
                        @else
                            <button type="button" 
                                    @click="openDetailModal({{ json_encode($item) }})"
                                    class="px-4 py-2 text-xs font-semibold rounded-xl border border-[#D8CDBD] text-[#70746F] hover:bg-[#FAF8F5] transition">
                                Detail
                            </button>
                        @endif

                    @elseif($item->status === 'diproses')
                        <!-- Button Terima Barang (Barista / Owner) -->
                        <button type="button" 
                                @click="openReceiveModal({{ json_encode($item) }})"
                                class="px-4 py-2 text-xs font-semibold rounded-xl bg-[#39715A] text-white hover:bg-[#2F5E4B] shadow-xs transition flex items-center gap-1.5">
                            <i data-lucide="package-check" class="w-3.5 h-3.5"></i>
                            <span>Terima Barang</span>
                        </button>

                        <button type="button" 
                                @click="openDetailModal({{ json_encode($item) }})"
                                class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-[#D8CDBD] text-[#24364A] hover:bg-[#FAF8F5] transition">
                            Detail
                        </button>

                    @else
                        <!-- Selesai / Ditolak -->
                        <button type="button" 
                                @click="openDetailModal({{ json_encode($item) }})"
                                class="px-4 py-2 text-xs font-semibold rounded-xl border border-[#D8CDBD] text-[#24364A] hover:bg-[#FAF8F5] transition">
                            Detail
                        </button>
                    @endif

                </div>

            </div>
        @empty
            <div class="card p-12 text-center bg-[#FFFDFA] border border-[#D8CDBD] rounded-2xl">
                <i data-lucide="inbox" class="w-10 h-10 text-[#B5ACA0] mx-auto mb-3"></i>
                <h3 class="font-serif font-bold text-base text-[#24364A]">Tidak Ada Catatan Request Pengadaan</h3>
                <p class="text-xs text-[#70746F] mt-1">
                    {{ $statusTab === 'semua' ? 'Belum ada request pengadaan yang diajukan oleh tim barista.' : "Tidak ada request dengan status '" . ucfirst($statusTab) . "'." }}
                </p>
                <div class="mt-4">
                    <a href="{{ route('procurement-requests.create') }}" class="btn btn-primary btn-sm inline-flex items-center gap-1.5">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Buat Request Pertama</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($requests->hasPages())
        <div class="pt-2">
            {{ $requests->links() }}
        </div>
    @endif

    <!-- MODAL 1: Setujui Request (Owner Only) -->
    <div x-show="approveModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="approveModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-[#D8CDBD]">
            
            <div class="flex items-center justify-between pb-3 border-b border-[#D8CDBD]">
                <div>
                    <h3 class="font-serif text-base font-bold text-[#24364A]">Setujui Request Pengadaan</h3>
                    <p class="text-xs text-[#70746F]" x-text="activeItem ? activeItem.request_code : ''"></p>
                </div>
                <button @click="approveModal = false" class="p-1.5 text-[#70746F] hover:text-[#24364A] rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeItem">
                <form :action="'/procurement-requests/' + activeItem.id + '/approve'" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <div class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Bahan Baku:</span>
                            <strong class="text-[#24364A]" x-text="activeItem.ingredient ? activeItem.ingredient.name : ''"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Jumlah Kebutuhan:</span>
                            <strong class="text-[#305785]" x-text="activeItem.formatted_quantity_requested + ' ' + (activeItem.ingredient && activeItem.ingredient.unit ? activeItem.ingredient.unit.symbol : '')"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Diajukan oleh:</span>
                            <span class="text-[#24364A]" x-text="activeItem.user ? activeItem.user.name : ''"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Supplier / Toko Penyedia</label>
                        <input type="text" name="supplier_name" placeholder="Contoh: CV Sumber Bahan Pangan"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Estimasi Kedatangan Barang</label>
                        <input type="date" name="estimated_arrival"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Catatan Pemesanan</label>
                        <textarea name="order_notes" rows="2" placeholder="Catatan opsional untuk pengadaan ini..."
                                  class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#305785]"></textarea>
                    </div>

                    <div class="pt-3 border-t border-[#D8CDBD] flex items-center justify-end gap-2">
                        <button type="button" @click="approveModal = false" class="btn btn-secondary btn-sm">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm flex items-center gap-1.5">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Konfirmasi Setujui</span>
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- MODAL 2: Tolak Request (Owner Only) -->
    <div x-show="rejectModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="rejectModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-[#D8CDBD]">
            
            <div class="flex items-center justify-between pb-3 border-b border-[#D8CDBD]">
                <div>
                    <h3 class="font-serif text-base font-bold text-[#A65043]">Tolak Request Pengadaan</h3>
                    <p class="text-xs text-[#70746F]" x-text="activeItem ? activeItem.request_code : ''"></p>
                </div>
                <button @click="rejectModal = false" class="p-1.5 text-[#70746F] hover:text-[#24364A] rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeItem">
                <form :action="'/procurement-requests/' + activeItem.id + '/reject'" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <div class="p-3 bg-[#FCEDEB] border border-[#F2C0B8] rounded-xl text-xs space-y-1">
                        <p class="text-xs text-[#A65043]">
                            Pengadaan bahan <strong x-text="activeItem.ingredient ? activeItem.ingredient.name : ''"></strong> tidak akan diproses ke supplier. Berikan alasan penolakan agar barista mengetahuinya.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-1.5">
                            Alasan Penolakan <span class="text-[#A65043]">*</span>
                        </label>
                        <textarea name="rejection_reason" rows="3" required placeholder="Contoh: Stok masih mencukupi untuk akhir pekan atau jadwal pengadaan rutin hari Jumat."
                                  class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#A65043]"></textarea>
                    </div>

                    <div class="pt-3 border-t border-[#D8CDBD] flex items-center justify-end gap-2">
                        <button type="button" @click="rejectModal = false" class="btn btn-secondary btn-sm">Batal</button>
                        <button type="submit" class="btn btn-sm bg-[#A65043] text-white hover:bg-[#8D3E33] flex items-center gap-1.5 font-semibold rounded-xl px-4 py-2">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            <span>Tolak Pengadaan</span>
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- MODAL 3: Penerimaan Barang (Step 6 & 7) -->
    <div x-show="receiveModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="receiveModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl border border-[#D8CDBD]">
            
            <div class="flex items-center justify-between pb-3 border-b border-[#D8CDBD]">
                <div>
                    <h3 class="font-serif text-base font-bold text-[#39715A]">Catat Penerimaan Bahan</h3>
                    <p class="text-xs text-[#70746F]">Verifikasi barang masuk dan perbarui stok sistem secara otomatis.</p>
                </div>
                <button @click="receiveModal = false" class="p-1.5 text-[#70746F] hover:text-[#24364A] rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeItem">
                <form :action="'/procurement-requests/' + activeItem.id + '/receive'" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <!-- Callout Alert Step 7: Stok Otomatis Bertambah -->
                    <div class="p-3.5 bg-[#EAF4EF] border border-[#CDE5D8] rounded-xl flex items-center gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-[#39715A] shrink-0"></i>
                        <p class="text-xs text-[#24364A]">
                            Menyimpan penerimaan ini akan <strong>langsung menambah stok fisik</strong> bahan baku ke sistem dan menyelesaikan alur pengadaan.
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
                            <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Supplier / Sumber</label>
                            <input type="text" name="supplier_name" :value="activeItem.supplier_name || ''" placeholder="Nama toko / distributor"
                                   class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#39715A]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Nomor Nota / Invoice</label>
                            <input type="text" name="invoice_number" placeholder="Contoh: INV-2026/10/004"
                                   class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#39715A]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Tanggal Kedaluwarsa (Exp)</label>
                            <input type="date" name="expiry_date"
                                   class="w-full px-3.5 py-2 rounded-xl border border-[#D8CDBD] text-xs text-[#24364A] bg-[#FFFDFA] focus:outline-none focus:ring-2 focus:ring-[#39715A]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#24364A] mb-1.5">Catatan Kondisi Barang</label>
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

    <!-- MODAL 4: Detail Informasi Lengkap -->
    <div x-show="detailModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-[#24364A]/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="detailModal = false" 
             class="bg-[#FFFDFA] rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-[#D8CDBD]">
            
            <div class="flex items-center justify-between pb-3 border-b border-[#D8CDBD]">
                <div>
                    <h3 class="font-serif text-base font-bold text-[#24364A]">Rincian Request Pengadaan</h3>
                    <p class="text-xs text-[#70746F]" x-text="activeItem ? activeItem.request_code : ''"></p>
                </div>
                <button @click="detailModal = false" class="p-1.5 text-[#70746F] hover:text-[#24364A] rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeItem">
                <div class="mt-4 space-y-3 text-xs">
                    <div class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl space-y-2">
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Bahan Baku:</span>
                            <strong class="text-[#24364A]" x-text="activeItem.ingredient ? activeItem.ingredient.name : ''"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Jumlah Diminta:</span>
                            <strong class="text-[#305785]" x-text="activeItem.formatted_quantity_requested + ' ' + (activeItem.ingredient && activeItem.ingredient.unit ? activeItem.ingredient.unit.symbol : '')"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Stok Saat Request:</span>
                            <span class="text-[#24364A]" x-text="activeItem.stock_at_request + ' (Batas aman: ' + activeItem.min_stock_at_request + ')'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Prioritas:</span>
                            <span class="font-semibold uppercase text-[11px]" x-text="activeItem.priority"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#70746F]">Diajukan oleh:</span>
                            <span class="text-[#24364A]" x-text="activeItem.user ? activeItem.user.name : ''"></span>
                        </div>
                    </div>

                    <div x-show="activeItem.reason" class="p-3 bg-[#FAF8F5] border border-[#D8CDBD] rounded-xl">
                        <span class="text-[#70746F] block font-semibold mb-1">Alasan Pengajuan:</span>
                        <p class="text-[#24364A] leading-relaxed" x-text="activeItem.reason"></p>
                    </div>

                    <!-- Informasi Persetujuan / Penolakan jika ada -->
                    <template x-if="activeItem.status === 'ditolak'">
                        <div class="p-3 bg-[#FCEDEB] border border-[#F2C0B8] rounded-xl">
                            <span class="text-[#A65043] block font-bold mb-1">Alasan Penolakan Owner:</span>
                            <p class="text-[#A65043] leading-relaxed" x-text="activeItem.rejection_reason || 'Tidak ada alasan rinci'"></p>
                        </div>
                    </template>

                    <template x-if="activeItem.status === 'selesai'">
                        <div class="p-3 bg-[#EAF4EF] border border-[#CDE5D8] rounded-xl space-y-1">
                            <span class="text-[#39715A] block font-bold mb-1">Data Penerimaan:</span>
                            <div class="flex justify-between text-[#70746F]">
                                <span>Jumlah Diterima:</span>
                                <strong class="text-[#24364A]" x-text="activeItem.formatted_quantity_received"></strong>
                            </div>
                            <div class="flex justify-between text-[#70746F]" x-show="activeItem.supplier_name">
                                <span>Supplier:</span>
                                <span class="text-[#24364A]" x-text="activeItem.supplier_name"></span>
                            </div>
                            <div class="flex justify-between text-[#70746F]" x-show="activeItem.invoice_number">
                                <span>No. Invoice:</span>
                                <span class="text-[#24364A]" x-text="activeItem.invoice_number"></span>
                            </div>
                        </div>
                    </template>

                    <div class="pt-3 border-t border-[#D8CDBD] flex items-center justify-end">
                        <button type="button" @click="detailModal = false" class="btn btn-primary btn-sm">Tutup</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function procurementIndexModal() {
        return {
            approveModal: false,
            rejectModal: false,
            receiveModal: false,
            detailModal: false,
            activeItem: null,

            openApproveModal(item) {
                this.activeItem = item;
                this.approveModal = true;
                this.$nextTick(() => { if (window.createIcons) window.createIcons(); });
            },

            openRejectModal(item) {
                this.activeItem = item;
                this.rejectModal = true;
                this.$nextTick(() => { if (window.createIcons) window.createIcons(); });
            },

            openReceiveModal(item) {
                this.activeItem = item;
                this.receiveModal = true;
                this.$nextTick(() => { if (window.createIcons) window.createIcons(); });
            },

            openDetailModal(item) {
                this.activeItem = item;
                this.detailModal = true;
                this.$nextTick(() => { if (window.createIcons) window.createIcons(); });
            }
        };
    }
</script>
@endpush

