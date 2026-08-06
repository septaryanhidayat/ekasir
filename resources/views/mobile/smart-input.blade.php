@extends('layouts.mobile')

@section('title', 'Kelola Produk & Stok Mobile - E-Kasir')

@section('content')
<div x-data="smartInputApp({{ json_encode($products) }})" class="glass-card rounded-3xl p-5 shadow-2xl mb-6 space-y-4">
    <!-- Header Title -->
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div>
            <h3 class="font-extrabold text-slate-800 text-sm">Kelola Produk & Stok Mobile</h3>
            <p class="text-[11px] text-slate-500">Tambah barang baru atau update stok toko</p>
        </div>
        <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full font-extrabold">WIB (GMT+7)</span>
    </div>

    <!-- Mode Switcher Tabs (Tambah Baru VS Update Stok) -->
    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-2xl text-xs font-bold">
        <button type="button" 
                @click="inputMode = 'new'" 
                :class="inputMode === 'new' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900'"
                class="py-2.5 rounded-xl transition flex items-center justify-center space-x-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>+ Tambah Baru</span>
        </button>

        <button type="button" 
                @click="inputMode = 'update'" 
                :class="inputMode === 'update' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900'"
                class="py-2.5 rounded-xl transition flex items-center justify-center space-x-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>📦 Restok / Update</span>
        </button>
    </div>

    <!-- Scanner Camera Preview Section -->
    <div class="space-y-2">
        <div x-show="scanning" class="relative rounded-2xl overflow-hidden bg-slate-900 border-2 border-indigo-500">
            <div id="interactive-smart-reader" class="w-full min-h-[220px]"></div>
            <button @click="stopScanner()" class="absolute top-2 right-2 px-3 py-1.5 bg-rose-600 text-white rounded-xl text-xs font-bold shadow-md">
                Tutup Kamera
            </button>
        </div>

        <button type="button" 
                x-show="!scanning" 
                @click="startScanner()" 
                class="w-full py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-2xl border border-indigo-200 text-xs flex items-center justify-center space-x-2 transition">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
            </svg>
            <span>Scan Barcode Produk Lewat Kamera</span>
        </button>
    </div>

    <!-- Matched Product Alert Banner (If barcode matches an existing product) -->
    <div x-show="matchedProduct" x-transition class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-xs space-y-1">
        <div class="flex items-center justify-between">
            <span class="font-extrabold text-amber-900 text-[11px] uppercase tracking-wider">⚡ Produk Ditemukan di Sistem!</span>
            <span class="px-2 py-0.5 bg-amber-200 text-amber-900 rounded font-bold text-[10px]" x-text="'Stok: ' + matchedProduct?.stock + ' Unit'"></span>
        </div>
        <p class="font-bold text-slate-800 text-xs" x-text="matchedProduct?.name"></p>
        <p class="text-[10px] text-slate-500" x-text="'HPP: Rp ' + new Intl.NumberFormat('id-ID').format(matchedProduct?.hpp || 0) + ' | Jual: Rp ' + new Intl.NumberFormat('id-ID').format(matchedProduct?.harga_jual || 0)"></p>
        <button type="button" @click="switchToUpdateMode(matchedProduct)" class="mt-1 w-full py-1.5 bg-amber-600 text-white rounded-xl font-bold text-[11px] hover:bg-amber-700">
            Gunakan Produk Ini Untuk Restok / Edit
        </button>
    </div>

    <!-- MAIN FORM -->
    <form action="{{ route('mobile.smart-input.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
        @csrf
        <input type="hidden" name="mode" :value="inputMode">
        <input type="hidden" name="product_id" :value="selectedProductId">

        @if(auth()->user()->role === 'superadmin')
            <div>
                <label class="block font-bold text-slate-700 mb-1">Outlet / Tenant</label>
                <select name="tenant_id" class="w-full px-3 py-2.5 bg-slate-100 rounded-2xl border border-slate-200 font-semibold text-xs">
                    @foreach(\App\Models\Tenant::where('is_active', true)->get() as $t)
                        <option value="{{ $t->id }}" {{ (session('active_tenant_id') == $t->id) ? 'selected' : '' }}>{{ $t->name }} ({{ $t->code }})</option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- MODE 1: TAMBAH PRODUK BARU -->
        <template x-if="inputMode === 'new'">
            <div class="space-y-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Barcode</label>
                    <div class="flex space-x-2">
                        <input type="text" name="barcode" x-model="barcode" @input="checkBarcodeMatch()" placeholder="Scan barcode atau biarkan kosong..."
                               class="w-full px-3 py-2.5 rounded-2xl bg-slate-100 font-mono font-bold text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button" @click="generateAutoBarcode()" class="px-3 py-2.5 bg-slate-800 text-white font-bold rounded-2xl text-[10px] shrink-0 hover:bg-slate-700">
                            Auto Code
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Barang / Produk</label>
                    <input type="text" name="name" x-model="productName" required placeholder="Contoh: Aqua Gelas 220ml..."
                           class="w-full px-3 py-2.5 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">HPP (Modal)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                            <input type="number" name="hpp" x-model="hpp" required placeholder="0"
                                   class="w-full pl-9 pr-3 py-2.5 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Harga Jual</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                            <input type="number" name="harga_jual" x-model="hargaJual" required placeholder="0"
                                   class="w-full pl-9 pr-3 py-2.5 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold text-indigo-600">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Stok Awal Produk</label>
                    <input type="number" name="stock" x-model="stock" required placeholder="50"
                           class="w-full px-3 py-2.5 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold">
                </div>

                <!-- Custom Tanggal Input for Mobile -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Masuk (WIB)</label>
                        <input type="datetime-local" name="created_at" value="{{ now('Asia/Jakarta')->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 text-slate-700 font-semibold text-[11px]">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Update (WIB)</label>
                        <input type="datetime-local" name="updated_at" value="{{ now('Asia/Jakarta')->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 text-slate-700 font-semibold text-[11px]">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Foto Fisik Produk (Opsional)</label>
                    <input type="file" name="image" accept="image/*" capture="environment" 
                           class="w-full text-[11px] text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-indigo-500/30 transform active:scale-95 transition">
                    + Simpan Produk Baru
                </button>
            </div>
        </template>

        <!-- MODE 2: RESTOK / UPDATE STOK BARANG LAMA -->
        <template x-if="inputMode === 'update'">
            <div class="space-y-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Produk Untuk Di-Restok</label>
                    <select name="product_id" x-model="selectedProductId" @change="onSelectProductChange()" required class="w-full px-3 py-2.5 bg-slate-100 rounded-2xl border border-slate-200 font-bold text-slate-800 text-xs">
                        <option value="">-- Pilih Produk Dari List --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-barcode="{{ $p->barcode }}" data-stock="{{ $p->stock }}" data-hpp="{{ $p->hpp }}" data-harga="{{ $p->harga_jual }}">
                                {{ $p->name }} (Stok: {{ $p->stock }} | {{ $p->barcode ?? 'No Barcode' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Mode (Tambah ke stok lama vs Overwrite Total Stok) -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                    <label class="block font-extrabold text-slate-700 text-[10px] uppercase tracking-wider">Aksi Pembaruan Stok:</label>
                    <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                        <label class="flex items-center space-x-2 p-2 bg-white rounded-xl border border-slate-200 cursor-pointer">
                            <input type="radio" name="stock_action" value="add" x-model="stockAction" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-[11px] text-emerald-700">+ Tambah Ke Stok Lama</span>
                        </label>
                        <label class="flex items-center space-x-2 p-2 bg-white rounded-xl border border-slate-200 cursor-pointer">
                            <input type="radio" name="stock_action" value="set" x-model="stockAction" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-[11px] text-indigo-700">Set Total Stok Baru</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="stockAction === 'add' ? 'Jumlah Stok Yang Ditambahkan (Pcs/Unit)' : 'Total Stok Baru Produk'"></label>
                    <input type="number" name="stock" x-model="stock" required placeholder="0"
                           class="w-full px-3 py-2.5 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Update HPP (Opsional)</label>
                        <input type="number" name="hpp" x-model="hpp" placeholder="Tetap"
                               class="w-full px-3 py-2.5 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Update Harga Jual</label>
                        <input type="number" name="harga_jual" x-model="hargaJual" placeholder="Tetap"
                               class="w-full px-3 py-2.5 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200 font-bold text-indigo-600">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Update Stok (WIB)</label>
                    <input type="datetime-local" name="updated_at" value="{{ now('Asia/Jakarta')->format('Y-m-d\TH:i') }}"
                           class="w-full px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 text-slate-700 font-semibold text-[11px]">
                </div>

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-emerald-500/30 transform active:scale-95 transition">
                    📦 Simpan Pembaruan Stok
                </button>
            </div>
        </template>
    </form>
</div>
@endsection

@push('scripts')
<script>
function smartInputApp(allProducts) {
    return {
        allProducts: allProducts || [],
        inputMode: 'new',
        selectedProductId: '',
        stockAction: 'add',
        barcode: '',
        productName: '',
        hpp: '',
        hargaJual: '',
        stock: 50,
        scanning: false,
        scannerObj: null,
        matchedProduct: null,

        generateAutoBarcode() {
            const todayStr = new Date().toISOString().slice(0,10).replace(/-/g,'');
            const randNum = Math.floor(1000 + Math.random() * 9000);
            this.barcode = 'BRD' + todayStr + randNum;
        },

        checkBarcodeMatch() {
            if (!this.barcode) {
                this.matchedProduct = null;
                return;
            }
            const found = this.allProducts.find(p => p.barcode && p.barcode.trim().toLowerCase() === this.barcode.trim().toLowerCase());
            if (found) {
                this.matchedProduct = found;
            } else {
                this.matchedProduct = null;
            }
        },

        switchToUpdateMode(prod) {
            this.inputMode = 'update';
            this.selectedProductId = prod.id;
            this.productName = prod.name;
            this.hpp = prod.hpp;
            this.hargaJual = prod.harga_jual;
            this.stockAction = 'add';
            this.stock = 10;
        },

        onSelectProductChange() {
            const prod = this.allProducts.find(p => p.id == this.selectedProductId);
            if (prod) {
                this.hpp = prod.hpp;
                this.hargaJual = prod.harga_jual;
                this.barcode = prod.barcode || '';
            }
        },

        startScanner() {
            this.scanning = true;
            this.$nextTick(() => {
                this.scannerObj = new Html5Qrcode("interactive-smart-reader");
                this.scannerObj.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 220, height: 220 } },
                    (decodedText) => {
                        this.barcode = decodedText;
                        this.checkBarcodeMatch();
                        if (this.matchedProduct) {
                            showToast('Produk Ditemukan: ' + this.matchedProduct.name);
                        } else {
                            showToast('Barcode Terpindai: ' + decodedText);
                        }
                        this.stopScanner();
                    },
                    (error) => {}
                ).catch(err => {
                    alert("Kamera gagal diakses: " + err);
                    this.scanning = false;
                });
            });
        },

        stopScanner() {
            if (this.scannerObj) {
                this.scannerObj.stop().then(() => {
                    this.scanning = false;
                }).catch(() => {
                    this.scanning = false;
                });
            } else {
                this.scanning = false;
            }
        }
    }
}
</script>
@endpush
