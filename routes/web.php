<?php

use Illuminate\Support\Facades\Route;

// USER CONTROLLERS
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController; // 1. Pastikan CheckoutController di-import di sini

// ADMIN CONTROLLERS
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\PartnersController;
use App\Http\Controllers\Admin\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC / USER ROUTES ====================
Route::get('/', [HomeController::class, 'index'])->name('home');

// Detail Event
Route::get('/event-detail/{id}', [EventController::class, 'show'])->name('events.show');

// PERBAIKAN DI SINI: Diubah dari EventController menjadi CheckoutController
Route::get('/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');

// Mengarahkan rute tiket agar dinamis membaca id transaksi
Route::get('/ticket/{id}', [EventController::class, 'ticket'])->name('ticket');

// Pengalihan Login Global jika diakses tanpa prefix admin
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Rute Halaman Pembayaran & Sukses Midtrans
Route::get('/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');


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
        
    });
});