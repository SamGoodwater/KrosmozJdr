<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\SpellEffectTypeController;
use Illuminate\Support\Facades\Route;

/**
 * Administration des types d'effets de sort (référentiel).
 * Liste à gauche, panneau d'édition à droite.
 */
Route::prefix('admin/spell-effect-types')
    ->name('admin.spell-effect-types.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [SpellEffectTypeController::class, 'index'])->name('index');
        Route::get('/{spellEffectType}', [SpellEffectTypeController::class, 'show'])->name('show');
        Route::patch('/{spellEffectType}', [SpellEffectTypeController::class, 'update'])->name('update');
    });
