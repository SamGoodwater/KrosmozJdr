<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\CapabilityController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/capabilities')->name('entities.capabilities.')->group(function () {
    Route::get('/', [CapabilityController::class, 'index'])->name('index');
    Route::get('/{capability}', [CapabilityController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/capabilities')->name('entities.capabilities.')->middleware('auth')->group(function () {
    Route::get('/create', [CapabilityController::class, 'create'])->name('create');
    Route::post('/', [CapabilityController::class, 'store'])->name('store');
    Route::get('/{capability}/edit', [CapabilityController::class, 'edit'])->name('edit');
    Route::patch('/{capability}', [CapabilityController::class, 'update'])->name('update');
    Route::delete('/{capability}', [CapabilityController::class, 'delete'])->name('delete');
});
