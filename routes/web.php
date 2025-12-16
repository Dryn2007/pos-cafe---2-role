<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AuthController;

// Auth (login only; no register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Shared area (auth only)
Route::middleware('auth')->group(function () {
    Route::get('/history', [PosController::class, 'history'])->name('orders.history');
    Route::get('/history/{id}', [PosController::class, 'historyDetail'])->name('orders.detail');
});

// Kasir area (POS)
Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('pos.index');
    Route::post('/checkout', [PosController::class, 'store'])->name('pos.checkout');
});

// Admin area
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/history', [PosController::class, 'history'])->name('orders.history');
    Route::get('/history/{id}', [PosController::class, 'historyDetail'])->name('orders.detail');

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [PosController::class, 'adminIndex'])->name('index');
        Route::post('/', [PosController::class, 'storeProduct'])->name('store');
        Route::put('/{id}', [PosController::class, 'updateProduct'])->name('update');
        Route::delete('/{id}', [PosController::class, 'destroyProduct'])->name('destroy');
    });
});
