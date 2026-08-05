@extends('layouts.desktop')

@section('title', 'Manajemen Pengeluaran Operasional - E-Kasir')
@section('page_title', 'Pengeluaran Operasional Toko')

@section('content')
<div x-data="{ showAddModal: false, editModal: false, selectedExpense: {}, customCategory: false }">
    <!-- Filter & Action Header -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <form method="GET" action="{{ route('desktop.expenses.index') }}" class="flex flex-wrap items-center gap-3 text-xs font-bold">
            <div>
                <label class="block text-slate-400 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
            </div>
            <div>
                <label class="block text-slate-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
            </div>
            <div>
                <label class="block text-slate-400 mb-1">Pilih Kategori</label>
                <select name="category" class="px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-semibold">
                    <option value="">Semua Kategori</option>
                    @foreach($presetCategories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-slate-400 mb-1">Cari Keterangan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 w-44">
            </div>
            <div class="pt-5">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl shadow-md">
                    Filter
                </button>
            </div>
        </form>

        <div class="flex items-center space-x-3">
            <button @click="showAddModal = true" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl shadow-lg shadow-indigo-500/30 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>+ Catat Pengeluaran</span>
            </button>
        </div>
    </div>

    <!-- Summary Banner Card -->
    <div class="bg-gradient-to-r from-rose-500 via-rose-600 to-pink-600 rounded-3xl p-6 text-white shadow-xl mb-8 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-rose-200">Total Biaya Operasional Periode Ini</span>
                <h2 class="text-3xl font-black tracking-tight mt-1">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h2>
                <p class="text-xs text-rose-100 mt-1">Rentang: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
            <div class="p-4 bg-white/10 rounded-2xl backdrop-blur-md border border-white/20 text-xs font-semibold max-w-xs">
                <p>💡 Pengeluaran operasional (Gaji, Listrik, Bensin, dll) otomatis mengurangi Laba Bersih pada Laporan Keuangan outlet.</p>
            </div>
        </div>
    </div>

    <!-- Expenses Datatable -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-800 text-base">Rincian Pengeluaran Operasional</h3>
            <span class="text-xs font-bold text-slate-400">Total {{ $expenses->total() }} Transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-4">Tanggal</th>
                        <th class="py-4 px-4">Kategori</th>
                        <th class="py-4 px-4">Nominal</th>
                        <th class="py-4 px-4">Keterangan / Catatan</th>
                        <th class="py-4 px-4">Dicatat Oleh</th>
                        @if(auth()->user()->role === 'superadmin')
                            <th class="py-4 px-4">Outlet</th>
                        @endif
                        <th class="py-4 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    @forelse($expenses as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $item->expense_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 font-bold text-[11px] border border-slate-200">
                                    {{ $item->category }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-black text-rose-600 text-sm">
                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">
                                {{ $item->user->name ?? '-' }}
                            </td>
                            @if(auth()->user()->role === 'superadmin')
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-bold">
                                        {{ $item->tenant->name ?? 'Outlet' }}
                                    </span>
                                </td>
                            @endif
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="editModal = true; selectedExpense = {{ json_encode($item) }}" class="p-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    <form action="{{ route('desktop.expenses.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data pengeluaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">Belum ada pengeluaran operasional pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $expenses->links() }}
        </div>
    </div>

    <!-- Modal Add Expense -->
    <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-800 text-base">Catat Pengeluaran Operasional</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('desktop.expenses.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                @if(auth()->user()->role === 'superadmin')
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Outlet / Kantin</label>
                        <select name="tenant_id" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-semibold">
                            @foreach(\App\Models\Tenant::where('is_active', true)->get() as $t)
                                <option value="{{ $t->id }}" {{ (session('active_tenant_id') == $t->id) ? 'selected' : '' }}>{{ $t->name }} ({{ $t->code }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Pengeluaran</label>
                    <select name="category" x-on:change="customCategory = ($el.value === 'Lainnya')" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-semibold mb-2">
                        @foreach($presetCategories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                        <option value="Lainnya">+ Tambah Kategori Kustom</option>
                    </select>

                    <div x-show="customCategory">
                        <input type="text" x-bind:name="customCategory ? 'category' : ''" placeholder="Ketik nama kategori baru..." class="w-full px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-xl font-bold text-indigo-700">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nominal Biaya (Rp)</label>
                    <input type="number" name="amount" required placeholder="Contoh: 150000" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-bold text-rose-600 text-sm">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Pengeluaran</label>
                    <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Keterangan / Catatan (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Pembayaran listrik bulan ini, bensin kurir, dll..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-lg shadow-rose-600/30">Simpan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Expense -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="editModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-800 text-base">Edit Pengeluaran Operasional</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form :action="'/desktop/expenses/' + selectedExpense.id" method="POST" class="space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Pengeluaran</label>
                    <input type="text" name="category" x-model="selectedExpense.category" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nominal Biaya (Rp)</label>
                    <input type="number" name="amount" x-model="selectedExpense.amount" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-bold text-rose-600 text-sm">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Pengeluaran</label>
                    <input type="date" name="expense_date" x-model="selectedExpense.expense_date ? selectedExpense.expense_date.substring(0,10) : ''" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Keterangan / Catatan</label>
                    <textarea name="notes" x-model="selectedExpense.notes" rows="3" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
