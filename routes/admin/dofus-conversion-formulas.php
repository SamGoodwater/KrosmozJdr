<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DofusConversionFormulaController;
use Illuminate\Support\Facades\Route;

/**
 * Aperçu des formules de conversion (page Caractéristiques) — MJ+.
 */
Route::prefix('admin/content/dofus-conversion-formulas')
    ->name('admin.dofus-conversion-formulas.')
    ->middleware(['auth', 'role:game_master', 'content.area', 'password.confirm'])
    ->group(function () {
        Route::get('/handlers', [DofusConversionFormulaController::class, 'handlers'])->name('handlers');
        Route::get('/formula-preview', [DofusConversionFormulaController::class, 'formulaPreview'])->name('formula-preview');
    });
