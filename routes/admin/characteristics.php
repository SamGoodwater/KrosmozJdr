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
        Route::get('/create', [CharacteristicController::class, 'create'])->name('create');
        Route::post('/', [CharacteristicController::class, 'store'])->name('store');
        Route::get('/formula-preview', [CharacteristicController::class, 'formulaPreview'])->name('formula-preview');
        Route::post('/upload-icon', [CharacteristicController::class, 'uploadIcon'])->name('upload-icon');
        Route::get('/{characteristic_key}', [CharacteristicController::class, 'show'])->name('show')->where('characteristic_key', '[a-z0-9_]+');
        Route::patch('/{characteristic_key}', [CharacteristicController::class, 'update'])->name('update')->where('characteristic_key', '[a-z0-9_]+');
        Route::delete('/{characteristic_key}', [CharacteristicController::class, 'destroy'])->name('destroy')->where('characteristic_key', '[a-z0-9_]+');
    });
