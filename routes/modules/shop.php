<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ShopController;

Route::prefix('shop')->name("shop.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/{shop:uniqid}', [ShopController::class, 'show'])
        ->name('show')
        ->where('shop', $uniqidRegex);

    // Routes nécessitant le rôle game_master minimum
    Route::middleware(['verified', 'role:game_master'])->group(function () use ($uniqidRegex) {
        Route::get('/create', [ShopController::class, 'create'])->name('create');
        Route::post('/', [ShopController::class, 'store'])->name('store');
    });

    // Routes nécessitant le rôle contributor minimum
    Route::middleware(['verified', 'role:contributor'])->group(function () use ($uniqidRegex) {
        Route::get('/{shop:uniqid}/edit', [ShopController::class, 'edit'])
        ->name('edit')
        ->where('shop', $uniqidRegex);
        Route::patch('/{shop:uniqid}', [ShopController::class, 'update'])
        ->name('update')
        ->where('shop', $uniqidRegex);
        Route::delete('/{shop:uniqid}', [ShopController::class, 'delete'])
        ->name('delete')
        ->where('shop', $uniqidRegex);
    });

    // Routes réservées aux super_admin
    Route::middleware(['verified', 'role:super_admin'])->group(function () use ($uniqidRegex) {
        Route::post('/{shop:uniqid}', [
            ShopController::class,
            'restore'
        ])
            ->name('restore')
            ->where(
                'shop',
                $uniqidRegex
            );
        Route::delete(
            '/forcedDelete/{shop:uniqid}',
            [ShopController::class, 'forcedDelete']
        )
        ->name('forcedDelete')
        ->where('shop', $uniqidRegex);
    });
});
