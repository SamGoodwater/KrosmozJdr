<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ConsumableController;

Route::prefix('consumable')->name("consumable.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [ConsumableController::class, 'index'])->name('index');
    Route::get('/{consumable:uniqid}', [ConsumableController::class, 'show'])
    ->name('show')
    ->where('consumable', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [ConsumableController::class, 'create'])->name('create');
        Route::post('/', [ConsumableController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{consumable:uniqid}/edit', [ConsumableController::class, 'edit'])
        ->name('edit')
        ->where('consumable', $uniqidRegex);
        Route::patch('/{consumable:uniqid}', [ConsumableController::class, 'update'])
        ->name('update')
        ->where('consumable', $uniqidRegex);
        Route::delete('/{consumable:uniqid}', [ConsumableController::class, 'delete'])
        ->name('delete')
        ->where('consumable', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{consumable:uniqid}', [ConsumableController::class, 'restore'])
        ->name('restore')
        ->where('consumable', $uniqidRegex);
        Route::delete('/forcedDelete/{consumable:uniqid}', [ConsumableController::class, 'forcedDelete'])
        ->name('forcedDelete')
        ->where('consumable', $uniqidRegex);
    });
});
