<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F1EB]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sistem Persediaan' }} — Kopi Kohlie Stock & Supply</title>

    <!-- Google Fonts: DM Sans & Libre Baskerville -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Brand Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex text-[#24364A] font-sans antialiased bg-[#F4F1EB]" x-data="{ sidebarOpen: false, notificationOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-[#24364A]/60 backdrop-blur-xs z-40 lg:hidden" 
         x-cloak></div>

    <!-- Sidebar (Theme: Deep Blue-Slate #24364A with Warm Sand Accents) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-[#24364A] text-[#F4F1EB] flex flex-col transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none lg:static border-r border-[#D8CDBD]/15">
        
        <!-- Brand Header with Official Logo -->
        <div class="h-[72px] flex items-center px-6 gap-3 border-b border-white/10 bg-black/10">
            <img src="{{ asset('images/logo.png') }}" alt="Kopi Kohlie" class="w-10 h-10 rounded-xl object-cover shadow-md border border-white/20 shrink-0">
            <div>
                <h1 class="font-serif font-bold text-base tracking-wide text-[#FFFDFA] leading-tight">KOPI KOHLIE</h1>
                <p class="text-[11px] text-[#D8CDBD] font-semibold tracking-wider uppercase">Stock & Supply</p>
            </div>
            <button @click="sidebarOpen = false" class="ml-auto text-white/70 hover:text-white lg:hidden">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Prominent Call-to-Action for Barista: Input Rekap Pesanan -->
        <div class="px-4 pt-5 pb-2">
            @if(auth()->user()->isBarista())
                <a href="{{ route('order-recaps.create') }}" 
                   class="flex items-center justify-center gap-2.5 w-full py-3 px-4 rounded-xl font-bold text-xs bg-[#305785] hover:bg-[#274A70] text-[#FFFDFA] shadow-md shadow-black/20 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 border border-white/10">
                    <i data-lucide="clipboard-pen-line" class="w-4 h-4 stroke-[2.5]"></i>
                    <span>Input Rekap Pesanan</span>
                </a>
            @else
                <a href="{{ route('order-recaps.index') }}" 
                   class="flex items-center justify-center gap-2.5 w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-white/10 hover:bg-white/15 text-[#FFFDFA] transition-all duration-150">
                    <i data-lucide="list" class="w-4 h-4"></i>
                    <span>Lihat Rekap Pesanan</span>
                </a>
            @endif
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-2 space-y-4 overflow-y-auto">
            <!-- Group: Beranda -->
            <div>
                <div class="px-3 pb-1 text-[10px] font-bold tracking-wider text-[#D8CDBD]/60 uppercase">Beranda</div>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-[#FFFDFA]' : '' }}"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- Group: Rekap Pesanan -->
            <div>
                <div class="px-3 pb-1 text-[10px] font-bold tracking-wider text-[#D8CDBD]/60 uppercase">Rekap Pesanan</div>
                <div class="space-y-1">
                    @if(auth()->user()->isBarista())
                        <a href="{{ route('order-recaps.create') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('order-recaps.create') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                            <i data-lucide="clipboard-pen-line" class="w-4 h-4"></i>
                            <span>Input Rekap</span>
                            <span class="ml-auto px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-[#A16A28] text-white">Shift</span>
                        </a>
                    @else
                        <div class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-white/40 cursor-not-allowed" title="Hanya untuk Barista">
                            <i data-lucide="clipboard-pen-line" class="w-4 h-4"></i>
                            <span>Input Rekap</span>
                            <i data-lucide="lock" class="w-3.5 h-3.5 ml-auto opacity-60"></i>
                        </div>
                    @endif

                    <a href="{{ route('order-recaps.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('order-recaps.index') || request()->routeIs('order-recaps.show') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="receipt-text" class="w-4 h-4"></i>
                        <span>Riwayat Rekap</span>
                    </a>
                </div>
            </div>

            <!-- Group: Menu & Resep -->
            <div>
                <div class="px-3 pb-1 text-[10px] font-bold tracking-wider text-[#D8CDBD]/60 uppercase">Menu & Resep</div>
                <div class="space-y-1">
                    <a href="{{ route('menus.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('menus.index') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                        <span>Daftar Menu</span>
                    </a>

                    <a href="{{ route('menus.recipe.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('menus.recipe*') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="flask-conical" class="w-4 h-4"></i>
                        <span>Komposisi Bahan</span>
                    </a>
                </div>
            </div>

            <!-- Group: Persediaan -->
            <div>
                <div class="px-3 pb-1 text-[10px] font-bold tracking-wider text-[#D8CDBD]/60 uppercase">Persediaan</div>
                <div class="space-y-1">
                    <a href="{{ route('monitoring.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('monitoring.*') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="boxes" class="w-4 h-4"></i>
                        <span>Monitoring Stok</span>
                        @php
                            $criticalCount = \App\Models\Ingredient::where('is_active', true)->whereColumn('current_stock', '<=', 'min_stock')->count();
                        @endphp
                        @if($criticalCount > 0)
                            <span class="ml-auto px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-[#A65043] text-white">
                                {{ $criticalCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('ingredients.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('ingredients.*') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="package-plus" class="w-4 h-4"></i>
                        <span>Penerimaan Bahan</span>
                    </a>

                    <a href="{{ route('stock-movements.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('stock-movements.*') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="history" class="w-4 h-4"></i>
                        <span>Riwayat Stok</span>
                    </a>
                </div>
            </div>

            <!-- Group: Pengadaan -->
            <div>
                <div class="px-3 pb-1 text-[10px] font-bold tracking-wider text-[#D8CDBD]/60 uppercase">Pengadaan</div>
                <div class="space-y-1">
                    <a href="{{ route('procurement-requests.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('procurement-requests.*') && request('tab') !== 'diproses' ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        <span>Request Pengadaan</span>
                        @php
                            $pendingReqCount = \App\Models\ProcurementRequest::where('status', 'menunggu')->count();
                        @endphp
                        @if($pendingReqCount > 0)
                            <span class="ml-auto px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-[#A16A28] text-white">
                                {{ $pendingReqCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('procurement-requests.index', ['tab' => 'diproses']) }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('procurement-requests.*') && request('tab') === 'diproses' ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="truck" class="w-4 h-4"></i>
                        <span>Pengadaan Berjalan</span>
                        @php
                            $inProgressReqCount = \App\Models\ProcurementRequest::where('status', 'diproses')->count();
                        @endphp
                        @if($inProgressReqCount > 0)
                            <span class="ml-auto px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-[#305785] text-white">
                                {{ $inProgressReqCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ auth()->user()->isBarista() ? route('emergency-purchases.create') : route('emergency-purchases.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('emergency-purchases.*') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="shield-alert" class="w-4 h-4 {{ request()->routeIs('emergency-purchases.*') ? 'text-[#FFFDFA]' : '' }}"></i>
                        <span>Pembelian Darurat</span>
                        @if(auth()->user()->isBarista())
                            <span class="ml-auto px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-[#A16A28] text-white">Input</span>
                        @else
                            <span class="ml-auto px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-[#305785]/80 text-white">Rekap</span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Group: Lainnya (Owner Only) -->
            <div>
                <div class="px-3 pb-1 text-[10px] font-bold tracking-wider text-[#D8CDBD]/60 uppercase">Lainnya</div>
                <div class="space-y-1">
                    @if(auth()->user()->isOwner())
                        <a href="{{ route('reports.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('reports.*') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                            <span>Laporan</span>
                        </a>

                        <a href="{{ route('users.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('users.*') ? 'bg-[#305785] text-[#FFFDFA] shadow-sm font-bold' : 'text-[#F4F1EB]/85 hover:bg-white/10 hover:text-white' }}">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                            <span>Pengaturan</span>
                        </a>
                    @else
                        <div class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-white/40 cursor-not-allowed" title="Khusus Owner">
                            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                            <span>Laporan</span>
                            <i data-lucide="lock" class="w-3.5 h-3.5 ml-auto opacity-60"></i>
                        </div>
                        <div class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-white/40 cursor-not-allowed" title="Khusus Owner">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                            <span>Pengaturan</span>
                            <i data-lucide="lock" class="w-3.5 h-3.5 ml-auto opacity-60"></i>
                        </div>
                    @endif
                </div>
            </div>
        </nav>

        <!-- User Profile Card in Sidebar Footer -->
        <div class="p-3.5 border-t border-white/10 bg-black/15">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#E8E1D6] text-[#305785] flex items-center justify-center font-bold text-xs shadow-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-[#FFFDFA] truncate">{{ auth()->user()->name }}</p>
                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold {{ auth()->user()->isOwner() ? 'bg-[#FAF0DD] text-[#A16A28]' : 'bg-[#EAF4EF] text-[#39715A]' }} uppercase">
                        {{ auth()->user()->role }}
                    </span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 text-white/60 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Shell -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Navigation Bar -->
        <header class="h-[72px] bg-[#FFFDFA] border-b border-[#D8CDBD] px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="p-2 rounded-lg text-[#24364A] hover:bg-[#E8E1D6]/40 lg:hidden">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <div class="flex items-center gap-3" x-data="realtimeHeaderClock()">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#305785]/10 text-[#305785]">
                        <span class="w-2 h-2 rounded-full bg-[#39715A] animate-pulse"></span>
                        <span>Sistem Persediaan</span>
                    </span>

                    <!-- Live Real-Time Clock Badge -->
                    <div class="inline-flex items-center gap-2 text-xs text-[#24364A] bg-[#FAF8F5] px-3 py-1.5 rounded-xl border border-[#D8CDBD] shadow-2xs">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-[#305785]"></i>
                        <span class="hidden md:inline text-[#70746F]" x-text="dateString"></span>
                        <span class="hidden md:inline text-[#D8CDBD]">•</span>
                        <span class="font-mono font-bold text-[#305785] tracking-wide" x-text="timeString"></span>
                        <span class="text-[10px] font-bold text-[#39715A] bg-[#EAF4EF] px-1.5 py-0.2 rounded">WIB</span>
                    </div>
                </div>
            </div>

            <!-- Topbar Actions -->
            <div class="flex items-center gap-3">
                <!-- Notification Bell with Popover -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="relative p-2.5 rounded-full text-[#24364A] hover:bg-[#E8E1D6]/50 border border-[#D8CDBD] transition-colors">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        @php
                            $unreadCount = \App\Models\Notification::unread()->count();
                        @endphp
                        @if($unreadCount > 0 || $criticalCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#A65043] ring-2 ring-white"></span>
                        @endif
                    </button>

                    <!-- Popover Dropdown -->
                    <div x-show="open" 
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-[#FFFDFA] rounded-2xl shadow-xl border border-[#D8CDBD] p-4 z-50"
                         x-cloak>
                        <div class="flex items-center justify-between pb-3 border-b border-[#D8CDBD]">
                            <h3 class="font-serif font-bold text-sm text-[#24364A]">Pemberitahuan Stok</h3>
                            <a href="{{ route('notifications.index') }}" class="text-xs text-[#305785] font-bold hover:underline">Lihat Semua</a>
                        </div>
                        <div class="divide-y divide-[#D8CDBD]/40 max-h-72 overflow-y-auto mt-2">
                            <div class="py-2.5 flex items-start gap-3">
                                <span class="p-1.5 rounded-lg bg-[#FAF0DD] text-[#A16A28] mt-0.5">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-[#24364A]">Stok Susu UHT mulai menipis</p>
                                    <p class="text-[11px] text-[#70746F]">Sisa 2.400 ml (Batas aman: 3.000 ml)</p>
                                    <span class="text-[10px] text-slate-400">10 menit lalu</span>
                                </div>
                            </div>
                            <div class="py-2.5 flex items-start gap-3">
                                <span class="p-1.5 rounded-lg bg-[#F8E8E5] text-[#A65043] mt-0.5">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-[#24364A]">Susu UHT mendekati kedaluwarsa</p>
                                    <p class="text-[11px] text-[#70746F]">Batch #SKD-09 kedaluwarsa dalam 3 hari</p>
                                    <span class="text-[10px] text-slate-400">25 menit lalu</span>
                                </div>
                            </div>
                            <div class="py-2.5 flex items-start gap-3">
                                <span class="p-1.5 rounded-lg bg-[#EAF4EF] text-[#39715A] mt-0.5">
                                    <i data-lucide="file-plus" class="w-4 h-4"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-[#24364A]">Request pengadaan baru</p>
                                    <p class="text-[11px] text-[#70746F]">Barista Dimas mengajukan 40L Susu UHT</p>
                                    <span class="text-[10px] text-slate-400">1 jam lalu</span>
                                </div>
                            </div>
                            <div class="py-2.5 flex items-start gap-3">
                                <span class="p-1.5 rounded-lg bg-[#FAF0DD] text-[#A16A28] mt-0.5">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-[#24364A]">Sirup Butterscotch hampir habis</p>
                                    <p class="text-[11px] text-[#70746F]">Tersisa 180 ml di bar station</p>
                                    <span class="text-[10px] text-slate-400">2 jam lalu</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile Badge -->
                <div class="flex items-center gap-2.5 pl-3 border-l border-[#D8CDBD]">
                    <div class="w-8 h-8 rounded-full bg-[#305785] text-[#FFFDFA] flex items-center justify-center font-bold text-xs shadow-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-bold text-[#24364A] leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-[#70746F] capitalize font-medium">{{ auth()->user()->role }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="flex-1 p-6 md:p-8">
            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="callout-box callout-success mb-6">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-[#39715A] shrink-0 mt-0.5"></i>
                    <div class="flex-1 font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="callout-box callout-danger mb-6">
                    <i data-lucide="alert-octagon" class="w-5 h-5 text-[#A65043] shrink-0 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-bold mb-1">{{ session('error') }}</p>
                        @if(session('shortages'))
                            <ul class="list-disc list-inside mt-2 space-y-1 text-xs text-[#A65043] bg-white/60 p-3 rounded-lg border border-[#A65043]/20">
                                @foreach(session('shortages') as $shortage)
                                    <li>
                                        <span class="font-bold">{{ $shortage['ingredient_name'] }}:</span> 
                                        Dibutuhkan <span class="font-bold">{{ number_format($shortage['required'], 0) }} {{ $shortage['unit'] }}</span>, 
                                        stok tersedia hanya <span class="font-bold">{{ number_format($shortage['available'], 0) }} {{ $shortage['unit'] }}</span> 
                                        (Kurang {{ number_format($shortage['shortage'], 0) }} {{ $shortage['unit'] }})
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="callout-box callout-warning mb-6">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-[#A16A28] shrink-0 mt-0.5"></i>
                    <div class="flex-1 font-medium">{{ session('warning') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="callout-box callout-info mb-6">
                    <i data-lucide="info" class="w-5 h-5 text-[#305785] shrink-0 mt-0.5"></i>
                    <div class="flex-1 font-medium">{{ session('info') }}</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Real-time clock & lucide init -->
    <script>
        if (!window.realtimeHeaderClock) {
            window.realtimeHeaderClock = function() {
                return {
                    dateString: '',
                    timeString: '',
                    init() {
                        this.tick();
                        setInterval(() => this.tick(), 1000);
                    },
                    tick() {
                        const now = new Date();
                        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                        const dayName = days[now.getDay()];
                        const date = String(now.getDate()).padStart(2, '0');
                        const monthName = months[now.getMonth()];
                        const year = now.getFullYear();

                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        const seconds = String(now.getSeconds()).padStart(2, '0');

                        this.dateString = `${dayName}, ${date} ${monthName} ${year}`;
                        this.timeString = `${hours}:${minutes}:${seconds}`;
                    }
                };
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide && window.lucide.createIcons) {
                window.lucide.createIcons();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
