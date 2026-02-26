<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Effect\EffectController;
use App\Http\Controllers\Api\Effect\EffectUsageController;
use App\Http\Controllers\Api\Effect\SubEffectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Effects — sous-effets, effects, usages, effet pour entité + niveau
|--------------------------------------------------------------------------
| Lecture : accessible à tous (guest y compris sans connexion).
| Écriture : réservée aux game_master et au-dessus.
*/
Route::prefix('effects')->group(function () {
    // ——— Lecture (public / guest) ———
    Route::get('for-entity', [EffectController::class, 'forEntity'])->name('effects.for-entity');
    Route::get('usages', [EffectUsageController::class, 'index'])->name('effects.usages.index');
    Route::get('usages/{effect_usage}', [EffectUsageController::class, 'show'])->name('effects.usages.show');
    Route::get('sub-effects', [SubEffectController::class, 'index'])->name('effects.sub-effects.index');
    Route::get('sub-effects/{sub_effect}', [SubEffectController::class, 'show'])->name('effects.sub-effects.show');
    Route::get('effects', [EffectController::class, 'index'])->name('effects.effects.index');
    Route::get('effects/{effect}', [EffectController::class, 'show'])->name('effects.effects.show');

    // ——— Écriture (game_master) ———
    Route::middleware(['web', 'auth', 'role:game_master'])->group(function () {
        Route::post('sub-effects', [SubEffectController::class, 'store'])->name('effects.sub-effects.store');
        Route::match(['put', 'patch'], 'sub-effects/{sub_effect}', [SubEffectController::class, 'update'])->name('effects.sub-effects.update');
        Route::delete('sub-effects/{sub_effect}', [SubEffectController::class, 'destroy'])->name('effects.sub-effects.destroy');
        Route::post('effects', [EffectController::class, 'store'])->name('effects.effects.store');
        Route::match(['put', 'patch'], 'effects/{effect}', [EffectController::class, 'update'])->name('effects.effects.update');
        Route::delete('effects/{effect}', [EffectController::class, 'destroy'])->name('effects.effects.destroy');
        Route::post('usages', [EffectUsageController::class, 'store'])->name('effects.usages.store');
        Route::match(['put', 'patch'], 'usages/{effect_usage}', [EffectUsageController::class, 'update'])->name('effects.usages.update');
        Route::delete('usages/{effect_usage}', [EffectUsageController::class, 'destroy'])->name('effects.usages.destroy');
    });
});
