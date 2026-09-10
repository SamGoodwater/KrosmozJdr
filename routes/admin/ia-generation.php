<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\IaGenerationConfigController;
use Illuminate\Support\Facades\Route;

/**
 * Réglages IA métier (champs figés, étalons). Admin + confirmation mot de passe.
 */
Route::prefix('admin/content/ia-generation')
    ->name('admin.content.ia-generation.')
    ->middleware(['auth', 'role:admin', 'content.area'])
    ->group(function () {
        Route::get('/', [IaGenerationConfigController::class, 'edit'])->name('edit');
        Route::middleware(['password.confirm'])->group(function () {
            Route::put('/', [IaGenerationConfigController::class, 'update'])->name('update');
            Route::delete('/', [IaGenerationConfigController::class, 'destroy'])->name('destroy');
        });
    });
