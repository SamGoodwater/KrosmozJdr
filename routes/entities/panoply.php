<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\PanoplyController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/panoplies')->name('entities.panoplies.')->group(function () {
    Route::get('/', [PanoplyController::class, 'index'])->name('index');
    Route::get('/{panoply}', [PanoplyController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/panoplies')->name('entities.panoplies.')->middleware('auth')->group(function () {
    Route::get('/create', [PanoplyController::class, 'create'])->name('create');
    Route::post('/', [PanoplyController::class, 'store'])->name('store');
    Route::get('/{panoply}/edit', [PanoplyController::class, 'edit'])->name('edit');
    // Route spécifique pour les items (doit être avant la route update générique)
    Route::patch('/{panoply}/items', [PanoplyController::class, 'updateItems'])->name('updateItems');
    Route::patch('/{panoply}', [PanoplyController::class, 'update'])->name('update');
    Route::delete('/{panoply}', [PanoplyController::class, 'delete'])->name('delete');
});
