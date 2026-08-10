@extends('layouts.desktop')

@section('title', 'Dashboard Utama - E-Kasir Multi-Outlet')
@section('page_title', 'Management Dashboard')

@section('content')
<div x-data="{ showImportModal: false }">
<!-- Top Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Omset -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Omset</span>
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        <p class="text-xs text-slate-400 mt-2 font-medium">Semua transaksi terakumulasi</p>
    </div>

    <!-- Card 2: Profit -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Profit Bersih</span>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-2xl font-black text-emerald-600 tracking-tight">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
        <p class="text-xs text-slate-400 mt-2 font-medium">Estimasi (Omset - HPP)</p>
    </div>

    <!-- Card 3: Transaksi -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Transaksi</span>
            <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalTransactions) }}</h3>
        <p class="text-xs text-slate-400 mt-2 font-medium">Struk terbit</p>
    </div>

    <!-- Card 4: Outlets -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Outlet Aktif</span>
            <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ $totalOutlets }} Cabang</h3>
        <p class="text-xs text-slate-400 mt-2 font-medium">Multi-tenant terhubung</p>
    </div>
</div>

<!-- Aktivitas Stok & Penjualan Hari Ini (WIB) Card Banner -->
<div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 shadow-xl mb-8 border border-slate-800">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 pb-4 border-b border-slate-800">
        <div>
            <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 font-extrabold text-[10px] rounded-full uppercase tracking-wider border border-indigo-500/30">
                Aktivitas Hari Ini ({{ now('Asia/Jakarta')->format('d M Y') }} - WIB)
            </span>
            <h3 class="text-lg font-black text-white mt-1">Ringkasan Barang Terjual & Perubahan Stok</h3>
        </div>
        <div class="flex items-center space-x-2 shrink-0 self-start md:self-auto">
            <a href="{{ route('backup.download') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition flex items-center space-x-1.5 shadow-lg shadow-emerald-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Backup DB (.SQL)</span>
            </a>
            <button type="button" @click="showImportModal = true" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl transition flex items-center space-x-1.5 shadow-lg shadow-amber-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                <span>Import DB (.SQL)</span>
            </button>
            <a href="{{ route('desktop.products.index') }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition">
                Stok Barang &rarr;
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-lg shrink-0">
                🛍️
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Barang Terjual Hari Ini</p>
                <p class="text-xl font-black text-emerald-400">{{ number_format($todayItemsSold) }} <span class="text-xs font-normal text-slate-300">Unit / Pcs</span></p>
            </div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-lg shrink-0">
                📦
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Barang Baru Ditambah Hari Ini</p>
                <p class="text-xl font-black text-blue-400">{{ number_format($todayProductsAddedCount) }} <span class="text-xs font-normal text-slate-300">Jenis Produk</span></p>
            </div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-lg shrink-0">
                🔄
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Stok Diupdate Hari Ini</p>
                <p class="text-xl font-black text-amber-400">{{ number_format($todayStockUpdatedCount) }} <span class="text-xs font-normal text-slate-300">Kali Restok</span></p>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section & Low Stock Warning -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Chart Penjualan 7 Hari -->
    <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-extrabold text-slate-800">Tren Penjualan & Profit (7 Hari Terakhir)</h3>
                <p class="text-xs text-slate-400">Grafik omset harian</p>
            </div>
        </div>

        <div class="h-72">
            <canvas id="salesTrendChart"></canvas>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-extrabold text-slate-800">Peringatan Stok Menipis</h3>
                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full">&le; 10 Unit</span>
            </div>

            <div class="space-y-3">
                @forelse($lowStockProducts as $lp)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <div>
                            <p class="font-bold text-xs text-slate-800">{{ $lp->name }}</p>
                            <p class="text-[10px] text-slate-400">{{ $lp->barcode }}</p>
                        </div>
                        <span class="px-2.5 py-1 bg-rose-100 text-rose-700 text-xs font-black rounded-lg">
                            Sisa {{ $lp->stock }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Semua stok produk mencukupi.</p>
                @endforelse
            </div>
        </div>

        <a href="{{ route('desktop.products.index') }}" class="mt-4 block text-center w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
            Kelola Seluruh Stok Produk &rarr;
        </a>
    </div>
</div>

<!-- Multi-Outlet Performance Table (Superadmin Only) -->
@if($isSuperAdmin && count($outletStats) > 0)
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8">
        <h3 class="text-base font-extrabold text-slate-800 mb-4">Ringkasan Performa Per Outlet</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase font-bold tracking-wider">
                        <th class="pb-3 px-3">Nama Outlet</th>
                        <th class="pb-3 px-3">Kode</th>
                        <th class="pb-3 px-3">Jumlah Transaksi</th>
                        <th class="pb-3 px-3">Total Omset</th>
                        <th class="pb-3 px-3">Profit Bersih</th>
                        <th class="pb-3 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    @foreach($outletStats as $os)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3 font-bold text-slate-900">{{ $os['tenant']->name }}</td>
                            <td class="py-3.5 px-3 text-slate-400">{{ $os['tenant']->code }}</td>
                            <td class="py-3.5 px-3">{{ number_format($os['tx_count']) }} transaksi</td>
                            <td class="py-3.5 px-3 font-extrabold text-indigo-600">Rp {{ number_format($os['sales'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 font-extrabold text-emerald-600">Rp {{ number_format($os['profit'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3">
                                <form action="{{ route('switch-tenant', $os['tenant']->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-[11px] font-bold rounded-xl transition">
                                        Lihat Context &rarr;
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

    <!-- Modal Import Database Desktop -->
    <div x-show="showImportModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showImportModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4 text-slate-800">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-base">Import Database (.SQL)</h3>
                </div>
                <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-xs space-y-1">
                <p class="font-extrabold text-amber-900 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Perhatian Penting!
                </p>
                <p class="text-amber-800 text-[11px] leading-relaxed">
                    Proses import akan memperbarui data tabel database sesuai dengan file backup SQL yang diunggah. Pastikan file yang dipilih adalah file `.sql` hasil backup resmi dari E-Kasir.
                </p>
            </div>

            <form action="{{ route('backup.import') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirmFormSubmit(event, 'Apakah Anda yakin ingin meng-import database ini? Data tabel akan diperbarui sesuai file backup.', 'Import Database', 'Ya, Restore & Import', 'warning')" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Pilih Berkas Backup (.SQL)</label>
                    <input type="file" 
                           name="backup_file" 
                           accept=".sql,.txt" 
                           required 
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 border border-slate-200 rounded-2xl p-1 bg-slate-50">
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showImportModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold rounded-xl text-xs shadow-lg shadow-amber-600/20 transition">
                        Restore & Import Database
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Omset Penjualan (Rp)',
                    data: @json($chartSalesData),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                },
                {
                    label: 'Profit Bersih (Rp)',
                    data: @json($chartProfitData),
                    borderColor: '#10b981',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    borderWidth: 2,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush
