<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\ShopController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/shops')->name('entities.shops.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/{shop}', [ShopController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/shops')->name('entities.shops.')->middleware('auth')->group(function () {
    Route::get('/create', [ShopController::class, 'create'])->name('create');
    Route::post('/', [ShopController::class, 'store'])->name('store');
    Route::get('/{shop}/edit', [ShopController::class, 'edit'])->name('edit');
    Route::patch('/{shop}', [ShopController::class, 'update'])->name('update');
    Route::delete('/{shop}', [ShopController::class, 'delete'])->name('delete');
});
