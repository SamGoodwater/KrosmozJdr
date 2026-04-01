<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ProjectBackupWebController;
use App\Http\Controllers\Admin\ProjectDepsWebController;
use Illuminate\Support\Facades\Route;

/**
 * Espace administration : tableau de bord, sauvegarde et mise à jour stack (super admin pour actions sensibles).
 */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin.area'])
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard.index');
    });

Route::prefix('admin/backup')
    ->name('admin.backup.')
    ->middleware(['auth', 'role:super_admin'])
    ->group(function () {
        Route::get('/', [ProjectBackupWebController::class, 'index'])->name('index');
        Route::post('/run', [ProjectBackupWebController::class, 'store'])
            ->middleware(['password.confirm', 'throttle:6,1'])
            ->name('run');
    });

Route::prefix('admin/project-update')
    ->name('admin.project-update.')
    ->middleware(['auth', 'role:super_admin'])
    ->group(function () {
        Route::get('/', [ProjectDepsWebController::class, 'index'])->name('index');
        Route::post('/run', [ProjectDepsWebController::class, 'store'])
            ->middleware(['password.confirm', 'throttle:6,1'])
            ->name('run');
    });
