<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('tenant')->paginate(15);
        $tenants = Tenant::where('is_active', true)->get();

        return view('desktop.users.index', compact('users', 'tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:superadmin,manager,cashier',
            'tenant_id' => 'nullable|required_if:role,manager,cashier|exists:tenants,id',
            'password' => 'required|min:6',
            'pin' => 'nullable|numeric|digits:6',
            'phone' => 'nullable|string',
        ]);

        User::create([
            'tenant_id' => $request->role === 'superadmin' ? null : $request->tenant_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'pin' => $request->pin ?: '123456',
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:superadmin,manager,cashier',
            'tenant_id' => 'nullable|required_if:role,manager,cashier|exists:tenants,id',
            'password' => 'nullable|min:6',
            'pin' => 'nullable|numeric|digits:6',
            'phone' => 'nullable|string',
        ]);

        $data = [
            'tenant_id' => $request->role === 'superadmin' ? null : $request->tenant_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->filled('pin')) {
            $data['pin'] = $request->pin;
        }

        $user->update($data);

        return back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus!');
    }
}
