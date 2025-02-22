<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\CapabilityController;

Route::prefix('capability')->name("capability.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [CapabilityController::class, 'index'])->name('index');
    Route::get('/{capability:uniqid}', [CapabilityController::class, 'show'])
    ->name('show')
    ->where('capability', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [CapabilityController::class, 'create'])->name('create');
        Route::post('/', [CapabilityController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{capability:uniqid}/edit', [
            CapabilityController::class,
            'edit'
        ])
        ->name('edit')
        ->where(
            'capability',
            $uniqidRegex
        );
        Route::patch('/{capability:uniqid}', [
            CapabilityController::class,
            'update'
        ])
        ->name('update')
            ->where('capability', $uniqidRegex);
        Route::delete('/{capability:uniqid}', [
            CapabilityController::class,
            'delete'
        ])
        ->name('delete')
            ->where('capability', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{capability:uniqid}', [
            CapabilityController::class,
            'restore'
        ])
        ->name('restore')
            ->where(
                'capability',
                $uniqidRegex
            );
        Route::delete(
            '/forcedDelete/{capability:uniqid}',
            [CapabilityController::class, 'forcedDelete']
        )
            ->name('forcedDelete')
            ->where('capability', $uniqidRegex);
    });
});
