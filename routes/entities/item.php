<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\ItemController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/items')->name('entities.items.')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('index');
    Route::get('/{item}', [ItemController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/items')->name('entities.items.')->middleware('auth')->group(function () {
    Route::get('/create', [ItemController::class, 'create'])->name('create');
    Route::post('/', [ItemController::class, 'store'])->name('store');
    Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('edit');
    Route::patch('/{item}', [ItemController::class, 'update'])->name('update');
    Route::delete('/{item}', [ItemController::class, 'delete'])->name('delete');
});
