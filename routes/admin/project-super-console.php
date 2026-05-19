<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ProjectReviewWebController;
use App\Http\Controllers\Admin\ProjectScheduleAdminController;
use Illuminate\Support\Facades\Route;

/**
 * Outils plateforme : planification Laravel (cron BDD), rapports `project:review` — super_admin interactif uniquement.
 */
Route::prefix('admin/project-schedule')
    ->name('admin.project-schedule.')
    ->middleware(['auth', 'role:super_admin'])
    ->group(function () {
        Route::get('/', [ProjectScheduleAdminController::class, 'index'])->name('index');
        Route::patch('/tasks/{project_schedule_task}', [ProjectScheduleAdminController::class, 'update'])
            ->middleware(['throttle:60,1'])
            ->name('tasks.update');
    });

Route::prefix('admin/project-review')
    ->name('admin.project-review.')
    ->middleware(['auth', 'role:super_admin'])
    ->group(function () {
        Route::get('/', [ProjectReviewWebController::class, 'index'])->name('index');
        Route::get('/report/{report}', [ProjectReviewWebController::class, 'download'])
            ->where('report', '[A-Za-z0-9_\-\.]+\.md')
            ->name('download');
        Route::post('/run', [ProjectReviewWebController::class, 'store'])
            ->middleware(['password.confirm', 'throttle:3,60'])
            ->name('run');
    });
