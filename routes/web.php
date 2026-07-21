<?php

use Illuminate\Support\Facades\Route;

// USER CONTROLLERS
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\MyTicketController; // <-- Import MyTicketController

// ADMIN CONTROLLERS
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\PartnersController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TenantApprovalController;

// TENANT CONTROLLERS
use App\Http\Controllers\Tenant\AuthController as TenantAuth;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboard;
use App\Http\Controllers\Tenant\EventController as TenantEventController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC / USER ROUTES ====================
Route::get('/', [HomeController::class, 'index'])->name('home');

// Google Authentication Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Logout Route untuk User Publik
Route::post('/logout', function() {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
})->name('logout');

// Detail Event
Route::get('/event-detail/{id}', [EventController::class, 'show'])->name('events.show');


// ==================== CHECKOUT & VOUCHER ROUTES ====================
// Rute voucher diletakkan tepat di atas rute parameter agar string statis tidak salah terbaca sebagai ID event
Route::post('/checkout/apply-voucher', [VoucherController::class, 'applyVoucher'])->name('checkout.apply-voucher');
Route::get('/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');


// Mengarahkan rute tiket agar dinamis membaca id transaksi
Route::get('/ticket/{id}', [EventController::class, 'ticket'])->name('ticket');

// Pengalihan Login Global jika diakses tanpa prefix admin
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Rute Halaman Pembayaran & Sukses Sisi Pembeli
Route::get('/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');

// Protected User Routes (Harus Login)
Route::middleware(['auth'])->group(function () {
    Route::post('/event/{event}/review', [ReviewController::class, 'store'])->name('review.store');
    
    // Rute Riwayat Tiket Saya
    Route::get('/my-tickets', [MyTicketController::class, 'index'])->name('my-tickets.index');
});


// ==================== MIDTRANS WEBHOOK CALLBACK ====================
// Rute ini ditaruh di luar group admin agar bisa diakses bebas oleh server Midtrans
Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handle'])->name('midtrans.callback');


// ==================== TENANT / HIMA ROUTES ====================
// Rute Guest Tenant (Hanya bisa diakses jika BELUM login sebagai Tenant)
Route::middleware(['guest:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/register', [TenantAuth::class, 'showRegister'])->name('register');
    Route::post('/register', [TenantAuth::class, 'register'])->name('register.store');
    Route::get('/login', [TenantAuth::class, 'showLogin'])->name('login');
    Route::post('/login', [TenantAuth::class, 'login'])->name('login.store');
});

// Rute Terproteksi Tenant (Wajib login sebagai Tenant)
Route::middleware(['auth:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', [TenantDashboard::class, 'index'])->name('dashboard');
    Route::post('/logout', [TenantAuth::class, 'logout'])->name('logout');
    
    // ROUTE CRUD EVENT UNTUK HIMA:
    Route::resource('events', TenantEventController::class);
});


// ==================== ADMIN ROUTES ====================
Route::redirect('/admin', '/admin/dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
    
    // --- Guest / Public Admin Routes (Auth) ---
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // --- Protected Admin Routes (Middleware) ---
    Route::middleware(['auth', 'admin'])->group(function () {
        
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Resource Routes
        Route::resource('events', AdminEventController::class);
        Route::resource('categories', CategoriesController::class)->except(['create', 'show', 'edit']);
        Route::resource('partners', PartnersController::class)->except(['create', 'show', 'edit']);
        
        // Transaction Routes
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        
        // --- RUTE PENGAWASAN KELAYAKAN TENANT ---
        // Halaman daftar kelayakan tenant
        Route::get('/tenants', [TenantApprovalController::class, 'index'])->name('tenants.index');
        // Aksi mengubah status kelayakan
        Route::patch('/tenants/{id}/status', [TenantApprovalController::class, 'updateStatus'])->name('tenants.update_status');
        
    });
});