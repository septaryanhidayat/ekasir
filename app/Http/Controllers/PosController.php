<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant ?? ($user->role === 'superadmin' && session('active_tenant_id') ? \App\Models\Tenant::find(session('active_tenant_id')) : null);

        // Get open cash register
        $openRegister = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        // Calculate sisa kas (Opening + Cash In - Cash Out + Cash Sales)
        $cashBalance = 0;
        if ($openRegister) {
            $cashIn = $openRegister->cashFlows()->where('type', 'in')->sum('amount');
            $cashOut = $openRegister->cashFlows()->where('type', 'out')->sum('amount');
            $cashSales = $openRegister->transactions()->where('payment_method', 'cash')->sum('total_amount');

            $cashBalance = $openRegister->opening_amount + $cashIn - $cashOut + $cashSales;
        }

        $todayTransactionsCount = Transaction::whereDate('created_at', today())->count();
        $todaySalesTotal = Transaction::whereDate('created_at', today())->sum('total_amount');
        
        $pendingOnlineOrdersCount = Transaction::where('order_source', 'customer_app')
            ->whereIn('order_status', ['paid', 'processing'])
            ->count();

        return view('mobile.dashboard', compact('user', 'tenant', 'openRegister', 'cashBalance', 'todayTransactionsCount', 'todaySalesTotal', 'pendingOnlineOrdersCount'));
    }

    public function pos()
    {
        $user = Auth::user();
        $openRegister = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('mobile.pos', compact('openRegister', 'products'));
    }

    public function searchProduct(Request $request)
    {
        $query = $request->input('q');
        $barcode = $request->input('barcode');

        $productsQuery = Product::where('is_active', true);

        if ($barcode) {
            $productsQuery->where('barcode', $barcode);
        } else if ($query) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            });
        }

        $products = $productsQuery->limit(20)->get();

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'cash_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();
        $tenantId = $user->tenant_id ?? session('active_tenant_id');

        $openRegister = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalHpp = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok produk '{$product->name}' tidak mencukupi (Sisa: {$product->stock}).");
                }

                $subtotal = $product->harga_jual * $item['qty'];
                $subtotalHpp = $product->hpp * $item['qty'];

                $totalAmount += $subtotal;
                $totalHpp += $subtotalHpp;

                // Deduct stock
                $product->decrement('stock', $item['qty']);

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'cost_price' => $product->hpp,
                    'selling_price' => $product->harga_jual,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ];
            }

            if ($request->cash_paid < $totalAmount && $request->payment_method === 'cash') {
                throw new \Exception("Uang pembayaran kurang dari total belanja.");
            }

            $changeAmount = max(0, $request->cash_paid - $totalAmount);

            $transaction = Transaction::create([
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'tenant_id' => $tenantId,
                'user_id' => $user->id,
                'cash_register_id' => $openRegister ? $openRegister->id : null,
                'order_source' => 'pos',
                'order_status' => 'completed',
                'total_hpp' => $totalHpp,
                'total_amount' => $totalAmount,
                'cash_paid' => $request->cash_paid,
                'change_amount' => $changeAmount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);

            foreach ($itemsData as $detail) {
                $transaction->details()->create($detail);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil!',
                'redirect_url' => route('mobile.receipt', $transaction->id),
                'transaction_id' => $transaction->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load(['details.product', 'user', 'tenant']);
        return view('mobile.receipt', compact('transaction'));
    }

    public function transactions()
    {
        $transactions = Transaction::with(['details', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('mobile.transactions', compact('transactions'));
    }

    public function onlineOrders()
    {
        $orders = Transaction::where('order_source', 'customer_app')
            ->with(['details'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('mobile.online-orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'order_status' => 'required|in:paid,processing,ready,completed,cancelled',
        ]);

        $transaction->update([
            'order_status' => $request->order_status,
        ]);

        return back()->with('success', 'Status pesanan customer berhasil diperbarui ke: ' . strtoupper($request->order_status));
    }
}
