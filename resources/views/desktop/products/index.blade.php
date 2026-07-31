@extends('layouts.desktop')

@section('title', 'Manajemen Produk & Stok - E-Kasir')
@section('page_title', 'Inventaris Produk & Stok')

@section('content')
<div x-data="{ showAddModal: false, editModal: false, selectedProduct: {} }">
    <!-- Action Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <form method="GET" action="{{ route('desktop.products.index') }}" class="flex items-center space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau barcode..."
                   class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
            <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-slate-800">Cari</button>
        </form>

        <button @click="showAddModal = true" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-500/30 flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>+ Tambah Produk Baru</span>
        </button>
    </div>

    <!-- Products Datatable -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-4">Produk</th>
                        <th class="py-4 px-4">Barcode</th>
                        <th class="py-4 px-4">HPP (Modal)</th>
                        <th class="py-4 px-4">Harga Jual</th>
                        <th class="py-4 px-4">Margin Profit</th>
                        <th class="py-4 px-4">Stok</th>
                        <th class="py-4 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-100">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $p->name }}</p>
                                        <p class="text-[10px] text-slate-400">ID: #{{ $p->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">{{ $p->barcode ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-600">Rp {{ number_format($p->hpp, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 font-bold text-indigo-600">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 font-bold text-emerald-600">
                                + Rp {{ number_format($p->harga_jual - $p->hpp, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($p->stock <= 10)
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-700 font-extrabold rounded-lg">
                                        {{ $p->stock }} Unit
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 font-extrabold rounded-lg">
                                        {{ $p->stock }} Unit
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="editModal = true; selectedProduct = {{ json_encode($p) }}" class="p-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    <form action="{{ route('desktop.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
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
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada produk ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Modal Add Product -->
    <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4">
            <h3 class="font-extrabold text-slate-800 text-base">Tambah Produk Baru</h3>

            <form action="{{ route('desktop.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" required placeholder="Nama barang..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Barcode</label>
                    <input type="text" name="barcode" placeholder="Optional / Auto generate..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">HPP (Modal)</label>
                        <input type="number" name="hpp" required placeholder="0" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Harga Jual</label>
                        <input type="number" name="harga_jual" required placeholder="0" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-bold text-indigo-600">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Stok Awal</label>
                    <input type="number" name="stock" value="50" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Foto Produk</label>
                    <input type="file" name="image" class="w-full text-slate-500">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Product -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="editModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4">
            <h3 class="font-extrabold text-slate-800 text-base">Edit Produk</h3>

            <form :action="'/desktop/products/' + selectedProduct.id" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" x-model="selectedProduct.name" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Barcode</label>
                    <input type="text" name="barcode" x-model="selectedProduct.barcode" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">HPP (Modal)</label>
                        <input type="number" name="hpp" x-model="selectedProduct.hpp" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Harga Jual</label>
                        <input type="number" name="harga_jual" x-model="selectedProduct.harga_jual" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-bold text-indigo-600">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Stok</label>
                    <input type="number" name="stock" x-model="selectedProduct.stock" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ubah Foto Produk (Opsional)</label>
                    <input type="file" name="image" class="w-full text-slate-500">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
