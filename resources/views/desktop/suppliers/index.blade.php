@extends('layouts.desktop')

@section('title', 'Manajemen Suplier & Distributor - E-Kasir')
@section('page_title', 'Suplier & Distributor')

@section('content')
<div x-data="{ 
    showAddModal: false, 
    editModal: false, 
    selectedSupplier: {},
    formatPhoneForWa(phone) {
        if (!phone) return '';
        let clean = phone.replace(/[^0-9]/g, '');
        if (clean.startsWith('0')) {
            clean = '62' + clean.slice(1);
        }
        return clean;
    }
}">
    <!-- Summary Header Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl shrink-0">
                🏢
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Total Suplier</p>
                <h3 class="text-2xl font-black text-slate-800">{{ number_format($totalSuppliers) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl shrink-0">
                ✅
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Suplier Aktif</p>
                <h3 class="text-2xl font-black text-emerald-600">{{ number_format($activeSuppliers) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-xl shrink-0">
                🚚
            </div>
            <div>
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Kemitraan Outlet</p>
                <h3 class="text-xs font-bold text-slate-700 mt-1">
                    {{ auth()->user()->tenant?->name ?? 'Semua Outlet' }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Action Bar & Search -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" action="{{ route('desktop.suppliers.index') }}" class="flex items-center space-x-2 w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, no hp, atau alamat..."
                   class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-80">
            <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-slate-800 shrink-0">Cari</button>
            @if(request('search'))
                <a href="{{ route('desktop.suppliers.index') }}" class="px-3 py-2.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 shrink-0">Reset</a>
            @endif
        </form>

        <button @click="showAddModal = true; selectedSupplier = {}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-500/30 flex items-center justify-center space-x-1.5 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>+ Tambah Suplier Baru</span>
        </button>
    </div>

    <!-- Mobile Cards List (Visible on Mobile) -->
    <div class="grid grid-cols-1 gap-3 md:hidden mb-4">
        @forelse($suppliers as $s)
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-mono font-bold text-[10px] rounded">{{ $s->code }}</span>
                        <h4 class="font-extrabold text-slate-900 text-sm mt-1">{{ $s->name }}</h4>
                    </div>
                    @if($s->is_active)
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-extrabold rounded-full text-[10px]">Aktif</span>
                    @else
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-500 font-extrabold rounded-full text-[10px]">Non-Aktif</span>
                    @endif
                </div>

                <div class="space-y-1 text-xs text-slate-600">
                    @if($s->phone)
                        <p class="flex items-center space-x-1.5">
                            <span class="text-slate-400">📱 No. Telp/WA:</span>
                            <span class="font-bold text-slate-800">{{ $s->phone }}</span>
                        </p>
                    @endif
                    @if($s->email)
                        <p class="flex items-center space-x-1.5">
                            <span class="text-slate-400">✉️ Email:</span>
                            <span class="font-medium text-slate-700">{{ $s->email }}</span>
                        </p>
                    @endif
                    @if($s->address)
                        <p class="text-slate-500 text-[11px] truncate">
                            <span class="text-slate-400">📍 Alamat:</span> {{ $s->address }}
                        </p>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold">{{ $s->products_count }} Produk Terkait</span>
                    
                    <div class="flex items-center space-x-1.5">
                        @if($s->phone)
                            <a :href="'https://wa.me/' + formatPhoneForWa('{{ $s->phone }}')" target="_blank" class="px-2.5 py-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold text-[11px] rounded-lg flex items-center space-x-1 transition">
                                <span>WA Chat</span>
                            </a>
                        @endif
                        <button @click="selectedSupplier = {{ json_encode($s) }}; editModal = true" class="px-2.5 py-1 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-[11px] rounded-lg transition">
                            Edit
                        </button>
                        <form action="{{ route('desktop.suppliers.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus suplier {{ $s->name }}?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-[11px] rounded-lg transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-6 text-center text-slate-400 text-xs">
                Belum ada data suplier/distributor yang terdaftar.
            </div>
        @endforelse
    </div>

    <!-- Desktop Datatable -->
    <div class="hidden md:block bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-4">Kode</th>
                        <th class="py-4 px-4">Nama Suplier / Distributor</th>
                        <th class="py-4 px-4">Kontak (HP / WhatsApp)</th>
                        <th class="py-4 px-4">Email</th>
                        <th class="py-4 px-4">Alamat & Catatan</th>
                        <th class="py-4 px-4 text-center">Status</th>
                        <th class="py-4 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    @forelse($suppliers as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-indigo-600">
                                <span class="px-2 py-1 bg-indigo-50 rounded-lg">{{ $s->code }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-extrabold text-slate-900 text-sm">{{ $s->name }}</p>
                                <p class="text-[10px] text-slate-400">{{ $s->products_count }} Produk Terkait</p>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($s->phone)
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-slate-800">{{ $s->phone }}</span>
                                        <a :href="'https://wa.me/' + formatPhoneForWa('{{ $s->phone }}')" target="_blank" title="Hubungi via WhatsApp" class="p-1 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                            </svg>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $s->email ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="text-slate-700 truncate" title="{{ $s->address }}">{{ $s->address ?: '-' }}</p>
                                @if($s->notes)
                                    <p class="text-[10px] text-slate-400 italic truncate" title="{{ $s->notes }}">Catatan: {{ $s->notes }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($s->is_active)
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 font-extrabold rounded-lg text-[10px]">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-500 font-extrabold rounded-lg text-[10px]">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <button @click="selectedSupplier = {{ json_encode($s) }}; editModal = true" title="Edit Suplier" class="p-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    <form action="{{ route('desktop.suppliers.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus suplier {{ $s->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Suplier" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition">
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
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data suplier/distributor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $suppliers->links() }}
        </div>
    </div>

    <!-- Modal Add Supplier -->
    <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="font-extrabold text-slate-800 text-base">Tambah Suplier / Distributor Baru</h3>

            <form action="{{ route('desktop.suppliers.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                @if(auth()->user()->role === 'superadmin')
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Outlet / Tenant</label>
                        <select name="tenant_id" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-semibold">
                            @foreach(\App\Models\Tenant::where('is_active', true)->get() as $t)
                                <option value="{{ $t->id }}" {{ (session('active_tenant_id') == $t->id) ? 'selected' : '' }}>{{ $t->name }} ({{ $t->code }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Suplier / Distributor <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: PT Indofood / CV Maju Jaya..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Suplier (Opsional)</label>
                    <input type="text" name="code" placeholder="Kosongkan untuk pembuat sistem (SUP-xxx)..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-mono">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">No. HP / WhatsApp</label>
                        <input type="text" name="phone" placeholder="08123456789..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email (Opsional)</label>
                        <input type="email" name="email" placeholder="sales@distributor.com" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alamat Gudang / Kantor</label>
                    <textarea name="address" rows="2" placeholder="Jl. Raya Industri No. 123..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Catatan termin pembayaran / sales person..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">Simpan Suplier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Supplier -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="editModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="font-extrabold text-slate-800 text-base">Edit Data Suplier / Distributor</h3>

            <form :action="'{{ url('desktop/suppliers') }}/' + selectedSupplier.id" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Suplier / Distributor <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="selectedSupplier.name" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Suplier</label>
                    <input type="text" name="code" x-model="selectedSupplier.code" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-mono">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">No. HP / WhatsApp</label>
                        <input type="text" name="phone" x-model="selectedSupplier.phone" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" x-model="selectedSupplier.email" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alamat Gudang / Kantor</label>
                    <textarea name="address" x-model="selectedSupplier.address" rows="2" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan</label>
                    <textarea name="notes" x-model="selectedSupplier.notes" rows="2" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Keaktifan</label>
                    <select name="is_active" x-model="selectedSupplier.is_active" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-semibold">
                        <option :value="1">Aktif</option>
                        <option :value="0">Non-Aktif</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">Update Suplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
