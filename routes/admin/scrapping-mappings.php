<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ScrappingMappingController;
use Illuminate\Support\Facades\Route;

/**
 * Administration du mapping scrapping (DofusDB → Krosmoz) par entité.
 *
 * Mutations : password.confirm.
 */
Route::prefix('admin/scrapping-mappings')
    ->name('admin.scrapping-mappings.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [ScrappingMappingController::class, 'index'])->name('index');

        Route::middleware(['password.confirm'])->group(function () {
            Route::post('/', [ScrappingMappingController::class, 'store'])->name('store');
            Route::patch('/{id}', [ScrappingMappingController::class, 'update'])->name('update');
            Route::delete('/{id}', [ScrappingMappingController::class, 'destroy'])->name('destroy');
        });
    });
