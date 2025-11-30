<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\CampaignController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/campaigns')->name('entities.campaigns.')->group(function () {
    Route::get('/', [CampaignController::class, 'index'])->name('index');
    Route::get('/{campaign}', [CampaignController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/campaigns')->name('entities.campaigns.')->middleware('auth')->group(function () {
    Route::get('/create', [CampaignController::class, 'create'])->name('create');
    Route::post('/', [CampaignController::class, 'store'])->name('store');
    Route::get('/{campaign}/edit', [CampaignController::class, 'edit'])->name('edit');
    // Routes spécifiques pour les relations (doivent être avant la route update générique)
    Route::patch('/{campaign}/users', [CampaignController::class, 'updateUsers'])->name('updateUsers');
    Route::patch('/{campaign}/scenarios', [CampaignController::class, 'updateScenarios'])->name('updateScenarios');
    Route::patch('/{campaign}/items', [CampaignController::class, 'updateItems'])->name('updateItems');
    Route::patch('/{campaign}/consumables', [CampaignController::class, 'updateConsumables'])->name('updateConsumables');
    Route::patch('/{campaign}/resources', [CampaignController::class, 'updateResources'])->name('updateResources');
    Route::patch('/{campaign}/spells', [CampaignController::class, 'updateSpells'])->name('updateSpells');
    Route::patch('/{campaign}/panoplies', [CampaignController::class, 'updatePanoplies'])->name('updatePanoplies');
    Route::patch('/{campaign}', [CampaignController::class, 'update'])->name('update');
    Route::delete('/{campaign}', [CampaignController::class, 'delete'])->name('delete');
});
