@extends('layouts.mobile')

@section('title', 'Pesanan Masuk Customer - E-Kasir')

@section('content')
<div class="glass-card rounded-3xl p-6 shadow-xl mb-6">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
        <div>
            <h3 class="font-extrabold text-slate-800 text-sm">Pesanan Online Customer</h3>
            <p class="text-[11px] text-slate-400">Pemesanan dari aplikasi e-commerce kantin</p>
        </div>
        <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 font-extrabold text-xs rounded-full">
            {{ $orders->total() }} Orders
        </span>
    </div>

    <div class="space-y-4">
        @forelse($orders as $o)
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-bold text-xs text-indigo-600">{{ $o->invoice_number }}</span>
                        <p class="text-xs font-extrabold text-slate-800">{{ $o->customer_name }} ({{ strtoupper($o->order_type) }})</p>
                    </div>

                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase
                        @if($o->order_status === 'paid') bg-emerald-100 text-emerald-700
                        @elseif($o->order_status === 'processing') bg-amber-100 text-amber-700
                        @elseif($o->order_status === 'ready') bg-purple-100 text-purple-700
                        @else bg-slate-200 text-slate-700 @endif">
                        {{ $o->order_status }}
                    </span>
                </div>

                <div class="py-2 border-t border-b border-slate-200/60 space-y-1 text-xs text-slate-600">
                    @foreach($o->details as $d)
                        <div class="flex justify-between">
                            <span>{{ $d->product_name }} x {{ $d->qty }}</span>
                            <span class="font-bold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between pt-1">
                    <span class="font-extrabold text-sm text-slate-900">Total: Rp {{ number_format($o->total_amount, 0, ',', '.') }}</span>

                    <!-- Status Changer Buttons for Cashier -->
                    <form action="{{ route('mobile.online-orders.update-status', $o->id) }}" method="POST" class="flex items-center space-x-1">
                        @csrf
                        @if($o->order_status === 'paid' || $o->order_status === 'processing')
                            <input type="hidden" name="order_status" value="ready">
                            <button type="submit" class="px-3 py-1.5 bg-purple-600 text-white font-extrabold text-xs rounded-xl shadow-md">
                                Tandai Siap Diambil &rarr;
                            </button>
                        @elseif($o->order_status === 'ready')
                            <input type="hidden" name="order_status" value="completed">
                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white font-extrabold text-xs rounded-xl shadow-md">
                                Tandai Selesai ✓
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        @empty
            <div class="py-10 text-center text-slate-400 text-xs">
                Belum ada pesanan online customer.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
