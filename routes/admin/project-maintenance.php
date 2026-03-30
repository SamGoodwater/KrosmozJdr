<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ProjectMaintenanceController;
use Illuminate\Support\Facades\Route;

/**
 * Maintenance projet (super admin uniquement) : sync données DofusDB via file d’attente.
 *
 * `password.confirm` sur tout le groupe : zone sensible (RequirePasswordWithInactivity),
 * même comportement que les routes API scrapping.
 */
Route::prefix('admin/project-maintenance')
    ->name('admin.project-maintenance.')
    ->middleware(['auth', 'role:super_admin', 'password.confirm'])
    ->group(function () {
        Route::get('/', [ProjectMaintenanceController::class, 'index'])->name('index');
        Route::post('/sync', [ProjectMaintenanceController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('sync');
    });
