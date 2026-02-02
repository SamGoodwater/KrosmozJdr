<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\CharacteristicController;
use Illuminate\Support\Facades\Route;

/**
 * Administration des caractéristiques (admin et super_admin).
 * Liste à gauche, panneau d'édition à droite. Graphiques pour les champs formule.
 */
Route::prefix('admin/characteristics')
    ->name('admin.characteristics.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [CharacteristicController::class, 'index'])->name('index');
        Route::get('/formula-preview', [CharacteristicController::class, 'formulaPreview'])->name('formula-preview');
        Route::post('/upload-icon', [CharacteristicController::class, 'uploadIcon'])->name('upload-icon');
        Route::get('/{characteristic}', [CharacteristicController::class, 'show'])->name('show');
        Route::patch('/{characteristic}', [CharacteristicController::class, 'update'])->name('update');
    });
