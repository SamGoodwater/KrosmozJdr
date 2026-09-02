<?php

declare(strict_types=1);

use App\Http\Controllers\Api\GameDownloadCatalogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Catalogue des téléchargements (livre, fiches, logo)
|--------------------------------------------------------------------------
*/

Route::middleware(['web'])->prefix('game-downloads')->group(function () {
    Route::get('/', [GameDownloadCatalogController::class, 'index'])
        ->name('api.game-downloads.index');
});
