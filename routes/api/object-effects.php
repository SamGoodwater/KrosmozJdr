<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ObjectEffectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Effets d’objet (structurés, hors système sort / effect_degrees)
|--------------------------------------------------------------------------
| Lecture : liste par entité (guest si la fiche est `view`).
| Session `web` requise pour que le MJ voie les brouillons après un save.
| Écriture : game_master+ (aligné sur /api/effects/usages).
*/
Route::prefix('object-effects')->middleware(['web'])->group(function () {
    Route::get('/', [ObjectEffectController::class, 'index'])->name('api.object-effects.index');
    Route::middleware(['auth', 'role:game_master'])->group(function () {
        Route::post('/', [ObjectEffectController::class, 'store'])->name('api.object-effects.store');
        Route::patch('/{object_effect}', [ObjectEffectController::class, 'update'])->name('api.object-effects.update');
        Route::delete('/{object_effect}', [ObjectEffectController::class, 'destroy'])->name('api.object-effects.destroy');
    });
});
