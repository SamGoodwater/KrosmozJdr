<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\EffectController as AdminEffectController;
use App\Http\Controllers\Admin\SubEffectController as AdminSubEffectController;
use Illuminate\Support\Facades\Route;

/**
 * Administration des sous-effets et effects (système unifié).
 * Liste à gauche, panneau à droite ; duplication degré sur un effect.
 */
Route::prefix('admin/sub-effects')
    ->name('admin.sub-effects.')
    ->middleware(['auth', 'role:game_master'])
    ->group(function () {
        Route::get('/', [AdminSubEffectController::class, 'index'])->name('index');
        Route::get('/create', [AdminSubEffectController::class, 'create'])->name('create');
        Route::post('/', [AdminSubEffectController::class, 'store'])->name('store');
        Route::get('/{sub_effect}', [AdminSubEffectController::class, 'show'])->name('show');
        Route::patch('/{sub_effect}', [AdminSubEffectController::class, 'update'])->name('update');
        Route::delete('/{sub_effect}', [AdminSubEffectController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin/effects')
    ->name('admin.effects.')
    ->middleware(['auth', 'role:game_master'])
    ->group(function () {
        Route::get('/', [AdminEffectController::class, 'index'])->name('index');
        Route::get('/create', [AdminEffectController::class, 'create'])->name('create');
        Route::post('/', [AdminEffectController::class, 'store'])->name('store');
        Route::post('/{effect}/duplicate-degree', [AdminEffectController::class, 'duplicateDegree'])->name('duplicate-degree');
        Route::get('/{effect}', [AdminEffectController::class, 'show'])->name('show');
        Route::patch('/{effect}', [AdminEffectController::class, 'update'])->name('update');
        Route::delete('/{effect}', [AdminEffectController::class, 'destroy'])->name('destroy');
    });
