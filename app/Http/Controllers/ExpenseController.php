<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = Expense::with(['user', 'tenant'])
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $expenses = (clone $query)->orderBy('expense_date', 'desc')->orderBy('created_at', 'desc')->paginate(15);
        $totalExpenses = (clone $query)->sum('amount');

        // Common preset categories for operational expenses
        $presetCategories = ['Gaji Karyawan', 'Listrik & Air', 'Bensin & Transportasi', 'Sewa Tempat', 'Maintenance & Perbaikan', 'Operasional Toko', 'Lain-lain'];

        return view('desktop.expenses.index', compact(
            'expenses',
            'startDate',
            'endDate',
            'totalExpenses',
            'presetCategories'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $user = Auth::user();
        $tenantId = $request->tenant_id 
            ?? $user->tenant_id 
            ?? session('active_tenant_id') 
            ?? Tenant::where('is_active', true)->first()?->id;

        if (!$tenantId) {
            return back()->with('error', 'Gagal menyimpan pengeluaran: Belum ada outlet/tenant yang terdaftar.');
        }

        Expense::create([
            'tenant_id' => $tenantId,
            'user_id' => $user->id,
            'category' => trim($request->category),
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Pengeluaran operasional berhasil dicatat!');
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $expense->update([
            'category' => trim($request->category),
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Data pengeluaran operasional berhasil diperbarui!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return back()->with('success', 'Data pengeluaran operasional berhasil dihapus!');
    }
}
