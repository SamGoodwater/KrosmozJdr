<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Routes de test pour le système de Scrapping
|--------------------------------------------------------------------------
|
| Ces routes permettent de tester le service DataCollect
| sans passer par l'orchestrateur complet.
|
*/

Route::prefix('scrapping/test')->group(function () {
    // Test de la disponibilité de l'API DofusDB
    Route::get('/api', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testApi'])
        ->name('scrapping.test.api');
    
    // Test de collecte d'une classe spécifique
    Route::get('/class/{id}', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectClass'])
        ->name('scrapping.test.class')
        ->where('id', '[1-9]|1[0-9]'); // 1-19
    
    // Test de collecte d'un monstre spécifique
    Route::get('/monster/{id}', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectMonster'])
        ->name('scrapping.test.monster')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-4][0-9][0-9][0-9]|5000'); // 1-5000
    
    // Test de collecte d'un objet spécifique
    Route::get('/item/{id}', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectItem'])
        ->name('scrapping.test.item')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-2][0-9][0-9][0-9][0-9]|30000'); // 1-30000
    
    // Test de collecte d'un sort spécifique
    Route::get('/spell/{id}', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectSpell'])
        ->name('scrapping.test.spell')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-9][0-9][0-9][0-9][0-9]|20000'); // 1-20000
    
    // Test de collecte d'un effet spécifique
    Route::get('/effect/{id}', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectEffect'])
        ->name('scrapping.test.effect')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|1000'); // 1-1000
    
    // Test de collecte d'objets par type
    Route::get('/items-by-type', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectItemsByType'])
        ->name('scrapping.test.items-by-type');
    
    // Test de nettoyage du cache
    Route::post('/clear-cache', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testClearCache'])
        ->name('scrapping.test.clear-cache');
});

/*
|--------------------------------------------------------------------------
| Routes de production pour le système de Scrapping
|--------------------------------------------------------------------------
|
| Ces routes utilisent l'orchestrateur complet pour effectuer les imports
| (collecte → conversion → intégration) depuis DofusDB vers KrosmozJDR.
|
*/

Route::prefix('scrapping/import')->group(function () {
    // Import d'une classe
    Route::post('/class/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importClass'])
        ->name('scrapping.import.class')
        ->where('id', '[1-9]|1[0-9]'); // 1-19
    
    // Import d'un monstre
    Route::post('/monster/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importMonster'])
        ->name('scrapping.import.monster')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-4][0-9][0-9][0-9]|5000'); // 1-5000
    
    // Import d'un objet
    Route::post('/item/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importItem'])
        ->name('scrapping.import.item')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-2][0-9][0-9][0-9][0-9]|30000'); // 1-30000
    
    // Import d'un sort
    Route::post('/spell/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importSpell'])
        ->name('scrapping.import.spell')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-9][0-9][0-9][0-9][0-9]|20000'); // 1-20000
    
    // Import en lot
    Route::post('/batch', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importBatch'])
        ->name('scrapping.import.batch');
});
