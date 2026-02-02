<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DofusConversionFormulaController;
use Illuminate\Support\Facades\Route;

/**
 * API d'aperçu des formules de conversion DofusDB → KrosmozJDR.
 * Utilisée par Admin > Caractéristiques (graphiques). L'édition se fait dans la page Caractéristiques.
 */
Route::prefix('admin/dofus-conversion-formulas')
    ->name('admin.dofus-conversion-formulas.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/handlers', [DofusConversionFormulaController::class, 'handlers'])->name('handlers');
        Route::get('/formula-preview', [DofusConversionFormulaController::class, 'formulaPreview'])->name('formula-preview');
    });
