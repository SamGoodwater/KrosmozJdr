<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminRecapController;
use App\Http\Controllers\Admin\ProjectBackupWebController;
use App\Http\Controllers\Admin\ProjectDepsWebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

/**
 * Espace administration : récapitulatif (admin+), sauvegarde et mise à jour stack (super admin).
 */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', function (): RedirectResponse {
            $user = auth()->user();
            if ($user && $user->isAdmin()) {
                return redirect()->route('admin.recap.index');
            }
            if ($user && $user->isGameMaster()) {
                return redirect()->route('admin.content.dashboard.index');
            }

            abort(403);
        })->name('dashboard.index');

        Route::get('/recap', AdminRecapController::class)
            ->middleware(['admin.area', 'password.confirm'])
            ->name('recap.index');
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
