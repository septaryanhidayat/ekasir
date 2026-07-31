@extends('layouts.mobile')

@section('title', 'Riwayat Transaksi - E-Kasir')

@section('content')
<div class="glass-card rounded-3xl p-6 shadow-xl mb-6">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
        <h3 class="font-extrabold text-slate-800 text-sm">Riwayat Struk & Transaksi</h3>
        <span class="text-xs text-slate-400 font-semibold">{{ $transactions->total() }} Total</span>
    </div>

    <div class="space-y-3">
        @forelse($transactions as $tx)
            <a href="{{ route('mobile.receipt', $tx->id) }}" class="block p-4 bg-slate-50 hover:bg-indigo-50/50 rounded-2xl border border-slate-200/80 transition shadow-sm group">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="font-bold text-xs text-slate-800 group-hover:text-indigo-600 transition">{{ $tx->invoice_number }}</span>
                    <span class="font-extrabold text-xs text-emerald-600">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <div class="flex items-center space-x-2">
                        <span>{{ $tx->created_at->format('d/m/H:i') }}</span>
                        <span>•</span>
                        <span>{{ $tx->details->count() }} Produk</span>
                    </div>
                    <span class="px-2 py-0.5 bg-slate-200 group-hover:bg-indigo-100 group-hover:text-indigo-700 text-slate-600 rounded-md font-bold text-[9px] uppercase">
                        {{ $tx->payment_method }}
                    </span>
                </div>
            </a>
        @empty
            <div class="py-8 text-center text-slate-400 text-xs">
                Belum ada data transaksi.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
