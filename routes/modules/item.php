<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ItemController;

Route::prefix('item')->name("item.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [ItemController::class, 'index'])->name('index');
    Route::get('/{item:uniqid}', [ItemController::class, 'show'])
        ->name('show')
        ->where('item', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [ItemController::class, 'create'])->name('create');
        Route::post('/', [ItemController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{item:uniqid}/edit', [ItemController::class, 'edit'])
        ->name('edit')
        ->where('item', $uniqidRegex);
        Route::patch('/{item:uniqid}', [ItemController::class, 'update'])
        ->name('update')
        ->where('item', $uniqidRegex);
        Route::delete('/{item:uniqid}', [ItemController::class, 'delete'])
        ->name('delete')
        ->where('item', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{item:uniqid}', [ItemController::class, 'restore'])
        ->name('restore')
        ->where(
            'item',
            $uniqidRegex
        );
        Route::delete(
            '/forcedDelete/{item:uniqid}',
            [ItemController::class, 'forcedDelete']
        )
        ->name('forcedDelete')
        ->where('item', $uniqidRegex);
    });
});
