<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\ResourceController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/resources')->name('entities.resources.')->group(function () {
    Route::get('/', [ResourceController::class, 'index'])->name('index');
    Route::get('/{resource}', [ResourceController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/resources')->name('entities.resources.')->middleware('auth')->group(function () {
    Route::get('/create', [ResourceController::class, 'create'])->name('create');
    Route::post('/', [ResourceController::class, 'store'])->name('store');
    Route::get('/{resource}/edit', [ResourceController::class, 'edit'])->name('edit');
    Route::get('/{resource}/pdf', [ResourceController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{resource}', [ResourceController::class, 'update'])->name('update');
    Route::delete('/{resource}', [ResourceController::class, 'delete'])->name('delete');
});
