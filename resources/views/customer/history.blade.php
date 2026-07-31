<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan Saya - E-Kantin</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<div class="customer-container p-5 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <a href="{{ route('shop.index') }}" class="p-2.5 bg-slate-100 rounded-2xl text-slate-700 text-xs font-bold flex items-center">
            &larr; Kembali ke Shop
        </a>
        <h3 class="font-extrabold text-slate-800 text-sm">Pesanan Saya</h3>
    </div>

    <div class="space-y-3">
        @forelse($orders as $o)
            <a href="{{ route('shop.track', $o->id) }}" class="block p-4 bg-white rounded-2xl border border-slate-200 shadow-sm hover:border-indigo-500 transition space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-900">{{ $o->invoice_number }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase
                        {{ $o->order_status === 'ready' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' }}">
                        {{ $o->order_status }}
                    </span>
                </div>

                <div class="text-xs text-slate-500 flex justify-between">
                    <span>{{ $o->tenant->name }} • {{ $o->details->count() }} Item</span>
                    <span class="font-extrabold text-slate-900">Rp {{ number_format($o->total_amount, 0, ',', '.') }}</span>
                </div>
            </a>
        @empty
            <div class="py-12 text-center text-slate-400 text-xs">
                Belum ada pesanan aktif.
            </div>
        @endforelse
    </div>
</div>
</body>
</html>
