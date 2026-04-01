<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ProjectMaintenanceController;
use Illuminate\Support\Facades\Route;

/**
 * Maintenance projet (super admin uniquement) : sync données DofusDB via file d’attente.
 *
 * Comme `/scrapping` : la page GET est servie sans `password.confirm` ; la porte d’accès UI
 * utilise `ConfirmPasswordModal` + `user.password.confirm` (session). Le POST `/sync` reste
 * protégé par `password.confirm` + throttle (équivalent API scrapping).
 */
Route::prefix('admin/project-maintenance')
    ->name('admin.project-maintenance.')
    ->middleware(['auth', 'role:super_admin'])
    ->group(function () {
        Route::get('/', [ProjectMaintenanceController::class, 'index'])->name('index');
        Route::post('/sync', [ProjectMaintenanceController::class, 'store'])
            ->middleware(['password.confirm', 'throttle:6,1'])
            ->name('sync');
    });
