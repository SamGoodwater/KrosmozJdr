<?php

declare(strict_types=1);

use App\Http\Controllers\Api\GlobalSearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Recherche globale (middleware web pour session utilisateur / Gate)
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'throttle:90,1'])
    ->get('/global-search', GlobalSearchController::class)
    ->name('api.global-search');
