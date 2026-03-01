<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DofusdbEffectMappingController;
use Illuminate\Support\Facades\Route;

/**
 * Administration des mappings effectId DofusDB → sous-effet Krosmoz (effets de sorts).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 */
Route::prefix('admin/dofusdb-effect-mappings')
    ->name('admin.dofusdb-effect-mappings.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [DofusdbEffectMappingController::class, 'index'])->name('index');
        Route::post('/', [DofusdbEffectMappingController::class, 'store'])->name('store');
        Route::patch('/{mapping}', [DofusdbEffectMappingController::class, 'update'])->name('update');
        Route::delete('/{mapping}', [DofusdbEffectMappingController::class, 'destroy'])->name('destroy');
    });
