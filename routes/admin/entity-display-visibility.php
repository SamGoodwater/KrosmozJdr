<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\EntityDisplayVisibilityController;
use Illuminate\Support\Facades\Route;

/**
 * Matrice visibilité des entités par état (admin).
 */
Route::prefix('admin/entity-display-visibility')
    ->name('admin.entity-display-visibility.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [EntityDisplayVisibilityController::class, 'index'])->name('index');
        Route::patch('/', [EntityDisplayVisibilityController::class, 'update'])->name('update');
    });
