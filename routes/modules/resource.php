<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ResourceController;

Route::prefix('resource')->name("resource.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [ResourceController::class, 'index'])->name('index');
    Route::get('/{resource:uniqid}', [ResourceController::class, 'show'])
        ->name('show')
        ->where('resource', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [ResourceController::class, 'create'])->name('create');
        Route::post('/', [ResourceController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{resource:uniqid}/edit', [ResourceController::class, 'edit'])
            ->name('edit')
            ->where('resource', $uniqidRegex);
        Route::patch('/{resource:uniqid}', [ResourceController::class, 'update'])
            ->name('update')
            ->where('resource', $uniqidRegex);
        Route::delete('/{resource:uniqid}', [ResourceController::class, 'delete'])
            ->name('delete')
            ->where('resource', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{resource:uniqid}', [
            ResourceController::class,
            'restore'
        ])
            ->name('restore')
            ->where(
                'resource',
                $uniqidRegex
            );
        Route::delete(
            '/forcedDelete/{resource:uniqid}',
            [ResourceController::class, 'forcedDelete']
        )
            ->name('forcedDelete')
            ->where('resource', $uniqidRegex);
    });
});
