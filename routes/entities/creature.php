<?php

use App\Http\Controllers\Entity\CreatureController;
use Illuminate\Support\Facades\Route;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/creatures')->name('entities.creatures.')->group(function () {
    Route::get('/', [CreatureController::class, 'index'])->name('index');
    // Avant /{creature} pour éviter que « resolved-stats » soit pris pour un id.
    Route::get('/{creature}/resolved-stats', [CreatureController::class, 'resolvedStats'])->name('resolvedStats');
    Route::get('/{creature}', [CreatureController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/creatures')->name('entities.creatures.')->middleware('auth')->group(function () {
    Route::get('/create', [CreatureController::class, 'create'])->name('create');
    Route::post('/', [CreatureController::class, 'store'])->name('store');
    Route::get('/{creature}/edit', [CreatureController::class, 'edit'])->name('edit');
    // Routes spécifiques pour les relations (doivent être avant la route update générique)
    Route::patch('/{creature}/items', [CreatureController::class, 'updateItems'])->name('updateItems');
    Route::patch('/{creature}/resources', [CreatureController::class, 'updateResources'])->name('updateResources');
    Route::patch('/{creature}/consumables', [CreatureController::class, 'updateConsumables'])->name('updateConsumables');
    Route::patch('/{creature}/creature-traits', [CreatureController::class, 'updateCreatureTraits'])->name('updateCreatureTraits');
    Route::patch('/{creature}/spells', [CreatureController::class, 'updateSpells'])->name('updateSpells');
    Route::get('/{creature}/pdf', [CreatureController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{creature}', [CreatureController::class, 'update'])->name('update');
    Route::delete('/{creature}', [CreatureController::class, 'delete'])->name('delete');
});
