<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ContentDofusdbWorkshopController;
use App\Http\Controllers\Admin\ContentManagementDashboardController;
use Illuminate\Support\Facades\Route;

/**
 * Gestion du contenu : vue d’ensemble + atelier DofusDB (admin+).
 */
Route::prefix('admin/content')
    ->name('admin.content.')
    ->middleware(['auth', 'content.area'])
    ->group(function () {
        Route::get('/', ContentManagementDashboardController::class)->name('dashboard.index');
        Route::get('/dofusdb', [ContentDofusdbWorkshopController::class, 'index'])->name('dofusdb.index');
        Route::post('/dofusdb/sync', [ContentDofusdbWorkshopController::class, 'sync'])
            ->middleware(['password.confirm', 'throttle:6,1'])
            ->name('dofusdb.sync');
    });
