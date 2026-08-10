@extends('layouts.mobile')

@section('title', 'POS Kasir - E-Kasir Mobile')

@section('content')
<div x-data="posApp()" x-init="init()" class="relative">

    <!-- Top Bar & Search -->
    <div class="glass-card p-4 rounded-3xl shadow-xl mb-4">
        <div class="flex items-center space-x-2">
            <div class="relative flex-1">
                <input type="text" 
                       x-model="searchQuery" 
                       @input.debounce.300ms="filterProducts()" 
                       placeholder="Cari nama barang / ketik barcode..." 
                       class="w-full pl-10 pr-4 py-3 bg-slate-100/80 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 border border-slate-200">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Scan Camera Trigger Button -->
            <button @click="openScanner()" class="p-3 bg-gradient-to-tr from-indigo-600 to-purple-600 text-white rounded-2xl shadow-md shadow-indigo-500/30 flex items-center justify-center transform active:scale-95 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Scanner Modal Overlay -->
    <div x-show="showScanner" 
         x-transition
         class="fixed inset-0 z-[100] bg-black/90 flex flex-col justify-between p-4" 
         style="display: none;">
        <div class="flex items-center justify-between text-white mb-2">
            <h3 class="font-bold text-sm">Arahkan Kamera ke Barcode</h3>
            <button @click="closeScanner()" class="p-2 bg-white/20 rounded-full text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div id="interactive-reader" class="w-full bg-slate-900 rounded-3xl overflow-hidden border-2 border-indigo-500 min-h-[300px]"></div>

        <div class="p-4 text-center">
            <p class="text-xs text-slate-300">Sistem otomatis mendeteksi barcode dan memasukkan ke keranjang.</p>
        </div>
    </div>

    <!-- Product Grid List -->
    <div class="grid grid-cols-2 gap-3 mb-24">
        <template x-for="product in filteredProducts" :key="product.id">
            <div @click="addToCart(product)" 
                 class="glass-card p-3 rounded-2xl shadow-sm hover:shadow-md transition cursor-pointer flex flex-col justify-between active:scale-95 transform border border-slate-200/60">
                <div>
                    <div class="w-full h-24 bg-slate-100 rounded-xl mb-2 overflow-hidden relative">
                        <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover">
                        <span class="absolute top-1 right-1 bg-slate-900/80 backdrop-blur-md text-white text-[9px] px-2 py-0.5 rounded-full font-bold" x-text="'Stok: ' + product.stock"></span>
                    </div>
                    <h4 class="font-bold text-slate-800 text-xs line-clamp-2 leading-tight" x-text="product.name"></h4>
                    <p class="text-[10px] text-slate-400 mt-0.5" x-text="product.barcode || '-'"></p>
                </div>

                <div class="mt-2 flex items-center justify-between pt-2 border-t border-slate-100">
                    <span class="font-extrabold text-indigo-600 text-xs" x-text="formatRupiah(product.harga_jual)"></span>
                    <button class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        +
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Floating Cart Footer -->
    <div x-show="cart.length > 0" 
         x-transition
         class="fixed bottom-20 left-1/2 -translate-x-1/2 w-full max-w-[440px] px-4 z-40">
        <div class="bg-slate-900 text-white rounded-3xl p-4 shadow-2xl flex items-center justify-between border border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-600 flex items-center justify-center font-bold text-sm shadow-inner" x-text="totalQty">
                </div>
                <div>
                    <p class="text-[10px] text-slate-400">Total Belanja</p>
                    <p class="font-extrabold text-base text-emerald-400" x-text="formatRupiah(totalAmount)"></p>
                </div>
            </div>

            <button @click="showCheckoutModal = true" class="px-5 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-indigo-500/30 transform active:scale-95 transition">
                Bayar Sekarang &rarr;
            </button>
        </div>
    </div>

    <!-- Checkout Modal / Drawer -->
    <div x-show="showCheckoutModal" 
         x-transition
         class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-end justify-center pb-4 sm:pb-0" 
         style="display: none;">
        <div @click.away="showCheckoutModal = false" class="w-full max-w-[480px] bg-white rounded-t-[2.5rem] p-6 pb-12 max-h-[82vh] overflow-y-auto space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-800 text-base">Rincian Pembayaran</h3>
                <button @click="showCheckoutModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Cart Items Summary List -->
            <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-2xl text-xs">
                        <div class="flex-1 mr-2">
                            <p class="font-bold text-slate-800" x-text="item.name"></p>
                            <p class="text-[10px] text-slate-400" x-text="formatRupiah(item.harga_jual) + ' x ' + item.qty"></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="updateQty(index, -1)" class="w-6 h-6 rounded-lg bg-slate-200 font-bold text-slate-700">-</button>
                            <span class="font-bold text-slate-800" x-text="item.qty"></span>
                            <button @click="updateQty(index, 1)" class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-bold">+</button>
                            <span class="font-extrabold text-slate-800 ml-2" x-text="formatRupiah(item.harga_jual * item.qty)"></span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Total Amount Card -->
            <div class="p-4 bg-indigo-50 rounded-2xl flex items-center justify-between text-indigo-950">
                <span class="font-bold text-xs uppercase tracking-wider">Total Tagihan</span>
                <span class="font-black text-xl text-indigo-600" x-text="formatRupiah(totalAmount)"></span>
            </div>

            <!-- Fast Cash Suggestion Chips -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2">Pilih Uang Tunai Cepat</label>
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <button @click="cashPaid = totalAmount" class="py-2 bg-slate-100 hover:bg-indigo-600 hover:text-white rounded-xl text-xs font-bold transition">Uang Pas</button>
                    <button @click="cashPaid = 10000" class="py-2 bg-slate-100 hover:bg-indigo-600 hover:text-white rounded-xl text-xs font-bold transition">Rp 10.000</button>
                    <button @click="cashPaid = 20000" class="py-2 bg-slate-100 hover:bg-indigo-600 hover:text-white rounded-xl text-xs font-bold transition">Rp 20.000</button>
                    <button @click="cashPaid = 50000" class="py-2 bg-slate-100 hover:bg-indigo-600 hover:text-white rounded-xl text-xs font-bold transition">Rp 50.000</button>
                    <button @click="cashPaid = 100000" class="py-2 bg-slate-100 hover:bg-indigo-600 hover:text-white rounded-xl text-xs font-bold transition">Rp 100.000</button>
                    <button @click="cashPaid = 200000" class="py-2 bg-slate-100 hover:bg-indigo-600 hover:text-white rounded-xl text-xs font-bold transition">Rp 200.000</button>
                </div>

                <div class="relative">
                    <span class="absolute left-3.5 top-3.5 text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" 
                           x-model.number="cashPaid" 
                           placeholder="Input Nominal Tunai..." 
                           class="w-full pl-10 pr-4 py-3 bg-slate-100 rounded-2xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Calculated Change -->
            <div class="p-3 bg-emerald-50 rounded-2xl flex items-center justify-between text-xs">
                <span class="font-bold text-emerald-800">Kembalian:</span>
                <span class="font-black text-emerald-600 text-sm" x-text="formatRupiah(Math.max(0, cashPaid - totalAmount))"></span>
            </div>

            <!-- Submit Button -->
            <div class="pb-6">
                <button @click="submitCheckout()" 
                        :disabled="cashPaid < totalAmount || loading" 
                        class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-600 disabled:opacity-50 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-emerald-500/30 transition transform active:scale-95">
                    <span x-show="!loading">Selesaikan Transaksi & Cetak Struk</span>
                    <span x-show="loading" style="display:none;">Memproses Transaksi...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function posApp() {
    return {
        products: @json($products),
        filteredProducts: [],
        searchQuery: '',
        cart: [],
        showScanner: false,
        showCheckoutModal: false,
        html5QrcodeScanner: null,
        cashPaid: 0,
        loading: false,

        init() {
            this.filteredProducts = this.products;
            
            // Auto open scanner if query param scan=1 is set
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('scan') === '1') {
                this.openScanner();
            }
        },

        filterProducts() {
            if (!this.searchQuery) {
                this.filteredProducts = this.products;
                return;
            }
            const q = this.searchQuery.toLowerCase();
            this.filteredProducts = this.products.filter(p => 
                p.name.toLowerCase().includes(q) || (p.barcode && p.barcode.toLowerCase().includes(q))
            );
        },

        addToCart(product) {
            const existing = this.cart.find(item => item.id === product.id);
            if (existing) {
                if (existing.qty < product.stock) {
                    existing.qty++;
                } else {
                    alert('Stok produk terbatas!');
                }
            } else {
                if (product.stock > 0) {
                    this.cart.push({ ...product, qty: 1 });
                } else {
                    alert('Stok produk habis!');
                }
            }
        },

        updateQty(index, change) {
            const item = this.cart[index];
            if (item) {
                item.qty += change;
                if (item.qty <= 0) {
                    this.cart.splice(index, 1);
                }
            }
        },

        get totalQty() {
            return this.cart.reduce((sum, item) => sum + item.qty, 0);
        },

        get totalAmount() {
            return this.cart.reduce((sum, item) => sum + (item.harga_jual * item.qty), 0);
        },

        openScanner() {
            this.showScanner = true;
            this.$nextTick(() => {
                this.html5QrcodeScanner = new Html5Qrcode("interactive-reader");
                this.html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        this.handleBarcodeScanned(decodedText);
                    },
                    (errorMessage) => {}
                ).catch(err => {
                    console.error("Camera access error", err);
                    alert("Gagal mengakses kamera smartphone: " + err);
                });
            });
        },

        closeScanner() {
            if (this.html5QrcodeScanner) {
                this.html5QrcodeScanner.stop().then(() => {
                    this.showScanner = false;
                }).catch(() => {
                    this.showScanner = false;
                });
            } else {
                this.showScanner = false;
            }
        },

        handleBarcodeScanned(barcode) {
            const product = this.products.find(p => p.barcode === barcode);
            if (product) {
                this.addToCart(product);
                this.closeScanner();
            } else {
                fetch(`{{ route('mobile.api.products.search') }}?barcode=${barcode}`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.data && res.data.length > 0) {
                            this.addToCart(res.data[0]);
                            this.closeScanner();
                        } else {
                            alert(`Produk dengan barcode '${barcode}' tidak ditemukan.`);
                        }
                    });
            }
        },

        submitCheckout() {
            if (this.cashPaid < this.totalAmount) {
                alert('Jumlah uang bayar kurang!');
                return;
            }

            this.loading = true;

            const payload = {
                items: this.cart.map(item => ({ id: item.id, qty: item.qty })),
                cash_paid: this.cashPaid,
                payment_method: 'cash',
            };

            fetch(`{{ route('mobile.checkout') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(res => {
                this.loading = false;
                if (res.status === 'success') {
                    window.location.href = res.redirect_url;
                } else {
                    alert(res.message || 'Terjadi kesalahan transaksi.');
                }
            })
            .catch(err => {
                this.loading = false;
                alert('Gagal memproses transaksi.');
            });
        },

        formatRupiah(val) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
        }
    }
}
</script>
@endpush
