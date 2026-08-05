<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'superadmin';

        $activeTenantId = session('active_tenant_id') ?? $user->tenant_id;
        $activeTenant = $activeTenantId ? Tenant::find($activeTenantId) : null;

        // General stats
        $totalOutlets = Tenant::where('is_active', true)->count();
        
        $totalSalesQuery = Transaction::query();
        $totalTransactions = (clone $totalSalesQuery)->count();
        $totalRevenue = (clone $totalSalesQuery)->sum('total_amount');
        $totalHpp = (clone $totalSalesQuery)->sum('total_hpp');
        $totalExpenses = Expense::query()->sum('amount');
        $totalProfit = ($totalRevenue - $totalHpp) - $totalExpenses;

        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 10)->limit(5)->get();

        // Recent 5 transactions
        $recentTransactions = Transaction::with(['user', 'tenant'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Sales last 7 days for Chart.js
        $chartLabels = [];
        $chartSalesData = [];
        $chartProfitData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d M');

            $daySales = Transaction::withoutGlobalScopes()
                ->when(!$isSuperAdmin || $activeTenantId, function ($q) use ($activeTenantId) {
                    $q->where('tenant_id', $activeTenantId);
                })
                ->whereDate('created_at', $date)
                ->sum('total_amount');

            $dayHpp = Transaction::withoutGlobalScopes()
                ->when(!$isSuperAdmin || $activeTenantId, function ($q) use ($activeTenantId) {
                    $q->where('tenant_id', $activeTenantId);
                })
                ->whereDate('created_at', $date)
                ->sum('total_hpp');

            $dayExpenses = Expense::withoutGlobalScopes()
                ->when(!$isSuperAdmin || $activeTenantId, function ($q) use ($activeTenantId) {
                    $q->where('tenant_id', $activeTenantId);
                })
                ->whereDate('expense_date', $date)
                ->sum('amount');

            $chartSalesData[] = (float) $daySales;
            $chartProfitData[] = (float) (($daySales - $dayHpp) - $dayExpenses);
        }

        // Outlet performance breakdown for Superadmin
        $outletStats = [];
        if ($isSuperAdmin) {
            $tenants = Tenant::where('is_active', true)->get();
            foreach ($tenants as $t) {
                $sales = Transaction::withoutGlobalScopes()->where('tenant_id', $t->id)->sum('total_amount');
                $hpp = Transaction::withoutGlobalScopes()->where('tenant_id', $t->id)->sum('total_hpp');
                $expenses = Expense::withoutGlobalScopes()->where('tenant_id', $t->id)->sum('amount');
                $txCount = Transaction::withoutGlobalScopes()->where('tenant_id', $t->id)->count();

                $outletStats[] = [
                    'tenant' => $t,
                    'sales' => $sales,
                    'expenses' => $expenses,
                    'profit' => ($sales - $hpp) - $expenses,
                    'tx_count' => $txCount,
                ];
            }
        }

        return view('desktop.dashboard', compact(
            'user',
            'isSuperAdmin',
            'activeTenant',
            'totalOutlets',
            'totalTransactions',
            'totalRevenue',
            'totalProfit',
            'totalExpenses',
            'totalProducts',
            'lowStockProducts',
            'recentTransactions',
            'chartLabels',
            'chartSalesData',
            'chartProfitData',
            'outletStats'
        ));
    }
}
