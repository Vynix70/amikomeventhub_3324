<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\TransactionController;
use Illuminate\Support\Facades\Route;


//user

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/event-detail', [App\Http\Controllers\EventController::class, 'show']);
Route::get('/checkout', [App\Http\Controllers\EventController::class, 'chekout'])->name('checkout');



//admin
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function (){

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/events', [AdminEventController::class, 'index'])->name('events');
    Route::get('/categories', [App\Http\Controllers\Admin\CategoriesController::class, 'index'])->name('categories');
    Route::get('/transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions');
    
});