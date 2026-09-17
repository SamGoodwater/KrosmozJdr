<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DofusdbEffectMappingController;
use Illuminate\Support\Facades\Route;

/**
 * Administration des mappings effectId DofusDB → sous-effet Krosmoz (effets de sorts).
 *
 * @see docs/features/effects/README.md
 *
 * Mutations : password.confirm.
 */
Route::prefix('admin/content/dofusdb-effect-mappings')
    ->name('admin.dofusdb-effect-mappings.')
    ->middleware(['auth', 'role:admin', 'content.area'])
    ->group(function () {
        Route::get('/', [DofusdbEffectMappingController::class, 'index'])->name('index');

        Route::middleware(['password.confirm'])->group(function () {
            Route::post('/', [DofusdbEffectMappingController::class, 'store'])->name('store');
            Route::patch('/{mapping}', [DofusdbEffectMappingController::class, 'update'])->name('update');
            Route::delete('/{mapping}', [DofusdbEffectMappingController::class, 'destroy'])->name('destroy');
        });
    });
