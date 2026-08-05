<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmartInputController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to Customer Shop
Route::get('/', function () {
    return redirect()->route('shop.index');
});

// Customer Mobile E-Commerce Storefront Routes (Public)
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [CustomerOrderController::class, 'index'])->name('index');
    Route::post('/order', [CustomerOrderController::class, 'storeOrder'])->name('order');
    Route::get('/track/{transaction}', [CustomerOrderController::class, 'track'])->name('track');
    Route::get('/history', [CustomerOrderController::class, 'history'])->name('history');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/quick-pin-login', [AuthController::class, 'quickPinLogin'])->name('quick-pin-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes (Staff / Cashier / Manager / Superadmin)
Route::middleware(['auth'])->group(function () {
    Route::post('/switch-tenant/{tenant}', [AuthController::class, 'switchTenant'])->name('switch-tenant');

    // Mobile POS Routes (Mobile Banking Style)
    Route::prefix('m')->name('mobile.')->group(function () {
        Route::get('/dashboard', [PosController::class, 'dashboard'])->name('dashboard');
        Route::get('/pos', [PosController::class, 'pos'])->name('pos');
        Route::get('/api/products/search', [PosController::class, 'searchProduct'])->name('api.products.search');
        Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
        Route::get('/receipt/{transaction}', [PosController::class, 'receipt'])->name('receipt');
        Route::get('/transactions', [PosController::class, 'transactions'])->name('transactions');

        // Customer Online Orders Management for Cashier
        Route::get('/online-orders', [PosController::class, 'onlineOrders'])->name('online-orders');
        Route::post('/online-orders/{transaction}/update-status', [PosController::class, 'updateOrderStatus'])->name('online-orders.update-status');

        // Smart Input Product via Camera
        Route::get('/smart-input', [SmartInputController::class, 'index'])->name('smart-input');
        Route::post('/smart-input', [SmartInputController::class, 'store'])->name('smart-input.store');

        // Daily Register & Petty Cash
        Route::get('/cash-register', [CashRegisterController::class, 'index'])->name('cash-register');
        Route::post('/cash-register/open', [CashRegisterController::class, 'openRegister'])->name('cash-register.open');
        Route::post('/cash-register/cash-flow', [CashRegisterController::class, 'addCashFlow'])->name('cash-register.cash-flow');
        Route::post('/cash-register/close', [CashRegisterController::class, 'closeRegister'])->name('cash-register.close');
    });

    // Desktop Management Dashboard Routes
    Route::prefix('desktop')->name('desktop.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Products Management
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Outlets / Tenants (Superadmin)
        Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
        Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
        Route::post('/tenants/{tenant}/toggle', [TenantController::class, 'toggleStatus'])->name('tenants.toggle');

        // Users & Staff Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Financial & Cash Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
});

// Fallback route to serve uploaded public storage files on cPanel / Shared Hosting
Route::get('/storage/{filename}', function ($filename) {
    $path = storage_path('app/public/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->where('filename', '.*');

