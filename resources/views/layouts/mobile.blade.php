<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'E-Kasir POS Mobile')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        banking: {
                            blue: '#0052cc',
                            purple: '#4f46e5',
                            dark: '#0f172a',
                            card: '#1e293b'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- HTML5-QRCode Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            -webkit-tap-highlight-color: transparent;
        }
        .mobile-container {
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            background-color: #f8fafc;
            box-shadow: 0 0 50px rgba(0,0,0,0.1);
            position: relative;
            padding-bottom: 90px;
        }
        .banking-gradient {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="mobile-container overflow-x-hidden">
    <!-- Header Mobile Banking -->
    <header class="banking-gradient text-white pt-6 pb-12 px-5 rounded-b-[2rem] shadow-xl relative no-print">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center font-bold text-white shadow-inner border border-white/30 text-lg">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-bold text-base leading-tight">{{ auth()->user()->name }}</h2>
                    <p class="text-xs text-indigo-200 flex items-center mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                        {{ auth()->user()->tenant->name ?? 'Semua Outlet' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                @if(auth()->user()->role === 'superadmin' || auth()->user()->role === 'manager')
                    <a href="{{ route('desktop.dashboard') }}" class="p-2.5 bg-white/10 hover:bg-white/20 rounded-xl transition text-xs font-semibold backdrop-blur-md flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Desktop
                    </a>
                @endif

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-200 rounded-xl transition backdrop-blur-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="-mt-8 px-4 relative z-10">
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-500 text-white text-xs font-semibold rounded-2xl shadow-lg flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-rose-500 text-white text-xs font-semibold rounded-2xl shadow-lg flex items-center justify-between">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Navigation Bar (Banking Style) -->
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-2.5 flex justify-around items-center z-50 shadow-2xl no-print">
        <a href="{{ route('mobile.dashboard') }}" 
           class="flex flex-col items-center text-xs font-medium {{ request()->routeIs('mobile.dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Beranda</span>
        </a>

        <a href="{{ route('mobile.pos') }}" 
           class="flex flex-col items-center text-xs font-medium {{ request()->routeIs('mobile.pos') ? 'text-indigo-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
            </svg>
            <span>Kasir</span>
        </a>

        <!-- Middle Quick Scan / Add Button -->
        <a href="{{ route('mobile.smart-input') }}" 
           class="flex flex-col items-center -mt-6">
            <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center shadow-xl shadow-indigo-500/40 border-4 border-slate-100 transform active:scale-95 transition-all">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-indigo-600 mt-0.5">Tambah</span>
        </a>

        <a href="{{ route('mobile.cash-register') }}" 
           class="flex flex-col items-center text-xs font-medium {{ request()->routeIs('mobile.cash-register') ? 'text-indigo-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span>Kas Harian</span>
        </a>

        <a href="{{ route('mobile.transactions') }}" 
           class="flex flex-col items-center text-xs font-medium {{ request()->routeIs('mobile.transactions') ? 'text-indigo-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span>Riwayat</span>
        </a>
    </nav>
</div>

@stack('scripts')
</body>
</html>
