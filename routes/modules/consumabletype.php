<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ConsumabletypeController;

Route::prefix('consumabletype')->name("consumabletype.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [ConsumabletypeController::class, 'index'])->name('index');
    Route::get('/{consumabletype:uniqid}', [ConsumabletypeController::class, 'show'])
    ->name('show')
    ->where('consumabletype', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [ConsumabletypeController::class, 'create'])->name('create');
        Route::post('/', [ConsumabletypeController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{consumabletype:uniqid}/edit', [ConsumabletypeController::class, 'edit'])
        ->name('edit')
        ->where('consumabletype', $uniqidRegex);
        Route::patch('/{consumabletype:uniqid}', [ConsumabletypeController::class, 'update'])
        ->name('update')
        ->where('consumabletype', $uniqidRegex);
        Route::delete('/{consumabletype:uniqid}', [ConsumabletypeController::class, 'delete'])
        ->name('delete')
        ->where('consumabletype', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{consumabletype:uniqid}', [ConsumabletypeController::class, 'restore'])
        ->name('restore')
        ->where('consumabletype',
            $uniqidRegex
        );
        Route::delete('/forcedDelete/{consumabletype:uniqid}', [ConsumabletypeController::class, 'forcedDelete'])
        ->name('forcedDelete')
        ->where('consumabletype', $uniqidRegex);
    });
});
