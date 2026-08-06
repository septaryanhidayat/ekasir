<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(15);

        return view('desktop.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:100',
            'hpp' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:4096',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $user = Auth::user();
        $tenantId = $request->tenant_id 
            ?? $user->tenant_id 
            ?? session('active_tenant_id') 
            ?? \App\Models\Tenant::where('is_active', true)->first()?->id;

        if (!$tenantId) {
            return back()->with('error', 'Gagal menambahkan produk: Belum ada outlet/tenant yang terdaftar.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $barcode = $request->barcode ?: 'BRD' . date('Ymd') . rand(1000, 9999);

        Product::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'barcode' => $barcode,
            'image' => $imagePath,
            'hpp' => $request->hpp,
            'harga_jual' => $request->harga_jual,
            'stock' => $request->stock,
            'is_active' => true,
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:100',
            'hpp' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:4096',
        ]);

        $barcode = $request->barcode ?: ($product->barcode ?: 'BRD' . date('Ymd') . rand(1000, 9999));

        $data = [
            'name' => $request->name,
            'barcode' => $barcode,
            'hpp' => $request->hpp,
            'harga_jual' => $request->harga_jual,
            'stock' => $request->stock,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return back()->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus!');
    }
}
