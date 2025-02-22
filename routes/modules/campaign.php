<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\CampaignController;

Route::prefix('campaign')->name("campaign.")->middleware('auth')->group(function () use ($uniqidRegex, $slugRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [CampaignController::class, 'index'])->name('index');
    Route::get('/{campaign:slug}', [CampaignController::class, 'show'])
    ->name('show')
    ->where('campaign', $slugRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex, $slugRegex) {
        Route::get('/create', [CampaignController::class, 'create'])->name('create');
        Route::post('/', [CampaignController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex, $slugRegex) {
        Route::get('/{campaign:slug}/edit', [CampaignController::class, 'edit'])
        ->name('edit')
        ->where('campaign', $slugRegex);
        Route::patch('/{campaign:uniqid}', [CampaignController::class, 'update'])
        ->name('update')
        ->where('campaign', $uniqidRegex);
        Route::delete('/{campaign:uniqid}', [CampaignController::class, 'delete'])
        ->name('delete')
        ->where('campaign', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{campaign:uniqid}', [CampaignController::class, 'restore'])
        ->name('restore')
        ->where('campaign', $uniqidRegex);
        Route::delete('/forcedDelete/{campaign:uniqid}', [CampaignController::class, 'forcedDelete'])
        ->name('forcedDelete')
        ->where('campaign', $uniqidRegex);
    });
});
