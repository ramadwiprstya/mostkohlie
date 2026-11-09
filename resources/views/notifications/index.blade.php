@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#70746F]">
        <span>Lainnya</span>
        <span>/</span>
        <span class="font-semibold text-[#24364A]">Pusat Notifikasi Stok</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#24364A]">Pusat Notifikasi Stok</h1>
            <p class="text-xs sm:text-sm text-[#70746F] mt-1">Peringatan otomatis stok bahan baku menipis, habis, dan pemberitahuan transaksi operasional.</p>
        </div>

        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="px-4 py-2.5 rounded-xl border border-[#D8CDBD] bg-[#FFFDFA] hover:bg-[#E8E1D6]/40 text-[#24364A] text-xs font-bold transition shadow-xs flex items-center gap-2">
                <i data-lucide="check-check" class="w-4 h-4 text-[#39715A]"></i>
                <span>Tandai Semua Telah Dibaca</span>
            </button>
        </form>
    </div>

    <!-- Notification List Container -->
    <div class="bg-[#FFFDFA] rounded-3xl border border-[#D8CDBD] shadow-xs overflow-hidden divide-y divide-[#E8E1D6]">
        @forelse($notifications as $notif)
            <div class="p-5 flex items-start gap-4 hover:bg-[#F4F1EB]/40 transition {{ !$notif->is_read ? 'bg-[#305785]/5' : '' }}">
                <span class="p-2.5 rounded-2xl shrink-0 mt-0.5 {{ $notif->type === 'stock_empty' ? 'bg-[#A65043]/15 text-[#A65043]' : ($notif->type === 'stock_low' ? 'bg-[#A16A28]/15 text-[#A16A28]' : 'bg-[#39715A]/15 text-[#39715A]') }}">
                    <i data-lucide="{{ $notif->type === 'stock_empty' ? 'alert-triangle' : ($notif->type === 'stock_low' ? 'alert-circle' : 'check-circle-2') }}" class="w-5 h-5"></i>
                </span>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-sm font-bold text-[#24364A]">{{ $notif->title }}</h3>
                        @if(!$notif->is_read)
                            <span class="w-2 h-2 rounded-full bg-[#A65043]"></span>
                        @endif
                    </div>
                    <p class="text-xs text-[#70746F] leading-relaxed">{{ $notif->message }}</p>
                    <span class="text-[11px] text-[#70746F] mt-2 block">{{ $notif->created_at->translatedFormat('d F Y, H:i') }} ({{ $notif->created_at->diffForHumans() }})</span>
                </div>

                @if(!$notif->is_read)
                    <form action="{{ route('notifications.read', $notif) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl border border-[#D8CDBD] text-[#70746F] hover:text-[#24364A] hover:bg-[#E8E1D6]/40 text-[11px] font-semibold transition whitespace-nowrap">
                            Tandai Dibaca
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="py-16 text-center text-xs text-[#70746F]">
                <i data-lucide="bell-off" class="w-10 h-10 text-[#D8CDBD] mx-auto mb-2"></i>
                <p class="font-bold text-[#24364A] text-sm">Tidak ada notifikasi</p>
                <p class="text-[#70746F] mt-0.5">Semua peringatan stok aman dan mutasi terbaru akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($notifications, 'links'))
        <div class="p-4">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection
