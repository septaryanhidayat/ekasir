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
        return view('mobile.smart-input');
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
            'image_camera_base64' => 'nullable|string',
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
        } elseif ($request->filled('image_camera_base64')) {
            $base64Image = $request->image_camera_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]);
                $image = base64_decode($base64Image);
                $filename = 'products/' . Str::random(20) . '.' . $type;
                Storage::disk('public')->put($filename, $image);
                $imagePath = $filename;
            }
        }

        $barcode = $request->barcode ?: 'BRD' . time() . rand(100, 999);

        $product = Product::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'barcode' => $barcode,
            'image' => $imagePath,
            'hpp' => $request->hpp,
            'harga_jual' => $request->harga_jual,
            'stock' => $request->stock,
            'is_active' => true,
        ]);

        return redirect()->route('mobile.pos')->with('success', "Produk '{$product->name}' berhasil ditambahkan!");
    }
}
