<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\SpellController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/spells')->name('entities.spells.')->group(function () {
    Route::get('/', [SpellController::class, 'index'])->name('index');
    Route::get('/{spell}', [SpellController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/spells')->name('entities.spells.')->middleware('auth')->group(function () {
    Route::get('/create', [SpellController::class, 'create'])->name('create');
    Route::post('/', [SpellController::class, 'store'])->name('store');
    Route::get('/{spell}/edit', [SpellController::class, 'edit'])->name('edit');
    // Routes spécifiques pour les relations (doivent être avant la route update générique)
    Route::patch('/{spell}/classes', [SpellController::class, 'updateClasses'])->name('updateClasses');
    Route::patch('/{spell}/spell-types', [SpellController::class, 'updateSpellTypes'])->name('updateSpellTypes');
    Route::get('/{spell}/pdf', [SpellController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{spell}', [SpellController::class, 'update'])->name('update');
    Route::delete('/{spell}', [SpellController::class, 'delete'])->name('delete');
});
