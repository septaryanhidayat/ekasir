@extends('layouts.desktop')

@section('title', 'Laporan Keuangan & Kas - E-Kasir')
@section('page_title', 'Laporan Keuangan & Kas Harian')

@section('content')
<!-- Date Filter Form -->
<div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h3 class="text-base font-extrabold text-slate-800">Filter Periode Laporan</h3>
        <p class="text-xs text-slate-400">Pilih rentang tanggal untuk melihat rekapitulasi</p>
    </div>

    <form method="GET" action="{{ route('desktop.reports.index') }}" class="flex items-center space-x-3 text-xs font-bold">
        <div>
            <label class="block text-slate-400 mb-1">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
        </div>
        <div>
            <label class="block text-slate-400 mb-1">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
        </div>
        <div class="pt-5">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md">
                Tampilkan Laporan
            </button>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Omset Penjualan</span>
        <h3 class="text-2xl font-black text-slate-900 mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total HPP (Modal)</span>
        <h3 class="text-2xl font-black text-slate-600 mt-2">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h3>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Profit Bersih</span>
        <h3 class="text-2xl font-black text-emerald-600 mt-2">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
    </div>
</div>

<!-- Sales Transactions Table -->
<div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8">
    <h3 class="text-base font-extrabold text-slate-800 mb-4">Detail Transaksi Penjualan</h3>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                    <th class="py-3 px-4">No. Invoice</th>
                    <th class="py-3 px-4">Waktu</th>
                    <th class="py-3 px-4">Kasir</th>
                    <th class="py-3 px-4">Jumlah Item</th>
                    <th class="py-3 px-4">Omset</th>
                    <th class="py-3 px-4">Profit</th>
                    <th class="py-3 px-4">Metode</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                @forelse($transactions as $t)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-4 font-bold text-indigo-600">{{ $t->invoice_number }}</td>
                        <td class="py-3 px-4 text-slate-500">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4">{{ $t->user->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $t->details->sum('qty') }} barang</td>
                        <td class="py-3 px-4 font-bold text-slate-900">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 font-bold text-emerald-600">Rp {{ number_format($t->total_amount - $t->total_hpp, 0, ',', '.') }}</td>
                        <td class="py-3 px-4"><span class="px-2 py-0.5 bg-slate-100 rounded font-bold uppercase text-[10px]">{{ $t->payment_method }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-400">Belum ada transaksi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</div>

<!-- Cash Registers History Table -->
<div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
    <h3 class="text-base font-extrabold text-slate-800 mb-4">Riwayat Kas Harian & Selisih Laci</h3>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                    <th class="py-3 px-4">Kasir / User</th>
                    <th class="py-3 px-4">Waktu Buka</th>
                    <th class="py-3 px-4">Waktu Tutup</th>
                    <th class="py-3 px-4">Modal Awal</th>
                    <th class="py-3 px-4">Estimasi Laci</th>
                    <th class="py-3 px-4">Fisik Laci</th>
                    <th class="py-3 px-4">Selisih</th>
                    <th class="py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                @forelse($cashRegisters as $cr)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-4 font-bold text-slate-900">{{ $cr->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-slate-500">{{ $cr->opened_at->format('d/m H:i') }}</td>
                        <td class="py-3 px-4 text-slate-500">{{ $cr->closed_at ? $cr->closed_at->format('d/m H:i') : '-' }}</td>
                        <td class="py-3 px-4">Rp {{ number_format($cr->opening_amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-4">Rp {{ number_format($cr->expected_amount ?? 0, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 font-bold">Rp {{ number_format($cr->closing_amount ?? 0, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 font-bold">
                            @if(($cr->variance ?? 0) < 0)
                                <span class="text-rose-600">Selisih Kurang: Rp {{ number_format($cr->variance, 0, ',', '.') }}</span>
                            @elseif(($cr->variance ?? 0) > 0)
                                <span class="text-emerald-600">Selisih Lebih: +Rp {{ number_format($cr->variance, 0, ',', '.') }}</span>
                            @else
                                <span class="text-slate-400">Sesuai (Rp 0)</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($cr->status === 'open')
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold rounded text-[10px]">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold rounded text-[10px]">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-slate-400">Belum ada riwayat kas harian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
