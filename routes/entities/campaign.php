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
    Route::patch('/{campaign}', [CampaignController::class, 'update'])->name('update');
    Route::delete('/{campaign}', [CampaignController::class, 'delete'])->name('delete');
});
