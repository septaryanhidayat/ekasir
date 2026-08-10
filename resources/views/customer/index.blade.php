<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>E-Kantin & Koperasi - Pemesanan Online</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .customer-container {
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            background-color: #ffffff;
            position: relative;
            padding-bottom: 100px;
            box-shadow: 0 0 40px rgba(0,0,0,0.08);
        }
        .header-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 50%, #3730a3 100%);
        }
    </style>
</head>
<body x-data="customerShopApp()" x-init="init()">

<div class="customer-container overflow-x-hidden">
    <!-- Header E-Commerce Kantin -->
    <header class="header-gradient text-white p-5 rounded-b-[2rem] shadow-lg relative">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center font-bold text-white shadow-inner border border-white/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="font-extrabold text-base leading-tight">E-Kantin & Koperasi</h1>
                    <p class="text-[11px] text-indigo-200">Pesan makanan & minuman langsung</p>
                </div>
            </div>

            <!-- Link History -->
            <a href="{{ route('shop.history') }}" class="p-2.5 bg-white/10 hover:bg-white/20 rounded-2xl transition backdrop-blur-md flex items-center text-xs font-bold">
                <svg class="w-4 h-4 mr-1 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pesanan Saya
            </a>
        </div>

        <!-- Canteen / Tenant Selector Dropdown -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/20">
            <label class="block text-[10px] uppercase tracking-wider font-extrabold text-indigo-200 mb-1">Pilih Kantin / Cabang:</label>
            <form method="GET" action="{{ route('shop.index') }}">
                <select name="tenant_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-white text-slate-800 rounded-xl font-bold text-xs focus:outline-none shadow-sm">
                    @foreach($tenants as $t)
                        <option value="{{ $t->id }}" {{ $selectedTenant->id == $t->id ? 'selected' : '' }}>
                            {{ $t->name }} ({{ $t->address ?? 'Kantin' }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </header>

    <main class="p-4 space-y-4">
        <!-- Promo Banner Carousel Card -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl p-5 text-white shadow-lg relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <span class="px-2.5 py-0.5 bg-white/20 backdrop-blur-md text-white font-extrabold text-[10px] rounded-full uppercase tracking-wider">Diskon Hemat Koperasi</span>
            <h3 class="text-lg font-black mt-2 leading-tight">Pesan Makan Siang Tanpa Antre!</h3>
            <p class="text-xs text-amber-100 mt-1">Pesan sekarang, ambil langsung di meja/kasir kantin.</p>
        </div>

        <!-- Search Bar -->
        <div class="relative">
            <input type="text" 
                   x-model="searchQuery" 
                   @input="filterProducts()" 
                   placeholder="Cari makanan, minuman, snack..." 
                   class="w-full pl-10 pr-4 py-3 bg-slate-100 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 border border-slate-200">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <!-- Product Grid (E-Commerce Style with Crisp Images) -->
        <div class="grid grid-cols-2 gap-3">
            <template x-for="product in filteredProducts" :key="product.id">
                <div class="bg-white rounded-3xl p-3 border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="w-full h-32 bg-slate-100 rounded-2xl overflow-hidden mb-2 relative">
                            <img :src="product.image_url" :alt="product.name" :onerror="'this.onerror=null; this.src=\'' + {{ json_encode(\App\Models\Product::getPlaceholderUrl()) }} + '\';'" class="w-full h-full object-cover">
                            <span class="absolute top-2 right-2 bg-slate-900/80 backdrop-blur-md text-white text-[9px] px-2 py-0.5 rounded-full font-bold" 
                                  x-text="'Stok: ' + product.stock"></span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-xs line-clamp-2 leading-snug" x-text="product.name"></h4>
                        <p class="font-black text-indigo-600 text-sm mt-1.5" x-text="formatRupiah(product.harga_jual)"></p>
                    </div>

                    <div class="mt-3 pt-2 border-t border-slate-100">
                        <template x-if="getCartQty(product.id) === 0">
                            <button @click="addToCart(product)" 
                                    :disabled="product.stock <= 0"
                                    class="w-full py-2 bg-indigo-600 disabled:bg-slate-300 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition transform active:scale-95">
                                + Tambah
                            </button>
                        </template>

                        <template x-if="getCartQty(product.id) > 0">
                            <div class="flex items-center justify-between bg-indigo-50 p-1 rounded-xl">
                                <button @click="updateCartQty(product.id, -1)" class="w-7 h-7 rounded-lg bg-white text-indigo-600 font-bold shadow-sm">-</button>
                                <span class="font-extrabold text-indigo-700 text-xs" x-text="getCartQty(product.id)"></span>
                                <button @click="updateCartQty(product.id, 1)" class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-bold shadow-sm">+</button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </main>

    <!-- Bottom Floating Cart Footer Drawer Button -->
    <div x-show="cart.length > 0" 
         x-transition
         class="fixed bottom-4 left-1/2 -translate-x-1/2 w-full max-w-[440px] px-4 z-40">
        <div class="bg-slate-900 text-white rounded-3xl p-4 shadow-2xl flex items-center justify-between border border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-600 flex items-center justify-center font-bold text-sm shadow-inner" x-text="totalQty">
                </div>
                <div>
                    <p class="text-[10px] text-slate-400">Total Keranjang</p>
                    <p class="font-extrabold text-base text-emerald-400" x-text="formatRupiah(totalAmount)"></p>
                </div>
            </div>

            <button @click="showCheckoutModal = true" class="px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-emerald-500/30 transform active:scale-95 transition">
                Lanjut Order &rarr;
            </button>
        </div>
    </div>

    <!-- Customer Checkout Modal -->
    <div x-show="showCheckoutModal" 
         x-transition
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end justify-center" 
         style="display: none;">
        <div @click.away="showCheckoutModal = false" class="w-full max-w-[480px] bg-white rounded-t-[2.5rem] p-6 max-h-[90vh] overflow-y-auto space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-800 text-base">Konfirmasi Pemesanan</h3>
                <button @click="showCheckoutModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Customer Identity Form -->
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemesan</label>
                    <input type="text" x-model="customerName" placeholder="Contoh: Rian (Siswa/Karyawan)" 
                           class="w-full px-4 py-3 bg-slate-100 rounded-2xl text-xs font-bold text-slate-800 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">No. HP / WhatsApp (Opsional)</label>
                    <input type="text" x-model="customerPhone" placeholder="08..." 
                           class="w-full px-4 py-3 bg-slate-100 rounded-2xl text-xs font-semibold text-slate-800 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan Layanan</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col items-center justify-center p-2.5 rounded-2xl border cursor-pointer text-center"
                               :class="orderType === 'dine_in' ? 'bg-indigo-50 border-indigo-600 text-indigo-700 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'">
                            <input type="radio" x-model="orderType" value="dine_in" class="sr-only">
                            <span class="text-xs">Makan di Tempat</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-2.5 rounded-2xl border cursor-pointer text-center"
                               :class="orderType === 'takeaway' ? 'bg-indigo-50 border-indigo-600 text-indigo-700 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'">
                            <input type="radio" x-model="orderType" value="takeaway" class="sr-only">
                            <span class="text-xs">Bawa Pulang / Pick-up</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-2.5 rounded-2xl border cursor-pointer text-center"
                               :class="orderType === 'delivery' ? 'bg-indigo-50 border-indigo-600 text-indigo-700 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'">
                            <input type="radio" x-model="orderType" value="delivery" class="sr-only">
                            <span class="text-xs">Antar ke Meja</span>
                        </label>
                    </div>
                </div>

                <div x-show="orderType !== 'takeaway'">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Meja / Nama Ruangan</label>
                    <input type="text" x-model="tableNumber" placeholder="Contoh: Meja 04 / Ruang Guru" 
                           class="w-full px-4 py-2.5 bg-slate-100 rounded-2xl text-xs font-semibold text-slate-800 border border-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer"
                               :class="paymentMethod === 'qris' ? 'bg-emerald-50 border-emerald-600 text-emerald-700 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'">
                            <input type="radio" x-model="paymentMethod" value="qris" class="mr-2">
                            <span class="text-xs">QRIS / Instant Bayar</span>
                        </label>
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer"
                               :class="paymentMethod === 'cash' ? 'bg-indigo-50 border-indigo-600 text-indigo-700 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'">
                            <input type="radio" x-model="paymentMethod" value="cash" class="mr-2">
                            <span class="text-xs">Bayar di Kasir</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Items Summary List -->
            <div class="p-3 bg-slate-50 rounded-2xl space-y-2 max-h-36 overflow-y-auto">
                <template x-for="item in cart" :key="item.id">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-800" x-text="item.name + ' (' + item.qty + 'x)'"></span>
                        <span class="font-bold text-slate-900" x-text="formatRupiah(item.harga_jual * item.qty)"></span>
                    </div>
                </template>
            </div>

            <div class="p-4 bg-indigo-50 rounded-2xl flex items-center justify-between text-indigo-950">
                <span class="font-bold text-xs uppercase tracking-wider">Total Pembayaran</span>
                <span class="font-black text-xl text-indigo-600" x-text="formatRupiah(totalAmount)"></span>
            </div>

            <button @click="submitCustomerOrder()" 
                    :disabled="!customerName || loading" 
                    class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 disabled:opacity-50 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-indigo-500/30 transition transform active:scale-95">
                <span x-show="!loading">Kirim Pesanan Sekarang</span>
                <span x-show="loading" style="display:none;">Mengirim Pesanan...</span>
            </button>
        </div>
    </div>
</div>

<script>
function customerShopApp() {
    return {
        products: @json($products),
        filteredProducts: [],
        searchQuery: '',
        cart: [],
        showCheckoutModal: false,
        customerName: '',
        customerPhone: '',
        orderType: 'dine_in',
        tableNumber: '',
        paymentMethod: 'qris',
        loading: false,

        init() {
            this.filteredProducts = this.products;
        },

        filterProducts() {
            if (!this.searchQuery) {
                this.filteredProducts = this.products;
                return;
            }
            const q = this.searchQuery.toLowerCase();
            this.filteredProducts = this.products.filter(p => p.name.toLowerCase().includes(q));
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
                }
            }
        },

        updateCartQty(productId, change) {
            const item = this.cart.find(i => i.id === productId);
            if (item) {
                item.qty += change;
                if (item.qty <= 0) {
                    this.cart = this.cart.filter(i => i.id !== productId);
                }
            }
        },

        getCartQty(productId) {
            const item = this.cart.find(i => i.id === productId);
            return item ? item.qty : 0;
        },

        get totalQty() {
            return this.cart.reduce((sum, item) => sum + item.qty, 0);
        },

        get totalAmount() {
            return this.cart.reduce((sum, item) => sum + (item.harga_jual * item.qty), 0);
        },

        submitCustomerOrder() {
            if (!this.customerName) {
                alert('Silakan isi nama pemesan terlebih dahulu.');
                return;
            }

            this.loading = true;

            const payload = {
                tenant_id: {{ $selectedTenant->id }},
                customer_name: this.customerName,
                customer_phone: this.customerPhone,
                order_type: this.orderType,
                table_number: this.tableNumber,
                payment_method: this.paymentMethod,
                items: this.cart.map(i => ({ id: i.id, qty: i.qty }))
            };

            fetch(`{{ route('shop.order') }}`, {
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
                    alert(res.message || 'Terjadi kesalahan pemesanan.');
                }
            })
            .catch(err => {
                this.loading = false;
                alert('Gagal mengirim pesanan.');
            });
        },

        formatRupiah(val) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
        }
    }
}
</script>
</body>
</html>
