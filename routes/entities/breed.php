<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\BreedController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/breeds')->name('entities.breeds.')->group(function () {
    Route::get('/', [BreedController::class, 'index'])->name('index');
    Route::get('/{breed}', [BreedController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/breeds')->name('entities.breeds.')->middleware('auth')->group(function () {
    Route::get('/create', [BreedController::class, 'create'])->name('create');
    Route::post('/', [BreedController::class, 'store'])->name('store');
    Route::get('/{breed}/edit', [BreedController::class, 'edit'])->name('edit');
    Route::get('/{breed}/pdf', [BreedController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{breed}', [BreedController::class, 'update'])->name('update');
    Route::delete('/{breed}', [BreedController::class, 'delete'])->name('delete');
});
