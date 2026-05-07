<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\ConditionController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/conditions')->name('entities.conditions.')->group(function () {
    Route::get('/', [ConditionController::class, 'index'])->name('index');
    Route::get('/{condition}', [ConditionController::class, 'show'])->whereNumber('condition')->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/conditions')->name('entities.conditions.')->middleware('auth')->group(function () {
    Route::get('/create', [ConditionController::class, 'create'])->name('create');
    Route::post('/', [ConditionController::class, 'store'])->name('store');
    Route::get('/{condition}/edit', [ConditionController::class, 'edit'])->name('edit');
    Route::get('/{condition}/pdf', [ConditionController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{condition}', [ConditionController::class, 'update'])->name('update');
    Route::delete('/{condition}', [ConditionController::class, 'delete'])->name('delete');
});

