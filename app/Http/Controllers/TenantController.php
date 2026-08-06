<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount(['users', 'products', 'transactions'])->paginate(15);
        return view('desktop.tenants.index', compact('tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tenants,code',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'bank_info' => 'nullable|string|max:255',
            'ewallet_info' => 'nullable|string|max:255',
            'qris_info' => 'nullable|string|max:500',
        ]);

        Tenant::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'phone' => $request->phone,
            'bank_info' => $request->bank_info,
            'ewallet_info' => $request->ewallet_info,
            'qris_info' => $request->qris_info,
            'is_active' => true,
        ]);

        return back()->with('success', 'Outlet berhasil ditambahkan!');
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tenants,code,' . $tenant->id,
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'bank_info' => 'nullable|string|max:255',
            'ewallet_info' => 'nullable|string|max:255',
            'qris_info' => 'nullable|string|max:500',
        ]);

        $tenant->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'phone' => $request->phone,
            'bank_info' => $request->bank_info,
            'ewallet_info' => $request->ewallet_info,
            'qris_info' => $request->qris_info,
        ]);

        return back()->with('success', 'Data outlet berhasil diperbarui!');
    }

    public function toggleStatus(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);
        return back()->with('success', 'Status outlet berhasil diubah.');
    }
}
