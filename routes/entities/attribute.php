<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\AttributeController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/attributes')->name('entities.attributes.')->group(function () {
    Route::get('/', [AttributeController::class, 'index'])->name('index');
    Route::get('/{attribute}', [AttributeController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/attributes')->name('entities.attributes.')->middleware('auth')->group(function () {
    Route::get('/create', [AttributeController::class, 'create'])->name('create');
    Route::post('/', [AttributeController::class, 'store'])->name('store');
    Route::get('/{attribute}/edit', [AttributeController::class, 'edit'])->name('edit');
    Route::get('/{attribute}/pdf', [AttributeController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{attribute}', [AttributeController::class, 'update'])->name('update');
    Route::delete('/{attribute}', [AttributeController::class, 'delete'])->name('delete');
});

