<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Datasets pour tables (TanStack Table — mode client)
|--------------------------------------------------------------------------
|
| Endpoints pour charger un lot d'entités (tri/filtre/recherche/pagination
| côté frontend). Consommés par l'UI Inertia (session/cookies).
|
*/

Route::middleware(['web', 'auth'])->prefix('entity-table')->group(function () {
    Route::get('/resources', [App\Http\Controllers\Api\EntityTableDataController::class, 'resources'])
        ->name('api.entity-table.resources');
    Route::get('/resource-types', [App\Http\Controllers\Api\EntityTableDataController::class, 'resourceTypes'])
        ->name('api.entity-table.resource-types');
});
