<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ContentManagementDashboardController;
use Illuminate\Support\Facades\Route;

/**
 * Gestion du contenu : vue d’ensemble (admin+).
 */
Route::prefix('admin/content')
    ->name('admin.content.')
    ->middleware(['auth', 'content.area'])
    ->group(function () {
        Route::get('/', ContentManagementDashboardController::class)->name('dashboard.index');
    });
