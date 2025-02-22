<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ResourcetypeController;

Route::prefix('resourcetype')->name("resourcetype.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [ResourcetypeController::class, 'index'])->name('index');
    Route::get('/{resourcetype:uniqid}', [ResourcetypeController::class, 'show'])
    ->name('show')
    ->where('resourcetype', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [ResourcetypeController::class, 'create'])->name('create');
        Route::post('/', [ResourcetypeController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{resourcetype:uniqid}/edit', [ResourcetypeController::class, 'edit'])
        ->name('edit')
        ->where('resourcetype', $uniqidRegex);
        Route::patch('/{resourcetype:uniqid}', [ResourcetypeController::class, 'update'])
        ->name('update')
        ->where('resourcetype', $uniqidRegex);
        Route::delete('/{resourcetype:uniqid}', [ResourcetypeController::class, 'delete'])
        ->name('delete')
        ->where('resourcetype', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{resourcetype:uniqid}', [
            ResourcetypeController::class,
            'restore'
        ])
        ->name('restore')
            ->where(
                'resourcetype',
                $uniqidRegex
            );
        Route::delete(
            '/forcedDelete/{resourcetype:uniqid}',
            [ResourcetypeController::class, 'forcedDelete']
        )
        ->name('forcedDelete')
        ->where('resourcetype', $uniqidRegex);
    });
});
