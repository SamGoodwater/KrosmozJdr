<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\LoadingTipController;
use Illuminate\Support\Facades\Route;

/**
 * Astuces de l’écran de chargement (UX app) — admin+, zone administration.
 */
Route::prefix('admin/loading-tips')
    ->name('admin.loading-tips.')
    ->middleware(['auth', 'role:admin', 'admin.area', 'password.confirm'])
    ->group(function () {
        Route::get('/', [LoadingTipController::class, 'index'])->name('index');
        Route::post('/', [LoadingTipController::class, 'store'])->name('store');
        Route::patch('/{loading_tip}', [LoadingTipController::class, 'update'])->name('update');
        Route::delete('/{loading_tip}', [LoadingTipController::class, 'destroy'])->name('destroy');
    });
