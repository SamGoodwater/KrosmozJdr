<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\ScenarioController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/scenarios')->name('entities.scenarios.')->group(function () {
    Route::get('/', [ScenarioController::class, 'index'])->name('index');
    Route::get('/{scenario}', [ScenarioController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/scenarios')->name('entities.scenarios.')->middleware('auth')->group(function () {
    Route::get('/create', [ScenarioController::class, 'create'])->name('create');
    Route::post('/', [ScenarioController::class, 'store'])->name('store');
    Route::get('/{scenario}/edit', [ScenarioController::class, 'edit'])->name('edit');
    // Routes spécifiques pour les relations (doivent être avant la route update générique)
    Route::patch('/{scenario}/items', [ScenarioController::class, 'updateItems'])->name('updateItems');
    Route::patch('/{scenario}/consumables', [ScenarioController::class, 'updateConsumables'])->name('updateConsumables');
    Route::patch('/{scenario}/resources', [ScenarioController::class, 'updateResources'])->name('updateResources');
    Route::patch('/{scenario}/spells', [ScenarioController::class, 'updateSpells'])->name('updateSpells');
    Route::patch('/{scenario}/panoplies', [ScenarioController::class, 'updatePanoplies'])->name('updatePanoplies');
    Route::patch('/{scenario}', [ScenarioController::class, 'update'])->name('update');
    Route::delete('/{scenario}', [ScenarioController::class, 'delete'])->name('delete');
});
