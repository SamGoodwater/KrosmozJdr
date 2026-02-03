<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\ResourceController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/resources')->name('entities.resources.')->group(function () {
    Route::get('/', [ResourceController::class, 'index'])->name('index');
    Route::get('/{resource}', [ResourceController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/resources')->name('entities.resources.')->middleware('auth')->group(function () {
    Route::get('/create', [ResourceController::class, 'create'])->name('create');
    Route::post('/', [ResourceController::class, 'store'])->name('store');
    Route::get('/{resource}/edit', [ResourceController::class, 'edit'])->name('edit');
    Route::get('/{resource}/pdf', [ResourceController::class, 'downloadPdf'])->name('pdf');

    // Gestion des pivots / relations
    Route::patch('/{resource}/recipe', [ResourceController::class, 'updateRecipe'])->name('updateRecipe');
    Route::patch('/{resource}/items', [ResourceController::class, 'updateItems'])->name('updateItems');
    Route::patch('/{resource}/consumables', [ResourceController::class, 'updateConsumables'])->name('updateConsumables');
    Route::patch('/{resource}/creatures', [ResourceController::class, 'updateCreatures'])->name('updateCreatures');
    Route::patch('/{resource}/shops', [ResourceController::class, 'updateShops'])->name('updateShops');
    Route::patch('/{resource}/scenarios', [ResourceController::class, 'updateScenarios'])->name('updateScenarios');
    Route::patch('/{resource}/campaigns', [ResourceController::class, 'updateCampaigns'])->name('updateCampaigns');

    Route::patch('/{resource}', [ResourceController::class, 'update'])->name('update');
    Route::delete('/{resource}', [ResourceController::class, 'delete'])->name('delete');
});
