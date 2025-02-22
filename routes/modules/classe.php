<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ClasseController;

Route::prefix('classe')->name("classe.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [ClasseController::class, 'index'])->name('index');
    Route::get('/{class:uniqid}', [ClasseController::class, 'show'])
    ->name('show')
    ->where('classe', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [ClasseController::class, 'create'])->name('create');
        Route::post('/', [ClasseController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{classe:uniqid}/edit', [ClasseController::class, 'edit'])
        ->name('edit')
        ->where('classe', $uniqidRegex);
        Route::patch('/{classe:uniqid}', [ClasseController::class, 'update'])
        ->name('update')
        ->where('classe', $uniqidRegex);
        Route::delete('/{classe:uniqid}', [ClasseController::class,
            'delete'
        ])
        ->name('delete')
        ->where('classe', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{classe:uniqid}', [ClasseController::class, 'restore'])
        ->name('restore')
        ->where('classe', $uniqidRegex);
        Route::delete('/forcedDelete/{classe:uniqid}', [ClasseController::class, 'forcedDelete'])
        ->name('forcedDelete')
        ->where('classe', $uniqidRegex);
    });
});
