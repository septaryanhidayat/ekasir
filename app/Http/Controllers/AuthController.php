<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        $tenants = Tenant::where('is_active', true)->get();
        return view('auth.login', compact('tenants'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if ($user->role === 'superadmin' && $request->filled('tenant_id')) {
                session(['active_tenant_id' => $request->tenant_id]);
            } else if ($user->tenant_id) {
                session(['active_tenant_id' => $user->tenant_id]);
            }

            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function quickPinLogin(Request $request)
    {
        $request->validate([
            'pin' => 'required|numeric|digits:6',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $user = User::where('tenant_id', $request->tenant_id)
            ->where('pin', $request->pin)
            ->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
            session(['active_tenant_id' => $user->tenant_id]);

            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'pin' => 'PIN Kasir tidak valid untuk outlet ini.',
        ]);
    }

    public function switchTenant(Request $request, Tenant $tenant)
    {
        if (Auth::user()->role === 'superadmin') {
            session(['active_tenant_id' => $tenant->id]);
            return back()->with('success', 'Outlet berhasil diubah ke ' . $tenant->name);
        }

        return back()->with('error', 'Akses ditolak.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectBasedOnRole()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            return redirect()->route('desktop.dashboard');
        } elseif ($user->role === 'manager') {
            return redirect()->route('desktop.dashboard');
        }

        return redirect()->route('mobile.dashboard');
    }
}
