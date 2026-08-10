<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SmartInputController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get();
        return view('mobile.smart-input', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mode' => 'nullable|string|in:new,update,update_stock',
            'product_id' => 'required_if:mode,update,update_stock|nullable|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'stock_action' => 'nullable|string|in:add,set',
            'name' => 'required_if:mode,new|nullable|string|max:255',
            'barcode' => 'nullable|string|max:100',
            'hpp' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp,heic|max:20480',
            'image_camera_base64' => 'nullable|string',
            'tenant_id' => 'nullable|exists:tenants,id',
            'created_at' => 'nullable|string',
            'updated_at' => 'nullable|string',
        ]);

        $user = Auth::user();
        $tenantId = $request->tenant_id 
            ?? $user->tenant_id 
            ?? session('active_tenant_id') 
            ?? \App\Models\Tenant::where('is_active', true)->first()?->id;

        if (!$tenantId) {
            return back()->with('error', 'Gagal menambahkan produk: Belum ada outlet/tenant yang terdaftar.');
        }

        // Mode 1: Update Existing Product Stock
        if ($request->product_id || in_array($request->mode, ['update', 'update_stock'])) {
            $product = Product::findOrFail($request->product_id);
            
            if ($request->filled('stock_action') && $request->stock_action === 'add') {
                $product->stock += (int) $request->stock;
            } else {
                $product->stock = (int) $request->stock;
            }

            if ($request->has('supplier_id')) $product->supplier_id = $request->supplier_id;
            if ($request->filled('hpp')) $product->hpp = $request->hpp;
            if ($request->filled('harga_jual')) $product->harga_jual = $request->harga_jual;
            if ($request->filled('barcode')) $product->barcode = $request->barcode;
            if ($request->filled('name')) $product->name = $request->name;

            if ($request->hasFile('image')) {
                $product->image = \App\Services\ImageOptimizer::compressAndStore($request->file('image'));
            }

            if ($request->filled('created_at')) {
                $product->created_at = \Carbon\Carbon::parse($request->created_at);
            }

            if ($request->filled('updated_at')) {
                $product->updated_at = \Carbon\Carbon::parse($request->updated_at);
            } else {
                $product->updated_at = now();
            }

            $product->save();

            return redirect()->route('mobile.dashboard')->with('success', "Stok produk '{$product->name}' berhasil diperbarui! Stok saat ini: {$product->stock} unit.");
        }

        // Mode 2: Create New Product
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = \App\Services\ImageOptimizer::compressAndStore($request->file('image'));
        } elseif ($request->filled('image_camera_base64')) {
            $imagePath = \App\Services\ImageOptimizer::compressAndStore($request->image_camera_base64);
        }

        $barcode = $request->barcode ?: 'BRD' . date('Ymd') . rand(1000, 9999);

        $productData = [
            'tenant_id' => $tenantId,
            'supplier_id' => $request->supplier_id,
            'name' => $request->name,
            'barcode' => $barcode,
            'image' => $imagePath,
            'hpp' => $request->hpp ?? 0,
            'harga_jual' => $request->harga_jual ?? 0,
            'stock' => $request->stock,
            'is_active' => true,
        ];

        if ($request->filled('created_at')) {
            $productData['created_at'] = \Carbon\Carbon::parse($request->created_at);
        }

        if ($request->filled('updated_at')) {
            $productData['updated_at'] = \Carbon\Carbon::parse($request->updated_at);
        }

        $product = Product::create($productData);

        return redirect()->route('mobile.dashboard')->with('success', "Produk baru '{$product->name}' berhasil ditambahkan dengan stok {$product->stock} unit!");
    }
}
