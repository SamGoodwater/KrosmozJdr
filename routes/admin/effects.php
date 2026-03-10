<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\EffectController as AdminEffectController;
use App\Http\Controllers\Admin\SubEffectController;
use Illuminate\Support\Facades\Route;

/**
 * Administration des effects et sous-effets (système unifié).
 * Effects : liste à gauche, panneau à droite ; duplication degré sur un effect.
 * Sub-effects : vue dédiée en lecture du référentiel.
 */
Route::get('admin/sub-effects', [SubEffectController::class, 'index'])
    ->name('admin.sub-effects.index')
    ->middleware(['auth', 'role:game_master']);

Route::prefix('admin/effects')
    ->name('admin.effects.')
    ->middleware(['auth', 'role:game_master'])
    ->group(function () {
        Route::get('/', [AdminEffectController::class, 'index'])->name('index');
        Route::get('/create', [AdminEffectController::class, 'create'])->name('create');
        Route::post('/', [AdminEffectController::class, 'store'])->name('store');
        Route::post('/{effect}/duplicate-degree', [AdminEffectController::class, 'duplicateDegree'])->name('duplicate-degree');
        Route::post('/{effect}/duplicate', [AdminEffectController::class, 'duplicate'])->name('duplicate');
        Route::get('/{effect}', [AdminEffectController::class, 'show'])->name('show');
        Route::patch('/{effect}', [AdminEffectController::class, 'update'])->name('update');
        Route::delete('/{effect}', [AdminEffectController::class, 'destroy'])->name('destroy');
    });
