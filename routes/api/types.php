<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Types internes (races monstres, types de sorts)
|--------------------------------------------------------------------------
|
| Administration : liste + bulk state, utilisés par l'UI (pages + modals).
|
*/

Route::middleware(['web', 'auth'])->prefix('types')->group(function () {
    Route::prefix('monster-races')->group(function () {
        Route::get('/', [App\Http\Controllers\Type\MonsterRaceTypeApiController::class, 'index'])
            ->name('types.monster-races.index');
        Route::patch('/bulk', [App\Http\Controllers\Type\MonsterRaceTypeApiController::class, 'bulkUpdate'])
            ->name('types.monster-races.bulk');
        Route::delete('/{monsterRace}', [App\Http\Controllers\Type\MonsterRaceTypeApiController::class, 'destroy'])
            ->name('types.monster-races.delete');
    });

    Route::prefix('spell-types')->group(function () {
        Route::get('/', [App\Http\Controllers\Type\SpellTypeApiController::class, 'index'])
            ->name('types.spell-types.index');
        Route::patch('/bulk', [App\Http\Controllers\Type\SpellTypeApiController::class, 'bulkUpdate'])
            ->name('types.spell-types.bulk');
        Route::delete('/{spellType}', [App\Http\Controllers\Type\SpellTypeApiController::class, 'destroy'])
            ->name('types.spell-types.delete');
    });
});
