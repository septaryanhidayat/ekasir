<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $tenants = Tenant::where('is_active', true)->get();
        
        $selectedTenantId = $request->input('tenant_id', session('customer_tenant_id', $tenants->first()->id ?? 1));
        session(['customer_tenant_id' => $selectedTenantId]);

        $selectedTenant = Tenant::find($selectedTenantId);

        $query = Product::withoutGlobalScopes()
            ->where('tenant_id', $selectedTenantId)
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get();

        return view('customer.index', compact('tenants', 'selectedTenant', 'products'));
    }

    public function checkout(Request $request)
    {
        $selectedTenantId = session('customer_tenant_id', 1);
        $selectedTenant = Tenant::find($selectedTenantId);

        return view('customer.checkout', compact('selectedTenant'));
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'table_number' => 'nullable|string|max:50',
            'payment_method' => 'required|in:qris,cash',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalHpp = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::withoutGlobalScopes()
                    ->where('tenant_id', $request->tenant_id)
                    ->findOrFail($item['id']);

                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok produk '{$product->name}' tidak mencukupi.");
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

            $orderStatus = $request->payment_method === 'qris' ? 'paid' : 'processing';
            $cashPaid = $request->payment_method === 'qris' ? $totalAmount : 0;

            $transaction = Transaction::withoutGlobalScopes()->create([
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'tenant_id' => $request->tenant_id,
                'user_id' => auth()->id() ?? null,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'order_type' => $request->order_type,
                'table_number' => $request->table_number,
                'order_source' => 'customer_app',
                'order_status' => $orderStatus,
                'notes' => $request->notes ?? null,
                'total_hpp' => $totalHpp,
                'total_amount' => $totalAmount,
                'cash_paid' => $cashPaid,
                'change_amount' => 0,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);

            foreach ($itemsData as $detail) {
                $transaction->details()->create($detail);
            }

            DB::commit();

            // Save order ID to customer session history
            $orderHistory = session('my_customer_orders', []);
            $orderHistory[] = $transaction->id;
            session(['my_customer_orders' => $orderHistory]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat!',
                'redirect_url' => route('shop.track', $transaction->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function track($id)
    {
        $transaction = Transaction::withoutGlobalScopes()
            ->with(['details', 'tenant'])
            ->findOrFail($id);

        return view('customer.track', compact('transaction'));
    }

    public function history()
    {
        $orderIds = session('my_customer_orders', []);
        $orders = Transaction::withoutGlobalScopes()
            ->with(['details', 'tenant'])
            ->whereIn('id', $orderIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.history', compact('orders'));
    }
}
