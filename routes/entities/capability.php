<?php

use App\Http\Controllers\Entity\CapabilityController;
use Illuminate\Support\Facades\Route;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/capabilities')->name('entities.capabilities.')->group(function () {
    Route::get('/', [CapabilityController::class, 'index'])->name('index');
    Route::get('/{capability}', [CapabilityController::class, 'show'])->whereNumber('capability')->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/capabilities')->name('entities.capabilities.')->middleware('auth')->group(function () {
    Route::get('/create', [CapabilityController::class, 'create'])->name('create');
    Route::post('/', [CapabilityController::class, 'store'])->name('store');
    Route::get('/{capability}/edit', [CapabilityController::class, 'edit'])->name('edit');
    Route::get('/{capability}/edit-payload', [CapabilityController::class, 'editPayload'])->name('edit-payload');
    Route::get('/{capability}/pdf', [CapabilityController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{capability}/conditions', [CapabilityController::class, 'updateConditions'])->name('updateConditions');
    Route::patch('/{capability}', [CapabilityController::class, 'update'])->name('update');
    Route::delete('/{capability}', [CapabilityController::class, 'delete'])->name('delete');
});
