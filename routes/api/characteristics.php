<?php

use App\Http\Controllers\Api\CharacteristicController;
use App\Http\Controllers\Api\CharacteristicNormsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Caractéristiques (métadonnées pour le frontend)
|--------------------------------------------------------------------------
|
| Chargement au démarrage pour résolution icon, color, name, etc.
|
*/

Route::middleware(['web'])->get('/characteristics', [CharacteristicController::class, 'index'])
    ->name('api.characteristics.index');

Route::middleware(['web'])->get('/characteristics/{key}/norms/{entity?}', [CharacteristicNormsController::class, 'show'])
    ->name('api.characteristics.norms');
