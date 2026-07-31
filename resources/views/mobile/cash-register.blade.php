@extends('layouts.mobile')

@section('title', 'Kas Harian - E-Kasir')

@section('content')
<div x-data="{ tab: 'status' }" class="space-y-4">

    <!-- Active Shift Status Card -->
    <div class="glass-card rounded-3xl p-6 shadow-xl relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-extrabold text-slate-800 text-sm">Status Kas Harian (Shift)</h3>
            @if($openRegister)
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 font-extrabold text-[10px] rounded-full">KAS DIBUKA</span>
            @else
                <span class="px-3 py-1 bg-amber-100 text-amber-700 font-extrabold text-[10px] rounded-full">KAS DITUTUP</span>
            @endif
        </div>

        @if($openRegister)
            <div class="mt-3 space-y-2 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Waktu Buka Kas:</span>
                    <span class="font-bold text-slate-800">{{ $openRegister->opened_at->format('d M Y H:i') }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Modal Awal Laci:</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($openRegister->opening_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Total Penjualan Tunai:</span>
                    <span class="font-bold text-emerald-600">+ Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Petty Cash Masuk (+):</span>
                    <span class="font-bold text-indigo-600">+ Rp {{ number_format($totalCashIn, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Petty Cash Keluar (-):</span>
                    <span class="font-bold text-rose-600">- Rp {{ number_format($totalCashOut, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between py-2 font-black text-sm bg-indigo-50 p-3 rounded-2xl text-indigo-950 mt-2">
                    <span>Estimasi Uang Fisik Laci:</span>
                    <span class="text-indigo-600">Rp {{ number_format($cashBalance, 0, ',', '.') }}</span>
                </div>
            </div>
        @else
            <div class="py-4 text-center text-slate-500 text-xs">
                Belum ada kas harian aktif. Silakan lakukan "Buka Kas" untuk memulai shift transaksi.
            </div>
        @endif
    </div>

    <!-- Action Forms Grid -->
    @if(!$openRegister)
        <!-- Form Buka Kas -->
        <div class="glass-card rounded-3xl p-6 shadow-xl">
            <h3 class="font-extrabold text-slate-800 text-sm mb-4">Buka Kas Baru (Shift Shift)</h3>

            <form action="{{ route('mobile.cash-register.open') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Modal Awal Laci Kas</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-xs text-slate-400 font-bold">Rp</span>
                        <input type="number" name="opening_amount" required value="200000"
                               class="w-full pl-10 pr-4 py-3 bg-slate-100 rounded-2xl text-slate-800 font-extrabold text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Shift (Opsional)</label>
                    <input type="text" name="notes" placeholder="Contoh: Shift Pagi Kasir Andi"
                           class="w-full px-4 py-3 bg-slate-100 rounded-2xl text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit" class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-indigo-500/30 transform active:scale-95 transition">
                    Mulai Shift & Buka Kas
                </button>
            </form>
        </div>
    @else
        <!-- Action Options: Petty Cash & Close Register -->
        <div class="glass-card rounded-3xl p-6 shadow-xl space-y-6">
            <!-- Form Petty Cash -->
            <div>
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-3">Catat Kas Keluar / Masuk (Petty Cash)</h4>
                
                <form action="{{ route('mobile.cash-register.cash-flow') }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center p-3 bg-rose-50 rounded-2xl border border-rose-200 cursor-pointer">
                            <input type="radio" name="type" value="out" checked class="text-rose-600">
                            <span class="ml-2 text-xs font-bold text-rose-700">Kas Keluar (-)</span>
                        </label>
                        <label class="flex items-center justify-center p-3 bg-indigo-50 rounded-2xl border border-indigo-200 cursor-pointer">
                            <input type="radio" name="type" value="in" class="text-indigo-600">
                            <span class="ml-2 text-xs font-bold text-indigo-700">Kas Masuk (+)</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Nominal</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-xs text-slate-400 font-bold">Rp</span>
                            <input type="number" name="amount" required placeholder="0"
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-100 rounded-2xl text-slate-800 font-bold text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Keterangan</label>
                        <input type="text" name="description" required placeholder="Contoh: Beli Es Batu 2 Plastik"
                               class="w-full px-4 py-2.5 bg-slate-100 rounded-2xl text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <button type="submit" class="w-full py-3 bg-slate-900 text-white font-bold text-xs rounded-2xl shadow-md">
                        Simpan Catatan Kas
                    </button>
                </form>
            </div>

            <hr class="border-slate-100">

            <!-- Form Tutup Kas -->
            <div>
                <h4 class="font-bold text-rose-700 text-xs uppercase tracking-wider mb-3">Tutup Kas & Rekap Shift</h4>
                
                <form action="{{ route('mobile.cash-register.close') }}" method="POST" class="space-y-3" onsubmit="return confirm('Apakah Anda yakin ingin menutup kas shift ini?')">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Total Uang Fisik Di Laci (Hasil Hitung)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-xs text-slate-400 font-bold">Rp</span>
                            <input type="number" name="closing_amount" required value="{{ $cashBalance }}"
                                   class="w-full pl-10 pr-4 py-3 bg-slate-100 rounded-2xl text-slate-900 font-black text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">*Sistem akan menghitung selisih otomatis antara fisik dan estimasi.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Catatan Penutupan</label>
                        <input type="text" name="notes" placeholder="Catatan selisih atau serah terima..."
                               class="w-full px-4 py-2.5 bg-slate-100 rounded-2xl text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-rose-500/30 transition">
                        Tutup Kas Shift & Rekapitulasi
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
