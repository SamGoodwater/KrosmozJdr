<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ContentDofusdbWorkshopController;
use Illuminate\Support\Facades\Route;

/**
 * Ancienne page super-admin « Sync données » : GET redirigé vers l’atelier.
 * POST conservé (même contrôleur) pour les liens/tests existants.
 */
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/project-maintenance', function () {
        return redirect()->route('admin.content.dofusdb.index');
    })->name('admin.project-maintenance.index');

    Route::post('/admin/project-maintenance/sync', [ContentDofusdbWorkshopController::class, 'sync'])
        ->middleware(['content.area', 'password.confirm', 'throttle:6,1'])
        ->name('admin.project-maintenance.sync');
});
