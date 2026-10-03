<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DofusdbEffectMappingController;
use Illuminate\Support\Facades\Route;

/**
 * Mappings effectId DofusDB → sous-effet (pipeline contenu) — admin+.
 *
 * @see docs/features/effects/README.md
 */
Route::prefix('admin/content/dofusdb-effect-mappings')
    ->name('admin.dofusdb-effect-mappings.')
    ->middleware(['auth', 'role:admin', 'content.area', 'password.confirm'])
    ->group(function () {
        Route::get('/', [DofusdbEffectMappingController::class, 'index'])->name('index');
        Route::post('/', [DofusdbEffectMappingController::class, 'store'])->name('store');
        Route::patch('/{mapping}', [DofusdbEffectMappingController::class, 'update'])->name('update');
        Route::delete('/{mapping}', [DofusdbEffectMappingController::class, 'destroy'])->name('destroy');
    });
