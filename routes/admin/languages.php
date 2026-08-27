<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\LanguageController;
use Illuminate\Support\Facades\Route;

/**
 * Référentiel des langues (hors hub bibliothèques).
 * Auth + rôle admin uniquement : pas de middleware password.confirm (référentiel léger).
 */
Route::prefix('admin/languages')
    ->name('admin.languages.')
    ->middleware(['auth', 'role:admin', 'content.area'])
    ->group(function () {
        Route::get('/', [LanguageController::class, 'index'])->name('index');
        Route::post('/', [LanguageController::class, 'store'])->name('store');
        Route::patch('/{language}', [LanguageController::class, 'update'])->name('update');
        Route::delete('/{language}', [LanguageController::class, 'destroy'])->name('destroy');
    });
