<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Management Dashboard - E-Kasir')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: window.innerWidth >= 1024 }" class="min-h-screen flex bg-slate-50 relative">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 lg:hidden" 
         style="display: none;"></div>

    <!-- Left Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:-translate-x-full lg:w-0 lg:p-0 lg:overflow-hidden'"
           class="fixed lg:sticky top-0 h-screen bg-slate-900 text-slate-300 flex flex-col min-h-screen shadow-xl z-40 transition-all duration-300 ease-in-out shrink-0">
        <!-- Logo -->
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="font-extrabold text-white text-lg tracking-tight">E-Kasir</h1>
                    <p class="text-xs text-slate-400">Multi-Outlet POS</p>
                </div>
            </div>

            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            <a href="{{ route('desktop.dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('desktop.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('desktop.products.index') }}" 
               class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('desktop.products.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span>Produk & Stok</span>
            </a>

            <a href="{{ route('desktop.expenses.index') }}" 
               class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('desktop.expenses.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Pengeluaran Operasional</span>
            </a>

            @if(auth()->user()->role === 'superadmin')
                <a href="{{ route('desktop.tenants.index') }}" 
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('desktop.tenants.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"></path>
                    </svg>
                    <span>Manajemen Outlet</span>
                </a>
            @endif

            <a href="{{ route('desktop.users.index') }}" 
               class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('desktop.users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Kelola Staf & User</span>
            </a>

            <a href="{{ route('desktop.reports.index') }}" 
               class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('desktop.reports.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Laporan Keuangan</span>
            </a>
        </nav>

        <!-- Switch to Mobile POS Quick Link -->
        <div class="p-4 border-t border-slate-800">
            <a href="{{ route('mobile.dashboard') }}" class="flex items-center justify-center p-3 rounded-xl bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition text-xs font-bold border border-indigo-500/30">
                <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>Buka Tampilan Mobile POS</span>
            </a>
        </div>
    </aside>

    <!-- Main Workspace -->
    <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200/80 px-4 md:px-8 py-4 flex items-center justify-between sticky top-0 z-20 shadow-sm">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-indigo-600 focus:outline-none transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <h2 class="text-base md:text-xl font-bold text-slate-800 truncate">@yield('page_title', 'Dashboard')</h2>
            </div>

            <!-- Outlet Switcher & User Profile -->
            <div class="flex items-center space-x-2 md:space-x-4">
                @if(auth()->user()->role === 'superadmin')
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-1.5 px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"></path>
                            </svg>
                            <span class="truncate max-w-[120px] md:max-w-none">{{ session('active_tenant_id') ? \App\Models\Tenant::find(session('active_tenant_id'))->name : 'Semua Outlet' }}</span>
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Pilih Outlet</div>
                            @foreach(\App\Models\Tenant::where('is_active', true)->get() as $t)
                                <form action="{{ route('switch-tenant', $t->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-between">
                                        <span>{{ $t->name }}</span>
                                        @if(session('active_tenant_id') == $t->id)
                                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex items-center space-x-2 md:space-x-3 pl-2 md:pl-4 border-l border-slate-200">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] uppercase font-extrabold text-indigo-600 tracking-wider">{{ auth()->user()->role }}</p>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="p-4 md:p-8 flex-1">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500 text-white font-semibold rounded-2xl shadow-lg flex items-center justify-between text-sm">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-500 text-white font-semibold rounded-2xl shadow-lg flex items-center justify-between text-sm">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Global Toast Container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col space-y-2 pointer-events-none"></div>

    <script>
    function copyTextToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            return new Promise((resolve, reject) => {
                document.execCommand('copy') ? resolve() : reject();
                textArea.remove();
            });
        }
    }

    function showToast(message, title = 'Tersalin!') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto bg-slate-900 text-white border border-slate-700/80 px-4 py-3 rounded-2xl shadow-2xl flex items-center space-x-3 transition-all duration-300 transform translate-y-4 opacity-0 text-xs font-semibold';
        toast.innerHTML = `
            <div class="w-7 h-7 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold shrink-0">
                ✓
            </div>
            <div>
                <p class="font-extrabold text-white text-[11px]">${title}</p>
                <p class="text-slate-300 text-[10px]">${message}</p>
            </div>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        }, 10);

        setTimeout(() => {
            toast.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }
    </script>

    @stack('scripts')
</body>
</html>
