@extends('layouts.mobile')

@section('title', 'Beranda - E-Kasir Mobile')

@section('content')
<!-- Kas Operasional Card (Livin/BCA Mobile Style) -->
<div class="glass-card rounded-3xl p-6 shadow-xl relative overflow-hidden mb-6">
    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

    <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Sisa Kas Operasional Laci</span>
        @if($openRegister)
            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-extrabold text-[11px] flex items-center">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-ping"></span>
                Kas Aktif
            </span>
        @else
            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 font-extrabold text-[11px]">
                Kas Ditutup
            </span>
        @endif
    </div>

    <div class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">
        Rp {{ number_format($cashBalance, 0, ',', '.') }}
    </div>

    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100 text-xs">
        <div class="flex items-center text-slate-600">
            <div class="w-7 h-7 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mr-2 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400">Modal Awal</p>
                <p class="font-bold text-slate-800">Rp {{ number_format($openRegister->opening_amount ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="flex items-center text-slate-600">
            <div class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mr-2 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400">Penjualan Hari Ini</p>
                <p class="font-bold text-slate-800">Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Customer Online Orders Notification Card -->
<div class="mb-6">
    <a href="{{ route('mobile.online-orders') }}" class="glass-card p-4 rounded-3xl shadow-lg border border-indigo-200/80 flex items-center justify-between hover:bg-indigo-50/50 transition">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                @if($pendingOnlineOrdersCount > 0)
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 text-white font-extrabold text-[9px] rounded-full flex items-center justify-center animate-bounce">
                        {{ $pendingOnlineOrdersCount }}
                    </span>
                @endif
            </div>
            <div>
                <h4 class="font-extrabold text-slate-800 text-xs">Pesanan Customer Online</h4>
                <p class="text-[11px] text-slate-500">{{ $pendingOnlineOrdersCount }} Pesanan Perlu Diproses</p>
            </div>
        </div>
        <span class="text-xs font-bold text-indigo-600">Lihat &rarr;</span>
    </a>
</div>

<!-- Daily Stock & Sales Activity Summary Card -->
<div class="mb-6 bg-gradient-to-tr from-slate-900 via-indigo-950 to-slate-900 text-white p-5 rounded-3xl shadow-xl border border-slate-800 space-y-3">
    <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
        <div class="flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <h4 class="font-extrabold text-white text-xs uppercase tracking-wider">Aktivitas Stok & Sales Hari Ini (WIB)</h4>
        </div>
        <span class="text-[10px] text-indigo-300 font-bold bg-indigo-500/20 px-2 py-0.5 rounded-full border border-indigo-500/30">
            {{ now('Asia/Jakarta')->format('d M') }}
        </span>
    </div>

    <div class="grid grid-cols-2 gap-3 text-xs">
        <div class="bg-white/5 p-3 rounded-2xl border border-white/10 flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-base shrink-0">
                🛍️
            </div>
            <div>
                <p class="text-[9px] text-slate-400 font-bold uppercase">Terjual</p>
                <p class="font-black text-emerald-400 text-sm">{{ number_format($todayItemsSold) }} <span class="text-[9px] text-slate-300 font-normal">Unit</span></p>
            </div>
        </div>

        <div class="bg-white/5 p-3 rounded-2xl border border-white/10 flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-base shrink-0">
                📦
            </div>
            <div>
                <p class="text-[9px] text-slate-400 font-bold uppercase">Update Stok</p>
                <p class="font-black text-amber-400 text-sm">{{ number_format($todayStockUpdatedCount) }} <span class="text-[9px] text-slate-300 font-normal">Kali</span></p>
            </div>
        </div>
    </div>
</div>

<!-- 3D Glassmorphism Action Grid Menu -->
<div class="mb-6">
    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 px-1">Menu Utama</h3>
    
    <div class="grid grid-cols-2 gap-4">
        <!-- Menu 1: POS Kasir -->
        <a href="{{ route('mobile.pos') }}" class="group glass-card p-5 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white flex items-center justify-center mb-3 shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                </svg>
            </div>
            <h4 class="font-extrabold text-slate-800 text-sm">Kasir POS</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">Scan & Go Transaksi</p>
        </a>

        <!-- Menu 2: Smart Input -->
        <a href="{{ route('mobile.smart-input') }}" class="group glass-card p-5 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-600 text-white flex items-center justify-center mb-3 shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h0.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 001.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h4 class="font-extrabold text-slate-800 text-sm">Tambah Barang</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">Kamera & Barcode</p>
        </a>

        <!-- Menu 3: Kas Harian -->
        <a href="{{ route('mobile.cash-register') }}" class="group glass-card p-5 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white flex items-center justify-center mb-3 shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h4 class="font-extrabold text-slate-800 text-sm">Kas Harian</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">Open/Close & Petty</p>
        </a>

        <!-- Menu 4: Riwayat -->
        <a href="{{ route('mobile.transactions') }}" class="group glass-card p-5 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-500 to-blue-600 text-white flex items-center justify-center mb-3 shadow-lg shadow-sky-500/30 group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h4 class="font-extrabold text-slate-800 text-sm">Riwayat Struk</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">{{ $todayTransactionsCount }} Transaksi Hari Ini</p>
        </a>

        <!-- Menu Suplier / Distributor -->
        <a href="{{ route('mobile.suppliers') }}" class="group glass-card p-5 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden flex flex-col items-center text-center col-span-2 border border-emerald-200/80 bg-emerald-50/30">
            <div class="flex items-center space-x-3 text-left w-full">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-700 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30 shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-800 text-sm">Suplier & Distributor</h4>
                    <p class="text-[11px] text-slate-500">Kelola kontak suplier & hubungi via WhatsApp</p>
                </div>
            </div>
        </a>

        @if(auth()->check() && (auth()->user()->role === 'superadmin' || auth()->user()->role === 'manager'))
            <!-- Menu 5: Manajemen Produk & Stok (Khusus Admin/Manajer) -->
            <a href="{{ route('desktop.products.index') }}" class="group glass-card p-5 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden flex flex-col items-center text-center col-span-2 border border-indigo-200/80 bg-indigo-50/40">
                <div class="flex items-center space-x-3 text-left w-full">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-700 text-white flex items-center justify-center shadow-lg shadow-blue-500/30 shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm">Manajemen Produk & Stok</h4>
                        <p class="text-[11px] text-slate-500">Kelola inventaris, barcode, & update stok barang</p>
                    </div>
                </div>
            </a>
        @endif
    </div>
</div>

<!-- Floating Action Button Scanner Quick Access -->
<div class="text-center mt-6">
    <a href="{{ route('mobile.pos') }}?scan=1" class="inline-flex items-center space-x-2 px-6 py-3.5 rounded-full bg-slate-900 text-white text-xs font-bold shadow-2xl hover:bg-slate-800 transition transform active:scale-95 border border-slate-700">
        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
        </svg>
        <span>Buka Scanner Barcode Langsung</span>
    </a>
</div>
@endsection
