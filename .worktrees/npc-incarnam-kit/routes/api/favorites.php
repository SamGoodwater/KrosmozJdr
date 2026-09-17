<?php

use App\Http\Controllers\Api\UserFavoriteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Favoris utilisateur
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])->prefix('favorites')->name('api.favorites.')->group(function () {
    Route::get('/', [UserFavoriteController::class, 'index'])->name('index');
    Route::post('/', [UserFavoriteController::class, 'store'])->name('store');
    Route::delete('/', [UserFavoriteController::class, 'destroy'])->name('destroy');
});
