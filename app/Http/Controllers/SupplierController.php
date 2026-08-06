<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('products');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $suppliers = (clone $query)->orderBy('name', 'asc')->paginate(15);
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', true)->count();

        return view('desktop.suppliers.index', compact('suppliers', 'totalSuppliers', 'activeSuppliers'));
    }

    public function mobileIndex(Request $request)
    {
        $query = Supplier::withCount('products');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $suppliers = (clone $query)->orderBy('name', 'asc')->get();
        $totalSuppliers = count($suppliers);
        $activeSuppliers = Supplier::where('is_active', true)->count();

        return view('mobile.suppliers', compact('suppliers', 'totalSuppliers', 'activeSuppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $user = Auth::user();
        $tenantId = $request->tenant_id 
            ?? $user->tenant_id 
            ?? session('active_tenant_id') 
            ?? Tenant::where('is_active', true)->first()?->id;

        if (!$tenantId) {
            return back()->with('error', 'Gagal menyimpan suplier: Belum ada outlet/tenant yang terdaftar.');
        }

        $code = $request->code ?: 'SUP' . date('Ymd') . rand(100, 999);

        Supplier::create([
            'tenant_id' => $tenantId,
            'code' => trim($code),
            'name' => trim($request->name),
            'phone' => $request->phone ? trim($request->phone) : null,
            'email' => $request->email ? trim($request->email) : null,
            'address' => $request->address ? trim($request->address) : null,
            'notes' => $request->notes ? trim($request->notes) : null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Data Suplier / Distributor berhasil ditambahkan!');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $supplier->update([
            'code' => $request->code ? trim($request->code) : $supplier->code,
            'name' => trim($request->name),
            'phone' => $request->phone ? trim($request->phone) : null,
            'email' => $request->email ? trim($request->email) : null,
            'address' => $request->address ? trim($request->address) : null,
            'notes' => $request->notes ? trim($request->notes) : null,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $supplier->is_active,
        ]);

        return back()->with('success', 'Data Suplier / Distributor berhasil diperbarui!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return back()->with('success', 'Data Suplier / Distributor berhasil dihapus!');
    }
}
