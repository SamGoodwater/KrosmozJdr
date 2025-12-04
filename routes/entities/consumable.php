<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\ConsumableController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/consumables')->name('entities.consumables.')->group(function () {
    Route::get('/', [ConsumableController::class, 'index'])->name('index');
    Route::get('/{consumable}', [ConsumableController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/consumables')->name('entities.consumables.')->middleware('auth')->group(function () {
    Route::get('/create', [ConsumableController::class, 'create'])->name('create');
    Route::post('/', [ConsumableController::class, 'store'])->name('store');
    Route::get('/{consumable}/edit', [ConsumableController::class, 'edit'])->name('edit');
    Route::get('/{consumable}/pdf', [ConsumableController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{consumable}', [ConsumableController::class, 'update'])->name('update');
    Route::delete('/{consumable}', [ConsumableController::class, 'delete'])->name('delete');
});
