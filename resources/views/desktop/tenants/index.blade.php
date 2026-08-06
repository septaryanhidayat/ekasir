@extends('layouts.desktop')

@section('title', 'Manajemen Outlet - E-Kasir')
@section('page_title', 'Kelola Cabang & Outlet Multi-Tenant')

@section('content')
<div x-data="{ showAddModal: false, editModal: false, selectedTenant: {} }">
    <!-- Action Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-base font-extrabold text-slate-800">Daftar Cabang Toko / Kantin</h3>
            <p class="text-xs text-slate-400">Pemisahan data multi-tenant</p>
        </div>

        <button @click="showAddModal = true" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-500/30 flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>+ Tambah Outlet Baru</span>
        </button>
    </div>

    <!-- Outlets Grid / Datatable -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($tenants as $t)
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-extrabold text-[10px] rounded-full uppercase tracking-wider">
                            {{ $t->code }}
                        </span>
                        @if($t->is_active)
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm"></span>
                        @else
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-sm"></span>
                        @endif
                    </div>

                    <h4 class="font-extrabold text-slate-900 text-base mb-1">{{ $t->name }}</h4>
                    <p class="text-xs text-slate-500 mb-2">{{ $t->address ?? 'Alamat belum diatur' }}</p>
                    <p class="text-xs text-slate-400 mb-3">Telp: {{ $t->phone ?? '-' }}</p>

                    <!-- Payment Methods Badges / Details -->
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 space-y-1.5 text-[11px]">
                        <div class="flex items-start space-x-1.5 text-slate-600">
                            <span class="font-bold text-indigo-600 min-w-[55px]">Bank:</span>
                            <span class="font-semibold text-slate-800">{{ $t->bank_info ?? 'Belum diatur' }}</span>
                        </div>
                        <div class="flex items-start space-x-1.5 text-slate-600">
                            <span class="font-bold text-teal-600 min-w-[55px]">E-Wallet:</span>
                            <span class="font-semibold text-slate-800">{{ $t->ewallet_info ?? 'Belum diatur' }}</span>
                        </div>
                        <div class="flex items-start space-x-1.5 text-slate-600">
                            <span class="font-bold text-emerald-600 min-w-[55px]">QRIS:</span>
                            <span class="font-semibold text-slate-800">{{ $t->qris_info ?? 'Standard Auto-Generate' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div class="space-x-3 text-slate-400 font-semibold">
                        <span>{{ $t->users_count }} Staf</span>
                        <span>•</span>
                        <span>{{ $t->products_count }} Produk</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button @click="editModal = true; selectedTenant = {{ json_encode($t) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg">
                            Edit
                        </button>

                        <form action="{{ route('desktop.tenants.toggle', $t->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 {{ $t->is_active ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} font-bold rounded-lg">
                                {{ $t->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Add Tenant -->
    <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="font-extrabold text-slate-800 text-base">Tambah Outlet Cabang Baru</h3>

            <form action="{{ route('desktop.tenants.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Outlet</label>
                    <input type="text" name="name" required placeholder="Contoh: Kantin Cabang BSD" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Outlet (Unik)</label>
                    <input type="text" name="code" required placeholder="OUT-004" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 uppercase font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="2" placeholder="Jl..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" placeholder="08..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <h5 class="font-extrabold text-indigo-600 mb-2">Setting Rekening & QRIS</h5>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Rekening Bank</label>
                            <input type="text" name="bank_info" placeholder="BCA 1234567890 a/n Kantin" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">E-Wallet (GoPay/OVO/DANA)</label>
                            <input type="text" name="ewallet_info" placeholder="081234567890 a/n Kantin" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Info / Code Payload QRIS</label>
                            <input type="text" name="qris_info" placeholder="NMID / Code QRIS Static / Text" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">Simpan Outlet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Tenant -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="editModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="font-extrabold text-slate-800 text-base">Edit Setting Outlet & Pembayaran</h3>

            <form :action="'/desktop/tenants/' + selectedTenant.id" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Outlet</label>
                    <input type="text" name="name" x-model="selectedTenant.name" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Outlet</label>
                    <input type="text" name="code" x-model="selectedTenant.code" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 uppercase font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alamat</label>
                    <textarea name="address" x-model="selectedTenant.address" rows="2" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Telepon</label>
                    <input type="text" name="phone" x-model="selectedTenant.phone" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <h5 class="font-extrabold text-indigo-600 mb-2">Setting Rekening, E-Wallet & QRIS</h5>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Rekening Bank</label>
                            <input type="text" name="bank_info" x-model="selectedTenant.bank_info" placeholder="Contoh: BCA 1234567890 a/n Kantin Robbani" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">E-Wallet (GoPay/OVO/DANA)</label>
                            <input type="text" name="ewallet_info" x-model="selectedTenant.ewallet_info" placeholder="Contoh: 081234567890 a/n Robbani Mart" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Info / Payload QRIS (NMID/Code)</label>
                            <input type="text" name="qris_info" x-model="selectedTenant.qris_info" placeholder="Contoh: ID10293847561 atau Kode QRIS Static" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">Update Setting</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
