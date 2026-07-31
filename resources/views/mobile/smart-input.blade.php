@extends('layouts.mobile')

@section('title', 'Smart Input Produk - E-Kasir')

@section('content')
<div x-data="smartInputApp()" class="glass-card rounded-3xl p-6 shadow-2xl mb-6">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
        <h3 class="font-extrabold text-slate-800 text-sm">Tambah Produk Cepat (Smart Input)</h3>
        <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full font-bold">Kamera & Barcode</span>
    </div>

    <!-- Scanner Preview Section -->
    <div class="mb-4">
        <label class="block text-xs font-bold text-slate-600 mb-1.5">1. Pindai Barcode Produk (Kamera)</label>
        
        <div x-show="scanning" class="relative rounded-2xl overflow-hidden bg-slate-900 border-2 border-indigo-500 mb-2">
            <div id="interactive-smart-reader" class="w-full min-h-[220px]"></div>
            <button @click="stopScanner()" class="absolute top-2 right-2 px-3 py-1 bg-rose-600 text-white rounded-xl text-xs font-bold shadow-md">
                Tutup Kamera
            </button>
        </div>

        <button type="button" 
                x-show="!scanning" 
                @click="startScanner()" 
                class="w-full py-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-2xl border border-indigo-200 text-xs flex items-center justify-center space-x-2 transition">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
            </svg>
            <span>Buka Kamera Barcode Scanner</span>
        </button>
    </div>

    <!-- Product Form -->
    <form action="{{ route('mobile.smart-input.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Kode Barcode</label>
            <input type="text" name="barcode" x-model="barcode" placeholder="Terisi otomatis setelah scan..."
                   class="w-full px-4 py-3 rounded-2xl bg-slate-100 font-bold text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Foto Fisik Produk</label>
            <input type="file" name="image" accept="image/*" capture="environment" 
                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Barang</label>
            <input type="text" name="name" required placeholder="Contoh: Indomie Goreng Spesial"
                   class="w-full px-4 py-3 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Harga HPP (Modal)</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-xs text-slate-400 font-bold">Rp</span>
                    <input type="number" name="hpp" required placeholder="0"
                           class="w-full pl-9 pr-3 py-3 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Harga Jual</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-xs text-slate-400 font-bold">Rp</span>
                    <input type="number" name="harga_jual" required placeholder="0"
                           class="w-full pl-9 pr-3 py-3 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold text-indigo-600">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Stok Awal Produk</label>
            <input type="number" name="stock" value="50" required
                   class="w-full px-4 py-3 rounded-2xl bg-slate-100 text-slate-800 text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold">
        </div>

        <button type="submit" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-emerald-500/30 transform active:scale-95 transition">
            Simpan Produk & Mulai Transaksi
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
function smartInputApp() {
    return {
        barcode: '',
        scanning: false,
        scannerObj: null,

        startScanner() {
            this.scanning = true;
            this.$nextTick(() => {
                this.scannerObj = new Html5Qrcode("interactive-smart-reader");
                this.scannerObj.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 220, height: 220 } },
                    (decodedText) => {
                        this.barcode = decodedText;
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
