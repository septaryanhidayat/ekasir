@extends('layouts.mobile')

@section('title', 'Struk Transaksi - E-Kasir')

@push('styles')
<style>
@media print {
    /* Hide layout navigation, top header, buttons, and non-receipt app UI */
    .no-print, header, nav, button, a {
        display: none !important;
    }

    body, html, .mobile-container, main {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-shadow: none !important;
    }

    .glass-card {
        background: none !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    #thermal-paper {
        border: none !important;
        box-shadow: none !important;
        width: 100% !important;
        max-width: 80mm !important;
        margin: 0 auto !important;
        padding: 8px !important;
        font-family: monospace !important;
        color: #000000 !important;
    }
}
</style>
@endpush

@section('content')
<div class="glass-card rounded-3xl p-6 shadow-2xl mb-6 relative">

    <!-- Thermal Paper Wrapper -->
    <div id="thermal-paper" class="bg-white p-6 rounded-2xl border border-dashed border-slate-300 font-mono text-slate-800 text-xs shadow-inner">
        <!-- Receipt Header -->
        <div class="text-center pb-4 border-b border-dashed border-slate-300">
            <h2 class="font-black text-lg text-slate-900 uppercase tracking-tight">ROBBANI MART</h2>
            @if($transaction->tenant?->name && !in_array(strtolower(trim($transaction->tenant->name)), ['robbani mart', 'robbani mart (pusat)', 'robbani mart pusat']))
                <h3 class="font-bold text-xs text-indigo-700 uppercase tracking-wider mt-0.5">{{ $transaction->tenant->name }}</h3>
            @endif
            @if($transaction->tenant?->address)
                <p class="text-[10px] text-slate-500 mt-1">{{ $transaction->tenant->address }}</p>
            @endif
            @if($transaction->tenant?->phone)
                <p class="text-[10px] text-slate-500">Telp: {{ $transaction->tenant->phone }}</p>
            @endif
        </div>

        <!-- Receipt Meta -->
        <div class="py-3 border-b border-dashed border-slate-300 space-y-1 text-[11px]">
            <div class="flex justify-between">
                <span>No. Invoice:</span>
                <span class="font-bold">{{ $transaction->invoice_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>Waktu:</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir / Operator:</span>
                <span>{{ $transaction->user?->name ?? ($transaction->customer_name ? $transaction->customer_name . ' (Online)' : 'Customer App / Online') }}</span>
            </div>
            @if($transaction->customer_name && $transaction->user)
            <div class="flex justify-between">
                <span>Pelanggan:</span>
                <span>{{ $transaction->customer_name }}</span>
            </div>
            @endif
            @if($transaction->order_type)
            <div class="flex justify-between">
                <span>Tipe Pesanan:</span>
                <span class="uppercase font-bold text-indigo-600">{{ str_replace('_', ' ', $transaction->order_type) }}</span>
            </div>
            @endif
            @if($transaction->table_number)
            <div class="flex justify-between">
                <span>No. Meja:</span>
                <span class="font-bold">{{ $transaction->table_number }}</span>
            </div>
            @endif
        </div>

        <!-- Items Table -->
        <div class="py-3 border-b border-dashed border-slate-300 space-y-2">
            @foreach($transaction->details as $item)
                <div>
                    <div class="font-bold text-slate-900">{{ $item->product_name }}</div>
                    <div class="flex justify-between text-slate-500 text-[11px]">
                        <span>{{ $item->qty }} x Rp {{ number_format($item->selling_price, 0, ',', '.') }}</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="py-3 space-y-1 text-[11px]">
            <div class="flex justify-between font-extrabold text-sm text-slate-900 pt-1">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Metode Bayar:</span>
                <span class="uppercase font-bold">{{ $transaction->payment_method }}</span>
            </div>
            @if($transaction->payment_method === 'cash')
            <div class="flex justify-between text-slate-600">
                <span>Tunai (Paid):</span>
                <span>Rp {{ number_format($transaction->cash_paid, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-emerald-600">
                <span>Kembalian:</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
            @else
            <div class="flex justify-between font-bold text-emerald-600">
                <span>Status Bayar:</span>
                <span class="uppercase">{{ $transaction->order_status ?? 'PAID' }}</span>
            </div>
            @endif
        </div>

        <!-- Footer Greeting -->
        <div class="text-center pt-4 border-t border-dashed border-slate-300 text-[10px] text-slate-500">
            <p>Terima Kasih Atas Kunjungan Anda!</p>
            <p class="font-bold text-slate-700 mt-1">E-Kasir Multi-Outlet POS</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 space-y-3 no-print">
        <button onclick="window.print()" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl shadow-lg shadow-indigo-500/30 flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span>Cetak Struk Thermal</span>
        </button>

        <a href="{{ route('mobile.transactions') }}" class="block text-center w-full py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
            &larr; Kembali ke Riwayat Transaksi
        </a>
    </div>
</div>
@endsection
