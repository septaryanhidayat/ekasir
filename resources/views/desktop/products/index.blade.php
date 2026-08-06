@extends('layouts.desktop')

@section('title', 'Manajemen Produk & Stok - E-Kasir')
@section('page_title', 'Inventaris Produk & Stok')

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #print-barcode-wrapper, #print-barcode-wrapper * {
        visibility: visible !important;
    }
    #print-barcode-wrapper {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        background: white !important;
        display: block !important;
    }
    .barcode-card-item {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
</style>
@endpush

@section('content')
<div x-data="{ 
    showAddModal: false, 
    editModal: false, 
    barcodeModal: false, 
    bulkBarcodeModal: false,
    selectedProduct: {}, 
    customPrintPrice: 0, 
    printQty: 12,
    showTenant: true,
    showProductName: true,
    showBarcodeImg: true,
    showBarcodeCode: true,
    showPrice: true,

    selectedProductIds: [],
    selectAll: false,
    bulkProducts: [],
    bulkDefaultQty: 6,

    toggleSelectAll(productsList) {
        if (this.selectAll) {
            this.selectedProductIds = productsList.map(p => p.id);
        } else {
            this.selectedProductIds = [];
        }
    },

    openBulkPrint(allProductsList) {
        let listToPrint = [];
        if (this.selectedProductIds.length > 0) {
            listToPrint = allProductsList.filter(p => this.selectedProductIds.includes(p.id));
        } else {
            listToPrint = allProductsList;
        }

        this.bulkProducts = listToPrint.map(p => ({
            id: p.id,
            name: p.name,
            barcode: p.barcode,
            harga_jual: p.harga_jual,
            print_price: p.harga_jual,
            print_qty: this.bulkDefaultQty || 6
        }));

        this.bulkBarcodeModal = true;
    },

    setAllBulkQty(qty) {
        this.bulkDefaultQty = qty;
        this.bulkProducts.forEach(p => p.print_qty = qty);
    },

    removeBulkItem(index) {
        this.bulkProducts.splice(index, 1);
    },

    get totalBulkStickers() {
        return this.bulkProducts.reduce((sum, p) => sum + parseInt(p.print_qty || 0), 0);
    },

    get flattenedBulkItems() {
        let result = [];
        if (!this.bulkProducts || !Array.isArray(this.bulkProducts)) return result;
        this.bulkProducts.forEach((item, prodIdx) => {
            const qty = Math.max(1, parseInt(item.print_qty || 1));
            for (let i = 0; i < qty; i++) {
                result.push({
                    ...item,
                    unique_key: 'prod-' + (item.id || prodIdx) + '-sticker-' + i
                });
            }
        });
        return result;
    },

    formatDateTimeLocal(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return '';
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }
}">
    <!-- Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" action="{{ route('desktop.products.index') }}" class="flex items-center space-x-2 w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau barcode..."
                   class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-64">
            <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-slate-800 shrink-0">Cari</button>
        </form>

        <div class="flex items-center space-x-2 w-full sm:w-auto">
            <!-- Bulk Print Button -->
            <button @click="openBulkPrint({{ json_encode($products->items()) }})" class="flex-1 sm:flex-none px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20 flex items-center justify-center space-x-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span x-text="selectedProductIds.length > 0 ? 'Cetak Massal (' + selectedProductIds.length + ')' : 'Cetak Massal'"></span>
            </button>

            <button @click="showAddModal = true; selectedProduct = {}" class="flex-1 sm:flex-none px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-500/30 flex items-center justify-center space-x-1.5 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>+ Tambah Produk</span>
            </button>
        </div>
    </div>

    <!-- Mobile Select All Bar (Visible only on Mobile) -->
    <div class="flex md:hidden items-center justify-between bg-white p-3 rounded-2xl border border-slate-100 mb-3 shadow-sm">
        <label class="flex items-center space-x-2 text-xs font-bold text-slate-700 cursor-pointer">
            <input type="checkbox" @change="toggleSelectAll({{ json_encode($products->items()) }})" x-model="selectAll" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
            <span>Pilih Semua Barang (Halaman Ini)</span>
        </label>
        <span class="text-[10px] text-indigo-600 font-extrabold" x-text="selectedProductIds.length + ' Terpilih'"></span>
    </div>

    <!-- Mobile Product Cards List (Visible on Mobile Screens) -->
    <div class="grid grid-cols-1 gap-3 md:hidden mb-4">
        @forelse($products as $p)
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3 transition hover:shadow-md">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center space-x-3 min-w-0">
                        <input type="checkbox" value="{{ $p->id }}" x-model="selectedProductIds" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer shrink-0">
                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-100 shrink-0">
                        <div class="min-w-0">
                            <h4 class="font-extrabold text-slate-900 text-xs truncate leading-tight">{{ $p->name }}</h4>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded font-mono font-bold text-[10px] inline-block mt-0.5">{{ $p->barcode ?? 'BRD-AUTO' }}</span>
                        </div>
                    </div>
                    @if($p->stock <= 10)
                        <span class="px-2 py-0.5 bg-rose-100 text-rose-700 font-black rounded-lg text-[10px] shrink-0">
                            Stok: {{ $p->stock }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-black rounded-lg text-[10px] shrink-0">
                            Stok: {{ $p->stock }}
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-2 py-2 px-3 bg-slate-50 rounded-xl text-[10px] font-semibold">
                    <div>
                        <span class="text-slate-400 block">HPP (Modal)</span>
                        <span class="text-slate-700 font-bold">Rp {{ number_format($p->hpp, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Harga Jual</span>
                        <span class="text-indigo-600 font-extrabold">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Margin Profit</span>
                        <span class="text-emerald-600 font-extrabold">+Rp {{ number_format($p->harga_jual - $p->hpp, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1 text-[10px] text-slate-400">
                    <div>
                        <span>Upd: {{ $p->updated_at ? $p->updated_at->format('d/m/Y H:i') : '-' }}</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <button @click="selectedProduct = {{ json_encode($p) }}; customPrintPrice = {{ $p->harga_jual }}; barcodeModal = true" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 font-bold rounded-lg text-[10px] transition">
                            Label
                        </button>
                        <button @click="selectedProduct = {{ json_encode($p) }}; editModal = true" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold rounded-lg text-[10px] transition">
                            Edit
                        </button>
                        <form action="{{ route('desktop.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-lg text-[10px] transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-6 text-center text-slate-400 text-xs">
                Tidak ada produk ditemukan.
            </div>
        @endforelse
    </div>

    <!-- Products Datatable (Visible on Desktop Screens) -->
    <div class="hidden md:block bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-4 w-10 text-center">
                            <input type="checkbox" @change="toggleSelectAll({{ json_encode($products->items()) }})" x-model="selectAll" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="py-4 px-4">Produk</th>
                        <th class="py-4 px-4">Barcode</th>
                        <th class="py-4 px-4">HPP (Modal)</th>
                        <th class="py-4 px-4">Harga Jual</th>
                        <th class="py-4 px-4">Margin Profit</th>
                        <th class="py-4 px-4">Stok & Tanggal Update</th>
                        <th class="py-4 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 text-center">
                                <input type="checkbox" value="{{ $p->id }}" x-model="selectedProductIds" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-100">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $p->name }}</p>
                                        <p class="text-[10px] text-slate-400">ID: #{{ $p->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">
                                <span class="px-2 py-0.5 bg-slate-100 rounded font-bold">{{ $p->barcode ?? 'BRD-AUTO' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">Rp {{ number_format($p->hpp, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 font-bold text-indigo-600">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 font-bold text-emerald-600">
                                + Rp {{ number_format($p->harga_jual - $p->hpp, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($p->stock <= 10)
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-700 font-extrabold rounded-lg inline-block mb-1">
                                        {{ $p->stock }} Unit
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 font-extrabold rounded-lg inline-block mb-1">
                                        {{ $p->stock }} Unit
                                    </span>
                                @endif
                                <div class="text-[10px] text-slate-400 space-y-0.5">
                                    <p title="Tanggal stok terakhir diupdate"><span class="font-bold text-slate-500">Upd:</span> {{ $p->updated_at ? $p->updated_at->format('d/m/Y H:i') : '-' }}</p>
                                    <p title="Tanggal barang pertama kali ditambah"><span class="font-bold text-slate-400">Add:</span> {{ $p->created_at ? $p->created_at->format('d/m/Y') : '-' }}</p>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <!-- Print Barcode Button -->
                                    <button @click="selectedProduct = {{ json_encode($p) }}; customPrintPrice = {{ $p->harga_jual }}; barcodeModal = true" title="Cetak Barcode & Label Harga Etalase" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg flex items-center space-x-1 font-bold text-[11px] transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2a1 1 0 001-1v-5a1 1 0 00-1-1H3a1 1 0 00-1 1v5a1 1 0 001 1h2m2 4h10a1 1 0 001-1v-5a1 1 0 00-1-1H7a1 1 0 00-1 1v5a1 1 0 001 1zm3-7h4"></path>
                                        </svg>
                                        <span class="hidden lg:inline">Cetak Label Barcode</span>
                                    </button>

                                    <button @click="selectedProduct = {{ json_encode($p) }}; editModal = true" title="Edit Produk" class="p-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    <form action="{{ route('desktop.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Produk" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition">
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
                            <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada produk ditemukan.</td>
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
        <div @click.away="showAddModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="font-extrabold text-slate-800 text-base">Tambah Produk Baru</h3>

            <form action="{{ route('desktop.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
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
                    <label class="block font-bold text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" required placeholder="Contoh: Aqua Gelas / Ciki..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Barcode (Opsional)</label>
                    <div class="flex space-x-2">
                        <input type="text" name="barcode" x-model="selectedProduct.barcode" placeholder="Scan atau biarkan kosong (auto-generate)..." class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-mono">
                        <button type="button" @click="selectedProduct.barcode = 'BRD' + (new Date().toISOString().slice(0,10).replace(/-/g,'')) + Math.floor(1000 + Math.random() * 9000)" class="px-3 py-2 bg-slate-800 text-white font-bold rounded-xl text-[10px] shrink-0 hover:bg-slate-700">
                            Auto Code
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">*Jika dikosongkan, sistem akan membuat kode barcode otomatis.</p>
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

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Masuk (WIB)</label>
                        <input type="datetime-local" name="created_at" value="{{ now('Asia/Jakarta')->format('Y-m-d\TH:i') }}" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 text-slate-700 font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Update (WIB)</label>
                        <input type="datetime-local" name="updated_at" value="{{ now('Asia/Jakarta')->format('Y-m-d\TH:i') }}" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 text-slate-700 font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Foto Produk</label>
                    <input type="file" name="image" class="w-full text-slate-500">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Product -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="editModal = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="font-extrabold text-slate-800 text-base">Edit Produk & Atur Tanggal Stok</h3>

            <form :action="'{{ url('desktop/products') }}/' + selectedProduct.id" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" x-model="selectedProduct.name" required class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Barcode</label>
                    <div class="flex space-x-2">
                        <input type="text" name="barcode" x-model="selectedProduct.barcode" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-mono">
                        <button type="button" @click="selectedProduct.barcode = 'BRD' + (new Date().toISOString().slice(0,10).replace(/-/g,'')) + Math.floor(1000 + Math.random() * 9000)" class="px-3 py-2 bg-slate-800 text-white font-bold rounded-xl text-[10px] shrink-0 hover:bg-slate-700">
                            Auto Code
                        </button>
                    </div>
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

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Masuk (WIB)</label>
                        <input type="datetime-local" name="created_at" :value="formatDateTimeLocal(selectedProduct.created_at)" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 text-slate-700 font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Update (WIB)</label>
                        <input type="datetime-local" name="updated_at" :value="formatDateTimeLocal(selectedProduct.updated_at)" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 text-slate-700 font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ubah Foto Produk (Opsional)</label>
                    <input type="file" name="image" class="w-full text-slate-500">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">Update Produk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Single Print Barcode & Price Tag -->
    <div x-show="barcodeModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="barcodeModal = false" class="bg-white rounded-3xl p-6 w-full max-w-lg shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-800 text-base">Cetak Label Barcode & Price Tag</h3>
                <button @click="barcodeModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <!-- Element Checklist Options -->
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                <p class="font-extrabold text-slate-700 text-[11px] uppercase tracking-wider">Tampilkan / Cetak Elemen:</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs font-bold text-slate-700">
                    <label class="flex items-center space-x-2 bg-white px-2.5 py-1.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-indigo-50/50 transition">
                        <input type="checkbox" x-model="showTenant" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                        <span class="text-[11px]">Nama Tenant</span>
                    </label>
                    <label class="flex items-center space-x-2 bg-white px-2.5 py-1.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-indigo-50/50 transition">
                        <input type="checkbox" x-model="showProductName" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                        <span class="text-[11px]">Nama Produk</span>
                    </label>
                    <label class="flex items-center space-x-2 bg-white px-2.5 py-1.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-indigo-50/50 transition">
                        <input type="checkbox" x-model="showBarcodeImg" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                        <span class="text-[11px]">Gbr Barcode</span>
                    </label>
                    <label class="flex items-center space-x-2 bg-white px-2.5 py-1.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-indigo-50/50 transition">
                        <input type="checkbox" x-model="showBarcodeCode" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                        <span class="text-[11px]">Kode Barcode</span>
                    </label>
                    <label class="flex items-center space-x-2 bg-white px-2.5 py-1.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-indigo-50/50 transition">
                        <input type="checkbox" x-model="showPrice" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                        <span class="text-[11px]">Harga Produk</span>
                    </label>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-center space-y-3">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Preview Live Label Stiker Etalase</p>
                
                <div class="inline-block p-4 bg-white rounded-2xl border-2 border-dashed border-indigo-200 shadow-md text-center max-w-[240px] w-full">
                    <p x-show="showTenant" class="font-black text-slate-400 text-[9px] uppercase tracking-wider mb-0.5">
                        {{ auth()->user()->tenant?->name ?? 'Robbani Mart' }}
                    </p>
                    <h4 x-show="showProductName" class="font-extrabold text-slate-900 text-xs leading-tight mb-2 truncate" x-text="selectedProduct.name"></h4>
                    
                    <div x-show="showBarcodeImg" class="flex justify-center my-1.5">
                        <img :src="'https://barcodeapi.org/api/128/' + (selectedProduct.barcode || 'BRD000')" 
                             class="h-10 object-contain max-w-full" 
                             alt="Barcode Preview">
                    </div>

                    <p x-show="showBarcodeCode" class="font-mono text-[10px] font-bold text-slate-600 tracking-wider mb-2" x-text="selectedProduct.barcode"></p>
                    
                    <div x-show="showPrice" class="pt-2 border-t border-slate-100">
                        <p class="text-[9px] text-slate-400 font-bold uppercase">Harga Etalase</p>
                        <p class="font-black text-indigo-600 text-lg">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(customPrintPrice || selectedProduct.harga_jual || 0)"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Print Settings -->
            <div class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Harga yang Dicetak di Label (Rp)</label>
                        <input type="number" x-model="customPrintPrice" min="0" step="500" placeholder="Harga label stiker..." class="w-full px-3 py-2 bg-indigo-50 rounded-xl border border-indigo-200 font-extrabold text-indigo-700 text-sm">
                        <span class="text-[10px] text-slate-400 mt-1 block">*Bisa disesuaikan khusus label (misal harga promo) tanpa mengubah harga master.</span>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jumlah Stiker Dicetak</label>
                        <div class="space-y-1.5">
                            <input type="number" x-model="printQty" min="1" max="500" class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 font-bold">
                            <div class="flex space-x-1">
                                <button type="button" @click="printQty = 1" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 font-bold rounded text-[10px]">1</button>
                                <button type="button" @click="printQty = 6" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 font-bold rounded text-[10px]">6</button>
                                <button type="button" @click="printQty = 12" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 font-bold rounded text-[10px]">12</button>
                                <button type="button" @click="printQty = 24" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 font-bold rounded text-[10px]">24</button>
                                <button type="button" @click="printQty = 50" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 font-bold rounded text-[10px]">50</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="button" @click="barcodeModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-xs">Batal</button>
                <button type="button" @click="window.print()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 shadow-lg shadow-emerald-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Print Label Stiker Sekarang</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal BULK Cetak Barcode Massal -->
    <div x-show="bulkBarcodeModal" x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="bulkBarcodeModal = false" class="bg-white rounded-3xl p-6 w-full max-w-3xl shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base flex items-center space-x-2">
                        <span>Cetak Barcode Massal (Batch Label Printer)</span>
                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold" x-text="bulkProducts.length + ' Produk'"></span>
                    </h3>
                    <p class="text-[11px] text-slate-400 font-semibold">Atur jumlah dan harga stiker untuk setiap produk secara sekaligus.</p>
                </div>
                <button @click="bulkBarcodeModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <!-- Quick Batch Controls & Checklist Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Checklist Elemen -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl space-y-2 text-xs">
                    <p class="font-extrabold text-slate-700 text-[10px] uppercase tracking-wider">Tampilkan pada Setiap Stiker:</p>
                    <div class="grid grid-cols-2 gap-1.5 font-bold text-slate-700">
                        <label class="flex items-center space-x-1.5 cursor-pointer">
                            <input type="checkbox" x-model="showTenant" class="rounded text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5">
                            <span class="text-[10px]">Nama Tenant</span>
                        </label>
                        <label class="flex items-center space-x-1.5 cursor-pointer">
                            <input type="checkbox" x-model="showProductName" class="rounded text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5">
                            <span class="text-[10px]">Nama Produk</span>
                        </label>
                        <label class="flex items-center space-x-1.5 cursor-pointer">
                            <input type="checkbox" x-model="showBarcodeImg" class="rounded text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5">
                            <span class="text-[10px]">Gbr Barcode</span>
                        </label>
                        <label class="flex items-center space-x-1.5 cursor-pointer">
                            <input type="checkbox" x-model="showBarcodeCode" class="rounded text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5">
                            <span class="text-[10px]">Kode Barcode</span>
                        </label>
                        <label class="flex items-center space-x-1.5 cursor-pointer col-span-2">
                            <input type="checkbox" x-model="showPrice" class="rounded text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5">
                            <span class="text-[10px]">Harga Produk</span>
                        </label>
                    </div>
                </div>

                <!-- Set Qty Massal -->
                <div class="p-3 bg-indigo-50/60 border border-indigo-100 rounded-2xl space-y-2 text-xs flex flex-col justify-center">
                    <p class="font-extrabold text-indigo-900 text-[10px] uppercase tracking-wider">Set Qty Stiker Serentak Semua Produk:</p>
                    <div class="flex items-center space-x-1.5">
                        <button type="button" @click="setAllBulkQty(1)" class="flex-1 py-1.5 bg-white hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl border border-indigo-200 text-[11px]">1 Pcs</button>
                        <button type="button" @click="setAllBulkQty(6)" class="flex-1 py-1.5 bg-white hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl border border-indigo-200 text-[11px]">6 Pcs</button>
                        <button type="button" @click="setAllBulkQty(12)" class="flex-1 py-1.5 bg-white hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl border border-indigo-200 text-[11px]">12 Pcs</button>
                        <button type="button" @click="setAllBulkQty(24)" class="flex-1 py-1.5 bg-white hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl border border-indigo-200 text-[11px]">24 Pcs</button>
                    </div>
                    <p class="text-[10px] text-slate-500 font-semibold text-right">
                        Total Stiker: <span class="font-extrabold text-emerald-600 text-xs" x-text="totalBulkStickers + ' Lembar'"></span>
                    </p>
                </div>
            </div>

            <!-- Table Batch List -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden max-h-64 overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-600 font-bold uppercase sticky top-0 text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Produk</th>
                            <th class="py-2.5 px-3">Barcode</th>
                            <th class="py-2.5 px-3">Harga Label (Rp)</th>
                            <th class="py-2.5 px-3 w-28 text-center">Jumlah Cetak</th>
                            <th class="py-2.5 px-3 w-10 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        <template x-for="(item, index) in bulkProducts" :key="item.id">
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 px-3 font-bold text-slate-900 truncate max-w-[150px]" x-text="item.name"></td>
                                <td class="py-2 px-3 font-mono text-[10px] text-slate-500" x-text="item.barcode || 'BRD-AUTO'"></td>
                                <td class="py-2 px-3">
                                    <input type="number" x-model="item.print_price" min="0" step="500" class="w-28 px-2 py-1 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-indigo-600">
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <input type="number" x-model="item.print_qty" min="1" max="500" class="w-16 px-2 py-1 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-center">
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <button type="button" @click="removeBulkItem(index)" title="Keluarkan dari cetakan massal" class="text-rose-500 hover:text-rose-700 font-extrabold text-sm">&times;</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="button" @click="bulkBarcodeModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-xs">Batal</button>
                <button type="button" @click="window.print()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center space-x-2 shadow-lg shadow-emerald-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak <span x-text="totalBulkStickers"></span> Label Stiker Sekarang</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Print Area for Barcode Labels (Supports both Single & Bulk Batch) -->
    <div id="print-barcode-wrapper" class="hidden print:block p-2">
        <div class="flex flex-wrap items-start gap-2">
            <!-- Single Print Loop -->
            <template x-if="barcodeModal">
                <template x-for="i in Array.from({length: parseInt(printQty || 1)})" :key="'single-' + i">
                    <div class="barcode-card-item border border-black p-2 rounded text-center w-[50mm] h-[34mm] box-border overflow-hidden bg-white">
                        <p x-show="showTenant" class="font-bold text-[8px] uppercase tracking-wider text-black truncate leading-none mb-0.5">
                            {{ auth()->user()->tenant?->name ?? 'E-Kasir' }}
                        </p>
                        <p x-show="showProductName" class="font-extrabold text-[10px] text-black truncate leading-tight mb-1" x-text="selectedProduct.name"></p>
                        
                        <div x-show="showBarcodeImg" class="flex justify-center my-0.5">
                            <img :src="'https://barcodeapi.org/api/128/' + (selectedProduct.barcode || 'BRD000')" 
                                 class="h-7 object-contain max-w-full" 
                                 alt="Barcode">
                        </div>

                        <p x-show="showBarcodeCode" class="font-mono text-[8px] font-bold text-black leading-none mb-1" x-text="selectedProduct.barcode"></p>
                        <p x-show="showPrice" class="font-black text-[12px] text-black leading-none">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(customPrintPrice || selectedProduct.harga_jual || 0)"></span>
                        </p>
                    </div>
                </template>
            </template>

            <!-- Bulk Print Loop (Flattened Array to prevent nested template truncation) -->
            <template x-if="bulkBarcodeModal">
                <template x-for="(card, index) in flattenedBulkItems" :key="card.unique_key">
                    <div class="barcode-card-item border border-black p-2 rounded text-center w-[50mm] h-[34mm] box-border overflow-hidden bg-white">
                        <p x-show="showTenant" class="font-bold text-[8px] uppercase tracking-wider text-black truncate leading-none mb-0.5">
                            {{ auth()->user()->tenant?->name ?? 'E-Kasir' }}
                        </p>
                        <p x-show="showProductName" class="font-extrabold text-[10px] text-black truncate leading-tight mb-1" x-text="card.name"></p>
                        
                        <div x-show="showBarcodeImg" class="flex justify-center my-0.5">
                            <img :src="'https://barcodeapi.org/api/128/' + (card.barcode || 'BRD000')" 
                                 class="h-7 object-contain max-w-full" 
                                 alt="Barcode">
                        </div>

                        <p x-show="showBarcodeCode" class="font-mono text-[8px] font-bold text-black leading-none mb-1" x-text="card.barcode"></p>
                        <p x-show="showPrice" class="font-black text-[12px] text-black leading-none">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(card.print_price || card.harga_jual || 0)"></span>
                        </p>
                    </div>
                </template>
            </template>
        </div>
    </div>
</div>
@endsection
