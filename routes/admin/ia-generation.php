<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\IaGenerationConfigController;
use Illuminate\Support\Facades\Route;

/**
 * Réglages IA métier (champs figés, étalons). Admin + confirmation mot de passe.
 * Aller-retour seeders équipements : CLI uniquement (`items:seeder-export` / `project:backup`).
 */
Route::prefix('admin/content/ia-generation')
    ->name('admin.content.ia-generation.')
    ->middleware(['auth', 'role:admin', 'content.area', 'password.confirm'])
    ->group(function () {
        Route::get('/', [IaGenerationConfigController::class, 'edit'])->name('edit');
        Route::middleware(['throttle:12,1'])->group(function () {
            Route::put('/', [IaGenerationConfigController::class, 'update'])->name('update');
            Route::delete('/', [IaGenerationConfigController::class, 'destroy'])->name('destroy');
        });
    });
