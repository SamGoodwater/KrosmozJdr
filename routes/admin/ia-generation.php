<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\IaGenerationConfigController;
use App\Http\Controllers\Admin\ItemSeederFilesController;
use Illuminate\Support\Facades\Route;

/**
 * Réglages IA métier (champs figés, étalons) et aller-retour des étalons d'équipement entre la base
 * et les fichiers JSON du seeder. Admin + confirmation mot de passe.
 */
Route::prefix('admin/content/ia-generation')
    ->name('admin.content.ia-generation.')
    ->middleware(['auth', 'role:admin', 'content.area'])
    ->group(function () {
        Route::get('/', [IaGenerationConfigController::class, 'edit'])->name('edit');
        Route::middleware(['password.confirm'])->group(function () {
            Route::put('/', [IaGenerationConfigController::class, 'update'])->name('update');
            Route::delete('/', [IaGenerationConfigController::class, 'destroy'])->name('destroy');
            Route::post('items-seeder/export', [ItemSeederFilesController::class, 'export'])
                ->middleware(['throttle:12,1'])
                ->name('items-seeder.export');
            Route::post('items-seeder/import', [ItemSeederFilesController::class, 'import'])
                ->middleware(['throttle:12,1'])
                ->name('items-seeder.import');
        });
    });
