<?php

use App\Http\Controllers\Type\MonsterRaceTypeApiController;
use App\Http\Controllers\Type\SpellTypeApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Types internes (races monstres, types de sorts)
|--------------------------------------------------------------------------
|
| Administration : liste + flags `allow_scrap` / `show_in_catalog`.
|
*/

Route::middleware(['web', 'auth'])->prefix('types')->group(function () {
    Route::prefix('monster-races')->group(function () {
        Route::get('/', [MonsterRaceTypeApiController::class, 'index'])
            ->name('types.monster-races.index');
        Route::patch('/bulk', [MonsterRaceTypeApiController::class, 'bulkUpdate'])
            ->name('types.monster-races.bulk');
        Route::patch('/{monsterRace}/catalog', [MonsterRaceTypeApiController::class, 'updateCatalog'])
            ->name('types.monster-races.catalog');
        Route::delete('/{monsterRace}', [MonsterRaceTypeApiController::class, 'destroy'])
            ->name('types.monster-races.delete');
    });

    Route::prefix('spell-types')->group(function () {
        Route::get('/', [SpellTypeApiController::class, 'index'])
            ->name('types.spell-types.index');
        Route::patch('/bulk', [SpellTypeApiController::class, 'bulkUpdate'])
            ->name('types.spell-types.bulk');
        Route::patch('/{spellType}/catalog', [SpellTypeApiController::class, 'updateCatalog'])
            ->name('types.spell-types.catalog');
        Route::delete('/{spellType}', [SpellTypeApiController::class, 'destroy'])
            ->name('types.spell-types.delete');
    });
});
