<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\AttributeController;

Route::prefix('attribute')->name("attribute.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [AttributeController::class, 'index'])->name('index');
    Route::get('/{attribute:uniqid}', [AttributeController::class, 'show'])
    ->name('show')
    ->where('attribute', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [AttributeController::class, 'create'])->name('create');
        Route::post('/', [AttributeController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum (inclut aussi game_master et super_admin)
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{attribute:uniqid}/edit', [AttributeController::class, 'edit'])
        ->name('edit')
        ->where('attribute', $uniqidRegex);
        Route::patch('/{attribute:uniqid}', [AttributeController::class, 'update'])
        ->name('update')
        ->where('attribute', $uniqidRegex);
        Route::delete('/{attribute:uniqid}', [AttributeController::class, 'delete'])
        ->name('delete')
        ->where('attribute', $uniqidRegex);
    });

    // Ces routes sont définies dans la policy comme retournant false
    // Seul le super_admin y aura accès (géré automatiquement par le middleware role)
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{attribute:uniqid}', [AttributeController::class, 'restore'])
        ->name('restore')
        ->where('attribute', $uniqidRegex);
        Route::delete('/forcedDelete/{attribute:uniqid}', [AttributeController::class, 'forcedDelete'])
        ->name('forcedDelete')
        ->where('attribute',
            $uniqidRegex
        );
    });
});
