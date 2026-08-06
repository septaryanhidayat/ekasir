<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan #{{ $transaction->invoice_number }}</title>
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
            padding-bottom: 50px;
            box-shadow: 0 0 40px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
<div class="customer-container p-5 space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('shop.index') }}" class="p-2.5 bg-slate-100 rounded-2xl text-slate-700 text-xs font-bold flex items-center">
            &larr; Kembali ke Shop
        </a>
        <span class="text-xs font-extrabold text-indigo-600 uppercase tracking-wider">{{ $transaction->tenant?->name }}</span>
    </div>

    <!-- Status Card Timeline -->
    <div class="bg-gradient-to-tr from-indigo-600 to-purple-600 rounded-3xl p-6 text-white shadow-xl text-center relative overflow-hidden">
        <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-extrabold uppercase tracking-wider">
            Live Order Status
        </span>

        <h2 class="text-2xl font-black mt-3">
            @if($transaction->order_status === 'paid')
                Pembayaran Diterima
            @elseif($transaction->order_status === 'processing')
                Sedang Diproses Kantin
            @elseif($transaction->order_status === 'ready')
                Pesanan Siap Diambil!
            @else
                Pesanan Selesai
            @endif
        </h2>
        <p class="text-xs text-indigo-200 mt-1">No. Order: {{ $transaction->invoice_number }}</p>

        <!-- Dynamic Timeline Steps -->
        <div class="flex items-center justify-around mt-6 pt-4 border-t border-white/20 text-[10px] font-bold">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-emerald-400 text-slate-900 flex items-center justify-center font-black mb-1">✓</div>
                <span>Order Masuk</span>
            </div>
            <div class="h-0.5 w-8 bg-white/40"></div>
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full {{ in_array($transaction->order_status, ['processing', 'ready', 'completed']) ? 'bg-emerald-400 text-slate-900' : 'bg-white/30 text-white' }} flex items-center justify-center font-black mb-1">
                    {{ in_array($transaction->order_status, ['processing', 'ready', 'completed']) ? '✓' : '2' }}
                </div>
                <span>Diproses</span>
            </div>
            <div class="h-0.5 w-8 bg-white/40"></div>
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full {{ in_array($transaction->order_status, ['ready', 'completed']) ? 'bg-amber-400 text-slate-900 animate-bounce' : 'bg-white/30 text-white' }} flex items-center justify-center font-black mb-1">
                    {{ in_array($transaction->order_status, ['ready', 'completed']) ? '★' : '3' }}
                </div>
                <span>Siap Diambil</span>
            </div>
        </div>
    </div>

    <!-- Payment Instruction Card -->
    @if($transaction->payment_method === 'qris')
        <div x-data="{ copiedText: '' }" class="bg-indigo-50 border border-indigo-200 rounded-3xl p-5 text-center space-y-3">
            <span class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-full uppercase tracking-wider">Metode Pembayaran QRIS & Transfer</span>
            <p class="text-xs text-slate-600">Scan QRIS ini menggunakan GoPay / OVO / Dana / Mobile Banking Anda:</p>
            
            <div class="p-4 bg-white rounded-2xl inline-block shadow-md">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($transaction->tenant?->effective_qris_info ?? $transaction->invoice_number) }}" 
                     alt="QRIS Barcode" 
                     class="w-44 h-44 mx-auto">
                <p class="font-mono text-[10px] text-slate-500 font-bold mt-2">NMID / ID: {{ $transaction->tenant?->effective_qris_info ?? 'ID10293847561' }}</p>
            </div>

            @if($transaction->tenant?->effective_bank_info || $transaction->tenant?->effective_ewallet_info || $transaction->tenant?->effective_qris_info)
                <div class="pt-3 border-t border-indigo-100 text-left text-xs space-y-2 bg-white/80 p-3.5 rounded-2xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="font-extrabold text-slate-800 text-[11px]">Opsi Transfer & Copy Detail:</p>
                        <span x-show="copiedText" x-transition class="text-[10px] font-extrabold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200" style="display:none;" x-text="copiedText + ' Tersalin!'"></span>
                    </div>

                    @if($transaction->tenant?->effective_bank_info)
                        @php
                            $bankNum = $transaction->tenant->effective_bank_account_number ?: preg_replace('/[^0-9]/', '', $transaction->tenant->effective_bank_info);
                        @endphp
                        <div class="flex items-center justify-between text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                            <div>
                                <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider">Rekening Bank</span>
                                <span class="font-extrabold text-indigo-700 text-xs">{{ $transaction->tenant->effective_bank_info }}</span>
                            </div>
                            <button @click="copyTextToClipboard('{{ e($bankNum) }}'); copiedText = 'No. Rekening {{ e($bankNum) }}'; setTimeout(() => copiedText = '', 2500)" 
                                    class="px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold rounded-xl text-[11px] flex items-center space-x-1 transition shadow-sm shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Salin No.</span>
                            </button>
                        </div>
                    @endif

                    @if($transaction->tenant?->effective_ewallet_info)
                        @php
                            $ewalletNum = $transaction->tenant->effective_ewallet_account_number ?: preg_replace('/[^0-9]/', '', $transaction->tenant->effective_ewallet_info);
                        @endphp
                        <div class="flex items-center justify-between text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                            <div>
                                <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider">E-Wallet</span>
                                <span class="font-extrabold text-teal-700 text-xs">{{ $transaction->tenant->effective_ewallet_info }}</span>
                            </div>
                            <button @click="copyTextToClipboard('{{ e($ewalletNum) }}'); copiedText = 'No. E-Wallet {{ e($ewalletNum) }}'; setTimeout(() => copiedText = '', 2500)" 
                                    class="px-2.5 py-1.5 bg-teal-600 hover:bg-teal-700 active:scale-95 text-white font-bold rounded-xl text-[11px] flex items-center space-x-1 transition shadow-sm shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Salin No.</span>
                            </button>
                        </div>
                    @endif

                    @if($transaction->tenant?->effective_qris_info)
                        <div class="flex items-center justify-between text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                            <div class="overflow-hidden pr-2">
                                <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider">Payload / NMID QRIS</span>
                                <span class="font-extrabold text-emerald-700 text-xs truncate block">{{ $transaction->tenant->effective_qris_info }}</span>
                            </div>
                            <button @click="copyTextToClipboard('{{ e($transaction->tenant->effective_qris_info) }}'); copiedText = 'Kode QRIS'; setTimeout(() => copiedText = '', 2500)" 
                                    class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold rounded-xl text-[11px] flex items-center space-x-1 transition shadow-sm shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Salin Code</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <p class="font-extrabold text-indigo-700 text-sm">Total Bayar: Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
        </div>
    @endif

    <!-- Invoice Items Summary -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3 text-xs">
        <h3 class="font-extrabold text-slate-800 border-b border-slate-100 pb-2">Rincian Pemesanan</h3>

        <div class="space-y-1 text-slate-600">
            <div class="flex justify-between">
                <span>Pemesan:</span>
                <span class="font-bold text-slate-800">{{ $transaction->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Layanan:</span>
                <span class="font-bold text-indigo-600 uppercase">{{ $transaction->order_type }}</span>
            </div>
            @if($transaction->table_number)
                <div class="flex justify-between">
                    <span>Meja / Lokasi:</span>
                    <span class="font-bold text-slate-800">{{ $transaction->table_number }}</span>
                </div>
            @endif
        </div>

        <div class="pt-2 border-t border-slate-100 space-y-2">
            @foreach($transaction->details as $d)
                <div class="flex items-center justify-between">
                    <span>{{ $d->product_name }} x {{ $d->qty }}</span>
                    <span class="font-bold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-between font-black text-sm text-slate-900">
            <span>Total Bill:</span>
            <span class="text-indigo-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Actions -->
    <div class="space-y-2">
        <a href="{{ route('shop.index') }}" class="block text-center w-full py-3.5 bg-indigo-600 text-white font-bold text-xs rounded-2xl shadow-md">
            Pesan Menu Lainnya &rarr;
        </a>
    </div>
</div>

<script>
function copyTextToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    } else {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        return new Promise((resolve, reject) => {
            document.execCommand('copy') ? resolve() : reject();
            textArea.remove();
        });
    }
}
</script>
</body>
</html>
