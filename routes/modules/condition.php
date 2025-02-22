<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ConditionController;

Route::prefix('condition')->name("condition.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [ConditionController::class, 'index'])->name('index');
    Route::get('/{condition:uniqid}', [ConditionController::class, 'show'])
    ->name('show')
    ->where('condition', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [ConditionController::class, 'create'])->name('create');
        Route::post('/', [ConditionController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{condition:uniqid}/edit', [ConditionController::class, 'edit'])
        ->name('edit')
        ->where('condition', $uniqidRegex);
        Route::patch('/{condition:uniqid}', [ConditionController::class, 'update'])
        ->name('update')
        ->where('condition', $uniqidRegex);
        Route::delete('/{condition:uniqid}', [ConditionController::class, 'delete'])
        ->name('delete')
        ->where('condition', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{condition:uniqid}', [ConditionController::class, 'restore'])
        ->name('restore')
        ->where('condition', $uniqidRegex);
        Route::delete('/forcedDelete/{condition:uniqid}', [ConditionController::class, 'forcedDelete'])
        ->name('forcedDelete')
        ->where('condition',
            $uniqidRegex
        );
    });
});
