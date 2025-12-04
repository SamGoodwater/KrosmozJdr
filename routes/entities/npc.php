<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\NpcController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/npcs')->name('entities.npcs.')->group(function () {
    Route::get('/', [NpcController::class, 'index'])->name('index');
    Route::get('/{npc}', [NpcController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/npcs')->name('entities.npcs.')->middleware('auth')->group(function () {
    Route::get('/create', [NpcController::class, 'create'])->name('create');
    Route::post('/', [NpcController::class, 'store'])->name('store');
    Route::get('/{npc}/edit', [NpcController::class, 'edit'])->name('edit');
    // Routes spécifiques pour les relations (doivent être avant la route update générique)
    Route::patch('/{npc}/panoplies', [NpcController::class, 'updatePanoplies'])->name('updatePanoplies');
    Route::patch('/{npc}/scenarios', [NpcController::class, 'updateScenarios'])->name('updateScenarios');
    Route::patch('/{npc}/campaigns', [NpcController::class, 'updateCampaigns'])->name('updateCampaigns');
    Route::get('/{npc}/pdf', [NpcController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{npc}', [NpcController::class, 'update'])->name('update');
    Route::delete('/{npc}', [NpcController::class, 'delete'])->name('delete');
});
