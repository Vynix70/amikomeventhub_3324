<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\TransactionController;

// USER
Route::get('/', [HomeController::class, 'index']);

Route::get('/event-detail', [EventController::class, 'show']);
Route::get('/checkout', [EventController::class, 'chekout'])->name('checkout');

// ADMIN
Route::redirect('/admin', '/admin/dashboard');

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('events', AdminEventController::class);

    Route::get('/categories', [CategoriesController::class, 'index'])
        ->name('categories');

    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions');
});