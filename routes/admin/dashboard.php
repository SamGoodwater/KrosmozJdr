<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminRecapController;
use App\Http\Controllers\Admin\FeedbackThreadController;
use App\Http\Controllers\Admin\ProjectBackupWebController;
use App\Http\Controllers\Admin\ProjectClearWebController;
use App\Http\Controllers\Admin\ProjectDepsWebController;
use App\Http\Controllers\Admin\ProjectOrphanFilesWebController;
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

        Route::get('/activity-log', [AdminActivityLogController::class, 'index'])
            ->middleware(['admin.area', 'password.confirm'])
            ->name('activity-log.index');

        Route::get('/feedback', [FeedbackThreadController::class, 'index'])
            ->middleware(['admin.area', 'password.confirm'])
            ->name('feedback.index');
        Route::get('/feedback/{feedback}', [FeedbackThreadController::class, 'show'])
            ->middleware(['admin.area', 'password.confirm'])
            ->name('feedback.show');
        Route::post('/feedback/{feedback}/reply', [FeedbackThreadController::class, 'reply'])
            ->middleware(['admin.area', 'password.confirm', 'throttle:12,1'])
            ->name('feedback.reply');
        Route::patch('/feedback/{feedback}/status', [FeedbackThreadController::class, 'updateStatus'])
            ->middleware(['admin.area', 'password.confirm', 'throttle:12,1'])
            ->name('feedback.status');
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

Route::prefix('admin/orphan-files')
    ->name('admin.orphan-files.')
    ->middleware(['auth', 'role:super_admin'])
    ->group(function () {
        Route::get('/', [ProjectOrphanFilesWebController::class, 'index'])->name('index');
        Route::post('/run', [ProjectOrphanFilesWebController::class, 'store'])
            ->middleware(['password.confirm', 'throttle:6,1'])
            ->name('run');
        Route::get('/jobs/{jobId}', [ProjectOrphanFilesWebController::class, 'status'])
            ->name('status');
        Route::post('/jobs/{jobId}/cancel', [ProjectOrphanFilesWebController::class, 'cancel'])
            ->middleware(['password.confirm', 'throttle:12,1'])
            ->name('cancel');
    });

Route::prefix('admin/project-clear')
    ->name('admin.project-clear.')
    ->middleware(['auth', 'role:super_admin'])
    ->group(function () {
        Route::get('/', [ProjectClearWebController::class, 'index'])->name('index');
        Route::post('/run', [ProjectClearWebController::class, 'store'])
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
