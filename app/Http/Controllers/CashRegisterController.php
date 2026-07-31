<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashRegisterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $openRegister = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        $historyRegisters = CashRegister::where('user_id', $user->id)
            ->orderBy('opened_at', 'desc')
            ->limit(10)
            ->get();

        $cashBalance = 0;
        $totalSales = 0;
        $totalCashIn = 0;
        $totalCashOut = 0;

        if ($openRegister) {
            $totalSales = $openRegister->transactions()->where('payment_method', 'cash')->sum('total_amount');
            $totalCashIn = $openRegister->cashFlows()->where('type', 'in')->sum('amount');
            $totalCashOut = $openRegister->cashFlows()->where('type', 'out')->sum('amount');

            $cashBalance = $openRegister->opening_amount + $totalCashIn - $totalCashOut + $totalSales;
        }

        return view('mobile.cash-register', compact(
            'openRegister', 
            'historyRegisters', 
            'cashBalance', 
            'totalSales', 
            'totalCashIn', 
            'totalCashOut'
        ));
    }

    public function openRegister(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $tenantId = $user->tenant_id ?? session('active_tenant_id');

        $existingOpen = CashRegister::where('user_id', $user->id)->where('status', 'open')->first();
        if ($existingOpen) {
            return back()->with('error', 'Anda sudah memiliki kas harian yang aktif.');
        }

        CashRegister::create([
            'tenant_id' => $tenantId,
            'user_id' => $user->id,
            'opening_amount' => $request->opening_amount,
            'status' => 'open',
            'opened_at' => now(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Kas harian berhasil dibuka!');
    }

    public function addCashFlow(Request $request)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $tenantId = $user->tenant_id ?? session('active_tenant_id');

        $openRegister = CashRegister::where('user_id', $user->id)->where('status', 'open')->first();

        CashFlow::create([
            'tenant_id' => $tenantId,
            'cash_register_id' => $openRegister ? $openRegister->id : null,
            'user_id' => $user->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        $msg = $request->type === 'in' ? 'Kas Masuk berhasil dicatat.' : 'Kas Keluar berhasil dicatat.';
        return back()->with('success', $msg);
    }

    public function closeRegister(Request $request)
    {
        $request->validate([
            'closing_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $openRegister = CashRegister::where('user_id', $user->id)->where('status', 'open')->first();

        if (!$openRegister) {
            return back()->with('error', 'Tidak ada kas harian aktif yang perlu ditutup.');
        }

        $totalSales = $openRegister->transactions()->where('payment_method', 'cash')->sum('total_amount');
        $totalCashIn = $openRegister->cashFlows()->where('type', 'in')->sum('amount');
        $totalCashOut = $openRegister->cashFlows()->where('type', 'out')->sum('amount');

        $expectedAmount = $openRegister->opening_amount + $totalCashIn - $totalCashOut + $totalSales;
        $variance = $request->closing_amount - $expectedAmount;

        $openRegister->update([
            'closing_amount' => $request->closing_amount,
            'expected_amount' => $expectedAmount,
            'variance' => $variance,
            'closed_at' => now(),
            'status' => 'closed',
            'notes' => $request->notes ? $openRegister->notes . ' | ' . $request->notes : $openRegister->notes,
        ]);

        return back()->with('success', 'Kas harian berhasil ditutup.');
    }
}
