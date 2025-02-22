<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\PanoplyController;

Route::prefix('panoply')->name("panoply.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [PanoplyController::class, 'index'])->name('index');
    Route::get('/{panoply:uniqid}', [PanoplyController::class, 'show'])
    ->name('show')
    ->where('panoply', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [PanoplyController::class, 'create'])->name('create');
        Route::post('/', [PanoplyController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{panoply:uniqid}/edit', [PanoplyController::class, 'edit'])
        ->name('edit')
        ->where('panoply', $uniqidRegex);
        Route::patch('/{panoply:uniqid}', [PanoplyController::class, 'update'])
        ->name('update')
        ->where('panoply', $uniqidRegex);
        Route::delete('/{panoply:uniqid}', [PanoplyController::class, 'delete'])
        ->name('delete')
        ->where('panoply', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{panoply:uniqid}', [PanoplyController::class,
            'restore'
        ])
        ->name('restore')
        ->where(
            'panoply',
            $uniqidRegex
        );
        Route::delete(
            '/forcedDelete/{panoply:uniqid}',
            [PanoplyController::class, 'forcedDelete']
        )
        ->name('forcedDelete')
        ->where('panoply', $uniqidRegex);
    });
});
