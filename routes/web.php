<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;

// Halaman Utama Kasir
Route::get('/', [PosController::class, 'index'])->name('pos.index');
Route::post('/checkout', [PosController::class, 'store']);

// History Transaksi
Route::get('/history', [PosController::class, 'history'])->name('orders.history');
Route::get('/history/{id}', [PosController::class, 'historyDetail'])->name('orders.detail');

// Group Khusus Manajemen Produk
Route::prefix('products')->name('products.')->group(function () {
    // Halaman List Produk (Tabel Admin)
    Route::get('/', [PosController::class, 'adminIndex'])->name('index');
    // Simpan Baru
    Route::post('/', [PosController::class, 'storeProduct'])->name('store');
    // Update (Edit)
    Route::put('/{id}', [PosController::class, 'updateProduct'])->name('update');
    // Hapus
    Route::delete('/{id}', [PosController::class, 'destroyProduct'])->name('destroy');
});
