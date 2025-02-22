<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\MobController;

Route::prefix('mob')->name("mob.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [MobController::class, 'index'])->name('index');
    Route::get('/{mob:uniqid}', [MobController::class, 'show'])
    ->name('show')
    ->where('mob', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [MobController::class, 'create'])->name('create');
        Route::post('/', [MobController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{mob:uniqid}/edit', [MobController::class, 'edit'])
        ->name('edit')
        ->where('mob', $uniqidRegex);
        Route::patch('/{mob:uniqid}', [MobController::class, 'update'])
        ->name('update')
        ->where('mob', $uniqidRegex);
        Route::delete('/{mob:uniqid}', [MobController::class, 'delete'])
        ->name('delete')
        ->where('mob', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{mob:uniqid}', [MobController::class, 'restore'])
        ->name('restore')
        ->where(
            'mob',
            $uniqidRegex
        );
        Route::delete(
            '/forcedDelete/{mob:uniqid}',
            [MobController::class, 'forcedDelete']
        )
        ->name('forcedDelete')
        ->where('mob', $uniqidRegex);
    });
});
