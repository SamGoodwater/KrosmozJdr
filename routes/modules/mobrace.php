<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\MobraceController;

Route::prefix('mobrace')->name("mobrace.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [MobraceController::class, 'index'])->name('index');
    Route::get('/{mobrace:uniqid}', [MobraceController::class, 'show'])
        ->name('show')
        ->where('mobrace', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [MobraceController::class, 'create'])->name('create');
        Route::post('/', [MobraceController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{mobrace:uniqid}/edit', [MobraceController::class, 'edit'])
            ->name('edit')
            ->where('mobrace', $uniqidRegex);
        Route::patch('/{mobrace:uniqid}', [MobraceController::class, 'update'])
            ->name('update')
            ->where('mobrace', $uniqidRegex);
        Route::delete('/{mobrace:uniqid}', [MobraceController::class, 'delete'])
            ->name('delete')
            ->where('mobrace', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{mobrace:uniqid}', [MobraceController::class, 'restore'])
            ->name('restore')
            ->where(
                'mobrace',
                $uniqidRegex
            );
        Route::delete(
            '/forcedDelete/{mobrace:uniqid}',
            [MobraceController::class, 'forcedDelete']
        )
            ->name('forcedDelete')
            ->where('mobrace', $uniqidRegex);
    });
});
