<?php

use App\Http\Controllers\Scrapping\ConsumableTypeRegistryController;
use App\Http\Controllers\Scrapping\DataCollectController;
use App\Http\Controllers\Scrapping\DofusDbItemTypesCatalogController;
use App\Http\Controllers\Scrapping\DofusDbMonsterRacesCatalogController;
use App\Http\Controllers\Scrapping\ItemTypeRegistryController;
use App\Http\Controllers\Scrapping\ResourceTypeRegistryController;
use App\Http\Controllers\Scrapping\ScrappingConfigController;
use App\Http\Controllers\Scrapping\ScrappingController;
use App\Http\Controllers\Scrapping\ScrappingImportController;
use App\Http\Controllers\Scrapping\ScrappingSearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Scrapping (tests, production, registries, catalogues)
|--------------------------------------------------------------------------
| Accès réservé aux administrateurs (lecture et écriture).
*/
Route::middleware(['web', 'auth', 'role:admin', 'password.confirm'])->group(function () {

    // Routes de test (DataCollect sans orchestrateur)
    Route::prefix('scrapping/test')->group(function () {
        Route::get('/api', [DataCollectController::class, 'testApi'])
            ->name('scrapping.test.api');
        Route::get('/class/{id}', [DataCollectController::class, 'testCollectClass'])
            ->name('scrapping.test.class')
            ->where('id', '[1-9]|1[0-9]');
        Route::get('/monster/{id}', [DataCollectController::class, 'testCollectMonster'])
            ->name('scrapping.test.monster')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-4][0-9][0-9][0-9]|5000');
        Route::get('/item/{id}', [DataCollectController::class, 'testCollectItem'])
            ->name('scrapping.test.item')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-2][0-9][0-9][0-9][0-9]|30000');
        Route::get('/spell/{id}', [DataCollectController::class, 'testCollectSpell'])
            ->name('scrapping.test.spell')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-9][0-9][0-9][0-9][0-9]|20000');
        Route::get('/effect/{id}', [DataCollectController::class, 'testCollectEffect'])
            ->name('scrapping.test.effect')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|1000');
        Route::get('/items-by-type', [DataCollectController::class, 'testCollectItemsByType'])
            ->name('scrapping.test.items-by-type');
        Route::post('/clear-cache', [DataCollectController::class, 'testClearCache'])
            ->name('scrapping.test.clear-cache');
    });

    // Scrapping production (config, search, meta, preview, import)
    Route::prefix('scrapping')->group(function () {
        Route::get('/config', [ScrappingConfigController::class, 'index'])
            ->name('scrapping.config');
        Route::get('/search/{entity}', [ScrappingSearchController::class, 'search'])
            ->name('scrapping.search')
            ->where('entity', '[a-z0-9\\-]+');
        Route::get('/meta', [ScrappingController::class, 'meta'])
            ->name('scrapping.meta');
        Route::get('/preview/{type}/{id}', [ScrappingController::class, 'preview'])
            ->name('scrapping.preview')
            ->where('type', 'class|monster|item|spell|panoply|resource|consumable|equipment')
            ->whereNumber('id');
        Route::post('/preview/batch', [ScrappingController::class, 'previewBatch'])
            ->name('scrapping.preview.batch');
        Route::post('/jobs', [ScrappingController::class, 'createJob'])
            ->name('scrapping.jobs.create');
        Route::get('/jobs', [ScrappingController::class, 'listJobs'])
            ->name('scrapping.jobs.list');
        Route::get('/jobs/{jobId}', [ScrappingController::class, 'jobStatus'])
            ->name('scrapping.jobs.status');
        Route::post('/jobs/{jobId}/cancel', [ScrappingController::class, 'cancelJob'])
            ->name('scrapping.jobs.cancel');
        Route::get('/dofusdb/item-types', [DofusDbItemTypesCatalogController::class, 'index'])
            ->name('scrapping.dofusdb.item-types');
        Route::get('/dofusdb/characteristic-labels', [ScrappingController::class, 'dofusdbCharacteristicLabels'])
            ->name('scrapping.dofusdb.characteristic-labels');

        Route::prefix('import')->group(function () {
            Route::post('/class/{id}', [ScrappingController::class, 'importClass'])
                ->name('scrapping.import.class')
                ->where('id', '[1-9]|1[0-9]');
            Route::post('/monster/{id}', [ScrappingController::class, 'importMonster'])
                ->name('scrapping.import.monster')
                ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-4][0-9][0-9][0-9]|5000');
            Route::post('/item/{id}', [ScrappingController::class, 'importItem'])
                ->name('scrapping.import.item')
                ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-2][0-9][0-9][0-9][0-9]|30000');
            Route::post('/resource/{id}', [ScrappingController::class, 'importResource'])
                ->name('scrapping.import.resource')
                ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-2][0-9][0-9][0-9][0-9]|30000');
            Route::post('/consumable/{id}', [ScrappingController::class, 'importConsumable'])
                ->name('scrapping.import.consumable')
                ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-2][0-9][0-9][0-9][0-9]|30000');
            Route::post('/spell/{id}', [ScrappingController::class, 'importSpell'])
                ->name('scrapping.import.spell')
                ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-9][0-9][0-9][0-9][0-9]|20000');
            Route::post('/panoply/{id}', [ScrappingController::class, 'importPanoply'])
                ->name('scrapping.import.panoply')
                ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|1000');
            Route::post('/batch', [ScrappingController::class, 'importBatch'])
                ->name('scrapping.import.batch');
            Route::post('/range', [ScrappingController::class, 'importRange'])
                ->name('scrapping.import.range');
            Route::post('/all', [ScrappingController::class, 'importAll'])
                ->name('scrapping.import.all');
        });

        Route::post('/import-with-merge', [ScrappingController::class, 'importWithMerge'])
            ->name('scrapping.import.with-merge');
        Route::post('/import/{entity}/{id}', [ScrappingImportController::class, 'importOne'])
            ->name('scrapping.import.one')
            ->where('entity', 'monster|breed|spell|item|class|ressource|consumable')
            ->whereNumber('id');
    });

    // Registries (resource-types, item-types, consumable-types)
    Route::prefix('scrapping/resource-types')->group(function () {
        Route::get('/', [ResourceTypeRegistryController::class, 'index'])
            ->name('scrapping.resource-types.index');
        Route::get('/pending', [ResourceTypeRegistryController::class, 'pending'])
            ->name('scrapping.resource-types.pending');
        Route::patch('/bulk', [ResourceTypeRegistryController::class, 'bulkUpdate'])
            ->name('scrapping.resource-types.bulk');
        Route::post('/move-bulk', [ResourceTypeRegistryController::class, 'moveBulkToCategory'])
            ->name('scrapping.resource-types.move-bulk');
        Route::delete('/{resourceType}', [ResourceTypeRegistryController::class, 'destroy'])
            ->name('scrapping.resource-types.delete');
        Route::post('/{resourceType}/move', [ResourceTypeRegistryController::class, 'moveToCategory'])
            ->name('scrapping.resource-types.move');
        Route::patch('/{resourceType}/decision', [ResourceTypeRegistryController::class, 'updateDecision'])
            ->name('scrapping.resource-types.decision');
        Route::patch('/{resourceType}/catalog', [ResourceTypeRegistryController::class, 'updateCatalog'])
            ->name('scrapping.resource-types.catalog');
        Route::get('/{resourceType}/pending-items', [ResourceTypeRegistryController::class, 'pendingItems'])
            ->name('scrapping.resource-types.pending-items');
        Route::post('/{resourceType}/replay', [ResourceTypeRegistryController::class, 'replayPending'])
            ->name('scrapping.resource-types.replay');
    });

    Route::prefix('scrapping/item-types')->group(function () {
        Route::get('/', [ItemTypeRegistryController::class, 'index'])
            ->name('scrapping.item-types.index');
        Route::get('/pending', [ItemTypeRegistryController::class, 'pending'])
            ->name('scrapping.item-types.pending');
        Route::patch('/bulk', [ItemTypeRegistryController::class, 'bulkUpdate'])
            ->name('scrapping.item-types.bulk');
        Route::post('/move-bulk', [ItemTypeRegistryController::class, 'moveBulkToCategory'])
            ->name('scrapping.item-types.move-bulk');
        Route::delete('/{itemType}', [ItemTypeRegistryController::class, 'destroy'])
            ->name('scrapping.item-types.delete');
        Route::post('/{itemType}/move', [ItemTypeRegistryController::class, 'moveToCategory'])
            ->name('scrapping.item-types.move');
        Route::patch('/{itemType}/decision', [ItemTypeRegistryController::class, 'updateDecision'])
            ->name('scrapping.item-types.decision');
        Route::patch('/{itemType}/catalog', [ItemTypeRegistryController::class, 'updateCatalog'])
            ->name('scrapping.item-types.catalog');
    });

    Route::prefix('scrapping/consumable-types')->group(function () {
        Route::get('/', [ConsumableTypeRegistryController::class, 'index'])
            ->name('scrapping.consumable-types.index');
        Route::get('/pending', [ConsumableTypeRegistryController::class, 'pending'])
            ->name('scrapping.consumable-types.pending');
        Route::patch('/bulk', [ConsumableTypeRegistryController::class, 'bulkUpdate'])
            ->name('scrapping.consumable-types.bulk');
        Route::post('/move-bulk', [ConsumableTypeRegistryController::class, 'moveBulkToCategory'])
            ->name('scrapping.consumable-types.move-bulk');
        Route::delete('/{consumableType}', [ConsumableTypeRegistryController::class, 'destroy'])
            ->name('scrapping.consumable-types.delete');
        Route::post('/{consumableType}/move', [ConsumableTypeRegistryController::class, 'moveToCategory'])
            ->name('scrapping.consumable-types.move');
        Route::patch('/{consumableType}/decision', [ConsumableTypeRegistryController::class, 'updateDecision'])
            ->name('scrapping.consumable-types.decision');
        Route::patch('/{consumableType}/catalog', [ConsumableTypeRegistryController::class, 'updateCatalog'])
            ->name('scrapping.consumable-types.catalog');
    });

    // Catalogue DofusDB (races monstres)
    Route::prefix('scrapping/monster-races')->group(function () {
        Route::get('/', [DofusDbMonsterRacesCatalogController::class, 'index'])
            ->name('scrapping.monster-races.catalog');
    });
});
