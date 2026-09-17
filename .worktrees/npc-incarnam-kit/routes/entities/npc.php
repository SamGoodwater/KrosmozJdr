<?php

use App\Http\Controllers\Entity\NpcController;
use Illuminate\Support\Facades\Route;

Route::prefix('entities/npcs')->name('entities.npcs.')->group(function () {
    Route::get('/', [NpcController::class, 'index'])->name('index');
    Route::get('/{npc}', [NpcController::class, 'show'])->name('show');
});

Route::prefix('entities/npcs')->name('entities.npcs.')->middleware('auth')->group(function () {
    Route::get('/create', [NpcController::class, 'create'])->name('create');
    Route::post('/', [NpcController::class, 'store'])->name('store');
    Route::get('/{npc}/edit', [NpcController::class, 'edit'])->name('edit');
    Route::patch('/{npc}/panoplies', [NpcController::class, 'updatePanoplies'])->name('updatePanoplies');
    Route::patch('/{npc}/scenarios', [NpcController::class, 'updateScenarios'])->name('updateScenarios');
    Route::patch('/{npc}/campaigns', [NpcController::class, 'updateCampaigns'])->name('updateCampaigns');
    Route::patch('/{npc}/languages', [NpcController::class, 'updateLanguages'])->name('updateLanguages');
    Route::patch('/{npc}/creature-traits', [NpcController::class, 'updateCreatureTraits'])->name('updateCreatureTraits');
    Route::patch('/{npc}/spells', [NpcController::class, 'updateSpells'])->name('updateSpells');
    Route::patch('/{npc}/items', [NpcController::class, 'updateItems'])->name('updateItems');
    Route::get('/{npc}/pdf', [NpcController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{npc}', [NpcController::class, 'update'])->name('update');
    Route::delete('/{npc}', [NpcController::class, 'delete'])->name('delete');
});
