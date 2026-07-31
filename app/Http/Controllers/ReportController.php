<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Models\CashRegister;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = Transaction::with(['user', 'details'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        $totalRevenue = (clone $query)->sum('total_amount');
        $totalHpp = (clone $query)->sum('total_hpp');
        $totalProfit = $totalRevenue - $totalHpp;

        $cashRegisters = CashRegister::with(['user'])
            ->whereDate('opened_at', '>=', $startDate)
            ->whereDate('opened_at', '<=', $endDate)
            ->orderBy('opened_at', 'desc')
            ->paginate(15, ['*'], 'registers_page');

        return view('desktop.reports.index', compact(
            'transactions',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalHpp',
            'totalProfit',
            'cashRegisters'
        ));
    }
}
