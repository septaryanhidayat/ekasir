@extends('layouts.mobile')

@section('title', 'Suplier & Distributor - E-Kasir Mobile')

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
    <!-- Header Title Card -->
    <div class="glass-card rounded-3xl p-5 shadow-xl border border-indigo-200/80 mb-5 flex items-center justify-between">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200/50">Mitra & Pasokan</span>
            <h3 class="text-base font-extrabold text-slate-800 mt-1">Suplier & Distributor</h3>
            <p class="text-xs text-slate-500">{{ $totalSuppliers }} Suplier Terdaftar</p>
        </div>
        <button @click="showAddModal = true; selectedSupplier = {}" class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30 active:scale-95 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </button>
    </div>

    <!-- Search Form -->
    <div class="mb-4">
        <form method="GET" action="{{ route('mobile.suppliers') }}" class="flex items-center space-x-2">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, hp, atau kota..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-2xl shadow hover:bg-slate-800">Cari</button>
        </form>
    </div>

    <!-- Supplier List Cards -->
    <div class="space-y-3 mb-6">
        @forelse($suppliers as $s)
            <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-mono font-bold text-[10px] rounded-lg">{{ $s->code }}</span>
                        <h4 class="font-extrabold text-slate-900 text-sm mt-1 leading-tight">{{ $s->name }}</h4>
                    </div>
                    @if($s->is_active)
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold rounded-full text-[10px]">Aktif</span>
                    @else
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-500 font-bold rounded-full text-[10px]">Non-Aktif</span>
                    @endif
                </div>

                <div class="space-y-1 text-xs text-slate-600">
                    @if($s->phone)
                        <div class="flex items-center space-x-2">
                            <span class="text-slate-400 font-medium">HP:</span>
                            <span class="font-bold text-slate-800">{{ $s->phone }}</span>
                        </div>
                    @endif

                    @if($s->address)
                        <div class="flex items-start space-x-1.5 text-[11px] text-slate-500">
                            <span class="text-slate-400 shrink-0">📍</span>
                            <p class="line-clamp-2">{{ $s->address }}</p>
                        </div>
                    @endif

                    @if($s->notes)
                        <div class="text-[10px] text-slate-400 italic">
                            Catatan: {{ $s->notes }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <span class="text-[10px] text-slate-400 font-bold">{{ $s->products_count }} Produk Terkait</span>

                    <div class="flex items-center space-x-1.5">
                        @if($s->phone)
                            <a :href="'https://wa.me/' + formatPhoneForWa('{{ $s->phone }}')" target="_blank" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-[11px] rounded-xl flex items-center space-x-1 shadow-sm transition">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                </svg>
                                <span>WA Chat</span>
                            </a>
                        @endif

                        <button @click="selectedSupplier = {{ json_encode($s) }}; editModal = true" class="px-2.5 py-1.5 bg-slate-100 text-slate-700 font-bold text-[11px] rounded-xl transition">
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 text-center text-slate-400 text-xs">
                Belum ada data suplier atau distributor.
            </div>
        @endforelse
    </div>

    <!-- Modal Add Supplier Mobile -->
    <div x-show="showAddModal" x-transition class="fixed inset-0 z-[100] bg-black/60 flex items-end sm:items-center justify-center p-0 sm:p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white rounded-t-3xl sm:rounded-3xl p-6 pb-10 w-full max-w-md shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-800 text-sm">Tambah Suplier Baru</h3>
                <button @click="showAddModal = false" class="text-slate-400 font-bold text-lg">&times;</button>
            </div>

            <form action="{{ route('mobile.suppliers.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Suplier / Distributor *</label>
                    <input type="text" name="name" required placeholder="PT Indofood / CV Maju Jaya..." class="w-full px-3.5 py-2.5 bg-slate-100 rounded-2xl border border-slate-200 font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" placeholder="08123456789..." class="w-full px-3.5 py-2.5 bg-slate-100 rounded-2xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alamat</label>
                    <textarea name="address" rows="2" placeholder="Jl. Raya No. 12..." class="w-full px-3.5 py-2.5 bg-slate-100 rounded-2xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan supplier..." class="w-full px-3.5 py-2.5 bg-slate-100 rounded-2xl border border-slate-200"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-2xl">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/30">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Supplier Mobile -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-[100] bg-black/60 flex items-end sm:items-center justify-center p-0 sm:p-4" style="display: none;">
        <div @click.away="editModal = false" class="bg-white rounded-t-3xl sm:rounded-3xl p-6 pb-10 w-full max-w-md shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-800 text-sm">Edit Data Suplier</h3>
                <button @click="editModal = false" class="text-slate-400 font-bold text-lg">&times;</button>
            </div>

            <form :action="'{{ url('m/suppliers') }}/' + selectedSupplier.id" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Suplier *</label>
                    <input type="text" name="name" x-model="selectedSupplier.name" required class="w-full px-3.5 py-2.5 bg-slate-100 rounded-2xl border border-slate-200 font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" x-model="selectedSupplier.phone" class="w-full px-3.5 py-2.5 bg-slate-100 rounded-2xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alamat</label>
                    <textarea name="address" x-model="selectedSupplier.address" rows="2" class="w-full px-3.5 py-2.5 bg-slate-100 rounded-2xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan</label>
                    <textarea name="notes" x-model="selectedSupplier.notes" rows="2" class="w-full px-3.5 py-2.5 bg-slate-100 rounded-2xl border border-slate-200"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-2xl">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/30">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
