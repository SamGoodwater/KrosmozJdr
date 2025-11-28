<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\SpecializationController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/specializations')->name('entities.specializations.')->group(function () {
    Route::get('/', [SpecializationController::class, 'index'])->name('index');
    Route::get('/{specialization}', [SpecializationController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/specializations')->name('entities.specializations.')->middleware('auth')->group(function () {
    Route::get('/create', [SpecializationController::class, 'create'])->name('create');
    Route::post('/', [SpecializationController::class, 'store'])->name('store');
    Route::get('/{specialization}/edit', [SpecializationController::class, 'edit'])->name('edit');
    Route::patch('/{specialization}', [SpecializationController::class, 'update'])->name('update');
    Route::delete('/{specialization}', [SpecializationController::class, 'delete'])->name('delete');
});
