<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Kasir\KasirDashboardController;
use App\Http\Controllers\Kasir\PosController;
use App\Http\Controllers\Kasir\TransactionHistoryController;
use App\Http\Controllers\TransactionReceiptController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - MyTokoGue POS & Store System
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('kasir.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Shared Auth Routes (Admin & Kasir)
Route::middleware(['auth'])->group(function () {
    Route::get('/transactions/{transaction}/receipt', [TransactionReceiptController::class, 'show'])->name('transactions.receipt');
    Route::get('/receipt/settings', [TransactionReceiptController::class, 'preview'])->name('receipt.settings');
});

// ADMIN ROUTES (Hanya Role Admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Produk
    Route::resource('products', ProductController::class);

    // Manajemen Kategori
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    // Manajemen Kasir & User
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

    // Laporan & Analitik Penjualan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// KASIR ROUTES (Kasir & Admin dapat mengakses)
Route::middleware(['auth', 'role:kasir,admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('dashboard');

    // Mesin Kasir / Point of Sale (POS)
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');

    // Riwayat Penjualan Kasir
    Route::get('/transactions', [TransactionHistoryController::class, 'index'])->name('transactions.index');
});
