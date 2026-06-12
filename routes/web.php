<?php

use Illuminate\Support\Facades\Route;

// USER CONTROLLERS
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;

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
|
| Di sini Anda dapat mendaftarkan semua rute web untuk aplikasi Anda.
| Rute-rute ini dimuat oleh RouteServiceProvider dalam grup middleware "web".
|
*/

// ==================== PUBLIC / USER ROUTES ====================
Route::get('/', [HomeController::class, 'index'])->name('home');

// Tambahkan parameter {id} pada detail, checkout, dan store
Route::get('/event-detail/{id}', [EventController::class, 'show'])->name('events.show');
Route::get('/checkout/{id}', [EventController::class, 'checkout'])->name('checkout');
Route::post('/checkout/{id}', [EventController::class, 'store'])->name('checkout.store');

// Mengarahkan rute tiket agar dinamis membaca id transaksi
// SEBELUM: Route::get('/ticket', [EventController::class, 'ticket'])->name('ticket');
// UBAH MENJADI:
Route::get('/ticket/{id}', [EventController::class, 'ticket'])->name('ticket');
// Pengalihan Login Global jika diakses tanpa prefix admin
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');


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
        
        // Transaction Routes (Sudah disesuaikan dengan folder admin/transactions)
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        
    });
});