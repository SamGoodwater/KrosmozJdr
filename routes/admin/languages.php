<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\LanguageController;
use Illuminate\Support\Facades\Route;

/**
 * Référentiel des langues (contenu de jeu, MJ+).
 * Zone contenu : content.area + password.confirm.
 */
Route::prefix('admin/content/languages')
    ->name('admin.languages.')
    ->middleware(['auth', 'role:game_master', 'content.area', 'password.confirm'])
    ->group(function () {
        Route::get('/', [LanguageController::class, 'index'])->name('index');
        Route::post('/', [LanguageController::class, 'store'])->name('store');
        Route::patch('/{language}', [LanguageController::class, 'update'])->name('update');
        Route::delete('/{language}', [LanguageController::class, 'destroy'])->name('destroy');
    });
