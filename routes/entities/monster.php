<?php

use App\Http\Controllers\Entity\MonsterController;
use Illuminate\Support\Facades\Route;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/monsters')->name('entities.monsters.')->group(function () {
    Route::get('/', [MonsterController::class, 'index'])->name('index');
    Route::get('/{monster}', [MonsterController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/monsters')->name('entities.monsters.')->middleware('auth')->group(function () {
    Route::get('/create', [MonsterController::class, 'create'])->name('create');
    Route::post('/', [MonsterController::class, 'store'])->name('store');
    Route::get('/{monster}/edit', [MonsterController::class, 'edit'])->name('edit');
    // Routes spécifiques pour les relations (doivent être avant la route update générique)
    Route::patch('/{monster}/scenarios', [MonsterController::class, 'updateScenarios'])->name('updateScenarios');
    Route::patch('/{monster}/campaigns', [MonsterController::class, 'updateCampaigns'])->name('updateCampaigns');
    Route::patch('/{monster}/spell-invocations', [MonsterController::class, 'updateSpellInvocations'])->name('updateSpellInvocations');
    Route::patch('/{monster}/languages', [MonsterController::class, 'updateLanguages'])->name('updateLanguages');
    Route::get('/{monster}/pdf', [MonsterController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{monster}', [MonsterController::class, 'update'])->name('update');
    Route::delete('/{monster}', [MonsterController::class, 'delete'])->name('delete');
});
