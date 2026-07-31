<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Kasir Multi-Outlet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md" x-data="{ tab: 'login' }">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-500 shadow-lg shadow-indigo-500/30 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">E-Kasir POS</h1>
            <p class="text-indigo-200 text-sm mt-1">Sistem Kasir Multi-Outlet Terintegrasi</p>
        </div>

        <!-- Glass Container -->
        <div class="glass-card rounded-3xl p-6 md:p-8 shadow-2xl shadow-black/50">
            <!-- Tabs -->
            <div class="flex p-1 bg-gray-100/80 rounded-2xl mb-6">
                <button @click="tab = 'login'" 
                        :class="tab === 'login' ? 'bg-white text-indigo-600 shadow-md font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-2.5 text-xs md:text-sm rounded-xl transition-all duration-200 text-center">
                    Email & Password
                </button>
                <button @click="tab = 'pin'" 
                        :class="tab === 'pin' ? 'bg-white text-indigo-600 shadow-md font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-2.5 text-xs md:text-sm rounded-xl transition-all duration-200 text-center">
                    Login Cepat PIN
                </button>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Tab 1: Login Email -->
            <form x-show="tab === 'login'" action="{{ url('/login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Email</label>
                    <input type="email" name="email" required placeholder="owner@ekasir.com" value="{{ old('email') }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Pilih Outlet (Opsional)</label>
                    <select name="tenant_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                        <option value="">-- Semua Outlet (Default) --</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-600">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition-all transform active:scale-95 text-sm">
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Tab 2: Login Quick PIN (Kasir) -->
            <form x-show="tab === 'pin'" action="{{ route('quick-pin-login') }}" method="POST" class="space-y-4" style="display: none;">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Pilih Cabang Outlet</label>
                    <select name="tenant_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">PIN Kasir (6 Digit)</label>
                    <input type="password" name="pin" maxlength="6" pattern="[0-9]*" inputmode="numeric" required placeholder="123456"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm text-center text-2xl tracking-widest">
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold rounded-xl shadow-lg shadow-purple-500/30 transition-all transform active:scale-95 text-sm">
                    Masuk Cepat Kasir
                </button>
            </form>

            <!-- Demo Credentials Helper -->
            <div class="mt-6 pt-4 border-t border-gray-100 text-xs text-gray-500">
                <p class="font-bold text-gray-700 mb-1">Akun Uji Coba Demo:</p>
                <div class="space-y-1">
                    <p><span class="font-semibold text-indigo-600">Owner/Superadmin:</span> owner@ekasir.com / password</p>
                    <p><span class="font-semibold text-indigo-600">Manager Kemang:</span> manager.kemang@ekasir.com / password</p>
                    <p><span class="font-semibold text-indigo-600">Kasir (PIN 123456):</span> kasir.kemang@ekasir.com</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
