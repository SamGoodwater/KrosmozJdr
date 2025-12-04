<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\ClasseController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/classes')->name('entities.classes.')->group(function () {
    Route::get('/', [ClasseController::class, 'index'])->name('index');
    Route::get('/{classe}', [ClasseController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/classes')->name('entities.classes.')->middleware('auth')->group(function () {
    Route::get('/create', [ClasseController::class, 'create'])->name('create');
    Route::post('/', [ClasseController::class, 'store'])->name('store');
    Route::get('/{classe}/edit', [ClasseController::class, 'edit'])->name('edit');
    Route::get('/{classe}/pdf', [ClasseController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{classe}', [ClasseController::class, 'update'])->name('update');
    Route::delete('/{classe}', [ClasseController::class, 'delete'])->name('delete');
});
