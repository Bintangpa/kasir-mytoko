<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DuitkuWebhookController;
use App\Http\Controllers\Kasir\KasirDashboardController;
use App\Http\Controllers\Kasir\PosController;
use App\Http\Controllers\Kasir\TransactionHistoryController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\TransactionReceiptController;
use App\Http\Controllers\User\UserShopController;
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
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()->isKasir()) {
            return redirect()->route('kasir.dashboard');
        }
        return redirect()->route('user.products.index');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Shared Auth Routes (Admin, Kasir & User - validasi kepemilikan di controller)
Route::middleware(['auth'])->group(function () {
    Route::get('/transactions/{transaction}/receipt', [TransactionReceiptController::class, 'show'])->name('transactions.receipt');
    Route::get('/products/{product}/image', [ProductController::class, 'image'])->name('products.image');
});

// Receipt Printer Settings (Khusus Admin & Kasir)
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
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

// USER / PENGGUNA ROUTES (Hanya melihat produk & melakukan pembayaran)
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/products', [UserShopController::class, 'index'])->name('products.index');
    Route::get('/cart', [UserShopController::class, 'cart'])->name('cart');
    Route::post('/checkout', [UserShopController::class, 'checkout'])->name('checkout');
    Route::get('/transactions', [UserShopController::class, 'transactions'])->name('transactions.index');
    Route::post('/transactions/{transaction}/check-status', [UserShopController::class, 'checkPaymentStatus'])->name('transactions.check-status');
});

// DUITKU PAYMENT GATEWAY WEBHOOK NOTIFICATION ENDPOINT (Tanpa auth/CSRF, dilindungi MD5 Signature)
Route::post('/webhooks/duitku', [DuitkuWebhookController::class, 'handle'])->name('webhooks.duitku');
Route::post('/webhooks/midtrans', [MidtransWebhookController::class, 'handle'])->name('webhooks.midtrans');
