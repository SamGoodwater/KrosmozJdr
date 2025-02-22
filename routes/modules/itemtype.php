<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ItemtypeController;

Route::prefix('itemtype')->name("itemtype.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [ItemtypeController::class, 'index'])->name('index');
    Route::get('/{itemtype:uniqid}', [ItemtypeController::class, 'show'])
    ->name('show')
    ->where('itemtype', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [ItemtypeController::class, 'create'])->name('create');
        Route::post('/', [ItemtypeController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{itemtype:uniqid}/edit', [ItemtypeController::class, 'edit'])
        ->name('edit')
        ->where('itemtype', $uniqidRegex);
        Route::patch('/{itemtype:uniqid}', [ItemtypeController::class, 'update'])
        ->name('update')
        ->where('itemtype', $uniqidRegex);
        Route::delete('/{itemtype:uniqid}', [ItemtypeController::class, 'delete'])
        ->name('delete')
        ->where('itemtype', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{itemtype:uniqid}', [ItemtypeController::class, 'restore'])
        ->name('restore')
        ->where(
            'itemtype',
            $uniqidRegex
        );
        Route::delete(
            '/forcedDelete/{itemtype:uniqid}',
            [ItemtypeController::class, 'forcedDelete']
        )
        ->name('forcedDelete')
        ->where('itemtype', $uniqidRegex);
    });
});
