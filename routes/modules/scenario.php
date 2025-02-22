<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ScenarioController;

Route::prefix('scenario')->name("scenario.")->middleware('auth')->group(function () use ($uniqidRegex, $slugRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [ScenarioController::class, 'index'])->name('index');
    Route::get('/{scenario:slug}', [ScenarioController::class, 'show'])
    ->name('show')
    ->where('scenario', $slugRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex, $slugRegex) {
        Route::get('/create', [ScenarioController::class, 'create'])->name('create');
        Route::post('/', [ScenarioController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex, $slugRegex) {
        Route::get('/{scenario:slug}/edit', [
            ScenarioController::class,
            'edit'
        ])
        ->name('edit')
            ->where('scenario', $slugRegex);
        Route::patch('/{scenario:uniqid}', [
            ScenarioController::class,
            'update'
        ])
        ->name('update')
            ->where('scenario', $uniqidRegex);
        Route::delete('/{scenario:uniqid}', [
            ScenarioController::class,
            'delete'
        ])
        ->name('delete')
            ->where('scenario', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{scenario:uniqid}', [
            ScenarioController::class,
            'restore'
        ])
        ->name('restore')
            ->where(
                'scenario',
                $uniqidRegex
            );
        Route::delete(
            '/forcedDelete/{scenario:uniqid}',
            [ScenarioController::class, 'forcedDelete']
        )
        ->name('forcedDelete')
        ->where('scenario', $uniqidRegex);
    });
});
