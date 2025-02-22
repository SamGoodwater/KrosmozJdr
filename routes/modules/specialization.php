<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SpecializationController;

Route::prefix('specialization')->name("specialization.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [SpecializationController::class, 'index'])->name('index');
    Route::get('/{specialization:uniqid}', [SpecializationController::class, 'show'])
    ->name('show')
    ->where('specialization', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [SpecializationController::class, 'create'])->name('create');
        Route::post('/', [SpecializationController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{specialization:uniqid}/edit', [SpecializationController::class, 'edit'])
        ->name('edit')
        ->where('specialization', $uniqidRegex);
        Route::patch('/{specialization:uniqid}', [SpecializationController::class, 'update'])
        ->name('update')
        ->where('specialization', $uniqidRegex);
        Route::delete('/{specialization:uniqid}', [SpecializationController::class, 'delete'])
        ->name('delete')
        ->where('specialization', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{specialization:uniqid}', [
            SpecializationController::class,
            'restore'
        ])
        ->name('restore')
            ->where(
                'specialization',
                $uniqidRegex
            );
        Route::delete(
            '/forcedDelete/{specialization:uniqid}',
            [SpecializationController::class, 'forcedDelete']
        )
        ->name('forcedDelete')
        ->where('specialization', $uniqidRegex);
    });
});
