<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SpellController;

Route::prefix('spell')->name("spell.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [SpellController::class, 'index'])->name('index');
    Route::get('/{spell:uniqid}', [SpellController::class, 'show'])
        ->name('show')
        ->where('spell', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [SpellController::class, 'create'])->name('create');
        Route::post('/', [SpellController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{spell:uniqid}/edit', [SpellController::class,
            'edit'
        ])
        ->name('edit')
        ->where('spell', $uniqidRegex);
        Route::patch('/{spell:uniqid}', [SpellController::class, 'update'])
        ->name('update')
        ->where('spell', $uniqidRegex);
        Route::delete('/{spell:uniqid}', [SpellController::class, 'delete'])
        ->name('delete')
        ->where('spell', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{spell:uniqid}', [
            SpellController::class,
            'restore'
        ])
        ->name('restore')
            ->where(
                'spell',
                $uniqidRegex
            );
        Route::delete(
            '/forcedDelete/{spell:uniqid}',
            [SpellController::class, 'forcedDelete']
        )
        ->name('forcedDelete')
        ->where('spell', $uniqidRegex);
    });
});
