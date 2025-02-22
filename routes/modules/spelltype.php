<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SpelltypeController;

Route::prefix('spelltype')->name("spelltype.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [SpelltypeController::class, 'index'])->name('index');
    Route::get('/{spelltype:uniqid}', [SpelltypeController::class, 'show'])
    ->name('show')
    ->where('spelltype', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [SpelltypeController::class, 'create'])->name('create');
        Route::post('/', [SpelltypeController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{spelltype:uniqid}/edit', [
            SpelltypeController::class,
            'edit'
        ])
        ->name('edit')
            ->where('spelltype', $uniqidRegex);
        Route::patch('/{spelltype:uniqid}', [SpelltypeController::class, 'update'])
        ->name('update')
        ->where('spelltype', $uniqidRegex);
        Route::delete('/{spelltype:uniqid}', [SpelltypeController::class, 'delete'])
        ->name('delete')
        ->where('spelltype', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{spelltype:uniqid}', [
            SpelltypeController::class,
            'restore'
        ])
        ->name('restore')
            ->where(
                'spelltype',
                $uniqidRegex
            );
        Route::delete(
            '/forcedDelete/{spelltype:uniqid}',
            [SpelltypeController::class, 'forcedDelete']
        )
            ->name('forcedDelete')
            ->where('spelltype',
                $uniqidRegex
            );
    });
});
