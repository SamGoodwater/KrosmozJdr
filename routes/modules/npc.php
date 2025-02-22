<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\NpcController;

Route::prefix('npc')->name("npc.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [NpcController::class, 'index'])->name('index');
    Route::get('/{npc:uniqid}', [NpcController::class, 'show'])
    ->name('show')
    ->where('npc', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [NpcController::class, 'create'])->name('create');
        Route::post('/', [NpcController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{npc:uniqid}/edit', [NpcController::class, 'edit'])
        ->name('edit')
        ->where('npc', $uniqidRegex);
        Route::patch('/{npc:uniqid}', [NpcController::class, 'update'])
        ->name('update')
        ->where('npc', $uniqidRegex);
        Route::delete('/{npc:uniqid}', [NpcController::class, 'delete'])
        ->name('delete')
        ->where('npc', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{npc:uniqid}', [NpcController::class, 'restore'])
        ->name('restore')
        ->where(
            'npc',
            $uniqidRegex
        );
        Route::delete(
            '/forcedDelete/{npc:uniqid}',
            [NpcController::class, 'forcedDelete']
        )
        ->name('forcedDelete')
        ->where('npc', $uniqidRegex);
    });
});
