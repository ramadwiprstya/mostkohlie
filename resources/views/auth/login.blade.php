<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F1EB]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Kopi Kohlie Stock & Supply</title>
    <!-- Google Fonts: DM Sans & Libre Baskerville -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Brand Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased text-[#24364A] bg-[#F4F1EB]">
    <div class="min-h-screen w-full grid grid-cols-1 lg:grid-cols-[1.15fr_1fr]">
        <!-- Left Column: Warm Cafe Atmosphere with Blue Overlay (Desktop) -->
        <div class="relative hidden lg:flex flex-col justify-between p-12 text-[#FFFDFA] bg-cover bg-center" style="background-image: url('{{ asset('images/login-hero.jpg') }}');">
            <!-- Transparent Blue Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#305785]/90 to-[#24364A]/95 z-0"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Kopi Kohlie" class="w-12 h-12 rounded-xl object-cover shadow-lg border border-white/30 shrink-0">
                    <div>
                        <h1 class="font-serif font-bold text-xl text-white tracking-wide">KOPI KOHLIE</h1>
                        <span class="text-xs text-[#D8CDBD] font-semibold tracking-wider uppercase block">Stock & Supply</span>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 text-white backdrop-blur-xs">
                    Operasional Kopi Kohlie
                </span>
            </div>

            <div class="relative z-10 max-w-md">
                <p class="font-serif italic text-2xl xl:text-3xl leading-snug mb-4 text-[#FFFDFA]">
                    “Bahan yang terjaga, untuk setiap cangkir yang istimewa.”
                </p>
                <p class="text-xs sm:text-sm text-[#E8E1D6] leading-relaxed">
                    Sistem terintegrasi rekap operasional kasir, pemakaian bahan baku, monitoring stok waktu-nyata, dan pengadaan coffee shop.
                </p>
            </div>

            <div class="relative z-10 text-xs text-[#D8CDBD]">
                &copy; {{ date('Y') }} Kopi Kohlie Stock & Supply. All rights reserved.
            </div>
        </div>

        <!-- Right Column: Login Form Panel -->
        <div class="flex flex-col justify-center px-6 sm:px-12 xl:px-16 py-12 bg-[#FFFDFA] border-l border-[#D8CDBD]">
            <div class="max-w-[400px] w-full mx-auto">
                <!-- Mobile Logo Header -->
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <img src="{{ asset('images/logo.png') }}" alt="Kopi Kohlie" class="w-11 h-11 rounded-xl object-cover shadow-md border border-[#D8CDBD] shrink-0">
                    <div>
                        <h1 class="font-serif font-bold text-lg text-[#24364A]">KOPI KOHLIE</h1>
                        <span class="text-[11px] text-[#70746F] font-semibold tracking-wider uppercase block">Stock & Supply</span>
                    </div>
                </div>

                <div class="mb-8">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#305785]/10 text-[#305785] mb-3">
                        Monitoring Persediaan
                    </span>
                    <h2 class="font-serif text-3xl font-bold text-[#24364A] tracking-tight mb-2">
                        Welcome back.
                    </h2>
                    <p class="text-xs sm:text-sm text-[#70746F]">
                        Silakan masuk ke akun operasional Anda untuk mengelola inventaris atau rekap transaksi.
                    </p>
                </div>

                @if(session('info'))
                    <div class="callout-box callout-info mb-5">
                        <i data-lucide="info" class="w-4 h-4 text-[#305785] shrink-0 mt-0.5"></i>
                        <div class="text-xs">{{ session('info') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="callout-box callout-danger mb-5">
                        <i data-lucide="alert-octagon" class="w-4 h-4 text-[#A65043] shrink-0 mt-0.5"></i>
                        <div class="text-xs font-medium">{{ $errors->first() }}</div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold text-[#24364A] mb-1.5">Email Operasional <span class="text-[#A65043]">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#70746F]">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@kopikohlie.com" required autofocus
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#D8CDBD] bg-[#FFFDFA] text-sm text-[#24364A] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-[#24364A] mb-1.5">Kata Sandi <span class="text-[#A65043]">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#70746F]">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </span>
                            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required
                                   class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-[#D8CDBD] bg-[#FFFDFA] text-sm text-[#24364A] focus:outline-none focus:ring-2 focus:ring-[#305785] focus:border-transparent transition-all">
                            <button type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#70746F] hover:text-[#24364A]">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-[#70746F]">
                            <input type="checkbox" name="remember" class="rounded border-[#D8CDBD] text-[#305785] focus:ring-[#305785]">
                            <span>Ingat saya</span>
                        </label>
                        <a href="javascript:void(0)" onclick="alert('Hubungi Owner Andra untuk pemulihan kata sandi operasional.')" class="font-semibold text-[#305785] hover:underline">
                            Lupa password?
                        </a>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-xl bg-[#305785] hover:bg-[#274A70] text-[#FFFDFA] text-sm font-bold shadow-lg shadow-[#305785]/25 transition-all duration-200 flex items-center justify-center gap-2 mt-4">
                        <span>Masuk ke Dashboard</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <p class="text-center text-xs text-[#70746F] mt-8 pt-4 border-t border-[#D8CDBD]">
                    &copy; {{ date('Y') }} Kopi Kohlie Stock & Supply. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide && window.lucide.createIcons) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
