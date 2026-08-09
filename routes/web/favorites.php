<?php

use App\Http\Controllers\FavoritePageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web — Favoris
|--------------------------------------------------------------------------
*/

Route::get('/favoris', [FavoritePageController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('favorites.index');
