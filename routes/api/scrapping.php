<?php

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
    Route::get('/api', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testApi'])
        ->name('scrapping.test.api');
    Route::get('/class/{id}', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectClass'])
        ->name('scrapping.test.class')
        ->where('id', '[1-9]|1[0-9]');
    Route::get('/monster/{id}', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectMonster'])
        ->name('scrapping.test.monster')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-4][0-9][0-9][0-9]|5000');
    Route::get('/item/{id}', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectItem'])
        ->name('scrapping.test.item')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-2][0-9][0-9][0-9][0-9]|30000');
    Route::get('/spell/{id}', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectSpell'])
        ->name('scrapping.test.spell')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-9][0-9][0-9][0-9][0-9]|20000');
    Route::get('/effect/{id}', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectEffect'])
        ->name('scrapping.test.effect')
        ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|1000');
    Route::get('/items-by-type', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testCollectItemsByType'])
        ->name('scrapping.test.items-by-type');
    Route::post('/clear-cache', [App\Http\Controllers\Scrapping\DataCollectController::class, 'testClearCache'])
        ->name('scrapping.test.clear-cache');
});

// Scrapping production (config, search, meta, preview, import)
Route::prefix('scrapping')->group(function () {
    Route::get('/config', [App\Http\Controllers\Scrapping\ScrappingConfigController::class, 'index'])
        ->name('scrapping.config');
    Route::get('/search/{entity}', [App\Http\Controllers\Scrapping\ScrappingSearchController::class, 'search'])
        ->name('scrapping.search')
        ->where('entity', '[a-z0-9\\-]+');
    Route::get('/meta', [App\Http\Controllers\Scrapping\ScrappingController::class, 'meta'])
        ->name('scrapping.meta');
    Route::get('/preview/{type}/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'preview'])
        ->name('scrapping.preview')
        ->where('type', 'class|monster|item|spell|panoply|resource|consumable|equipment')
        ->whereNumber('id');
    Route::post('/preview/batch', [App\Http\Controllers\Scrapping\ScrappingController::class, 'previewBatch'])
        ->name('scrapping.preview.batch');
    Route::post('/jobs', [App\Http\Controllers\Scrapping\ScrappingController::class, 'createJob'])
        ->name('scrapping.jobs.create');
    Route::get('/jobs', [App\Http\Controllers\Scrapping\ScrappingController::class, 'listJobs'])
        ->name('scrapping.jobs.list');
    Route::get('/jobs/{jobId}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'jobStatus'])
        ->name('scrapping.jobs.status');
    Route::post('/jobs/{jobId}/cancel', [App\Http\Controllers\Scrapping\ScrappingController::class, 'cancelJob'])
        ->name('scrapping.jobs.cancel');
    Route::get('/dofusdb/item-types', [App\Http\Controllers\Scrapping\DofusDbItemTypesCatalogController::class, 'index'])
        ->name('scrapping.dofusdb.item-types');
    Route::get('/dofusdb/characteristic-labels', [App\Http\Controllers\Scrapping\ScrappingController::class, 'dofusdbCharacteristicLabels'])
        ->name('scrapping.dofusdb.characteristic-labels');

    Route::prefix('import')->group(function () {
        Route::post('/class/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importClass'])
            ->name('scrapping.import.class')
            ->where('id', '[1-9]|1[0-9]');
        Route::post('/monster/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importMonster'])
            ->name('scrapping.import.monster')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-4][0-9][0-9][0-9]|5000');
        Route::post('/item/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importItem'])
            ->name('scrapping.import.item')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-2][0-9][0-9][0-9][0-9]|30000');
        Route::post('/resource/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importResource'])
            ->name('scrapping.import.resource')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-2][0-9][0-9][0-9][0-9]|30000');
        Route::post('/consumable/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importConsumable'])
            ->name('scrapping.import.consumable')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-2][0-9][0-9][0-9][0-9]|30000');
        Route::post('/spell/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importSpell'])
            ->name('scrapping.import.spell')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-9][0-9][0-9][0-9][0-9]|20000');
        Route::post('/panoply/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importPanoply'])
            ->name('scrapping.import.panoply')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|1000');
        Route::post('/batch', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importBatch'])
            ->name('scrapping.import.batch');
        Route::post('/range', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importRange'])
            ->name('scrapping.import.range');
        Route::post('/all', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importAll'])
            ->name('scrapping.import.all');
    });

    Route::post('/import-with-merge', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importWithMerge'])
        ->name('scrapping.import.with-merge');
    Route::post('/import/{entity}/{id}', [App\Http\Controllers\Scrapping\ScrappingImportController::class, 'importOne'])
        ->name('scrapping.import.one')
        ->where('entity', 'monster|breed|spell|item|class|ressource|consumable')
        ->whereNumber('id');
});

// Registries (resource-types, item-types, consumable-types)
Route::prefix('scrapping/resource-types')->group(function () {
    Route::get('/', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'index'])
        ->name('scrapping.resource-types.index');
    Route::get('/pending', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'pending'])
        ->name('scrapping.resource-types.pending');
    Route::patch('/bulk', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'bulkUpdate'])
        ->name('scrapping.resource-types.bulk');
    Route::post('/move-bulk', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'moveBulkToCategory'])
        ->name('scrapping.resource-types.move-bulk');
    Route::delete('/{resourceType}', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'destroy'])
        ->name('scrapping.resource-types.delete');
    Route::post('/{resourceType}/move', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'moveToCategory'])
        ->name('scrapping.resource-types.move');
    Route::patch('/{resourceType}/decision', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'updateDecision'])
        ->name('scrapping.resource-types.decision');
    Route::get('/{resourceType}/pending-items', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'pendingItems'])
        ->name('scrapping.resource-types.pending-items');
    Route::post('/{resourceType}/replay', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'replayPending'])
        ->name('scrapping.resource-types.replay');
});

Route::prefix('scrapping/item-types')->group(function () {
    Route::get('/', [App\Http\Controllers\Scrapping\ItemTypeRegistryController::class, 'index'])
        ->name('scrapping.item-types.index');
    Route::get('/pending', [App\Http\Controllers\Scrapping\ItemTypeRegistryController::class, 'pending'])
        ->name('scrapping.item-types.pending');
    Route::patch('/bulk', [App\Http\Controllers\Scrapping\ItemTypeRegistryController::class, 'bulkUpdate'])
        ->name('scrapping.item-types.bulk');
    Route::post('/move-bulk', [App\Http\Controllers\Scrapping\ItemTypeRegistryController::class, 'moveBulkToCategory'])
        ->name('scrapping.item-types.move-bulk');
    Route::delete('/{itemType}', [App\Http\Controllers\Scrapping\ItemTypeRegistryController::class, 'destroy'])
        ->name('scrapping.item-types.delete');
    Route::post('/{itemType}/move', [App\Http\Controllers\Scrapping\ItemTypeRegistryController::class, 'moveToCategory'])
        ->name('scrapping.item-types.move');
    Route::patch('/{itemType}/decision', [App\Http\Controllers\Scrapping\ItemTypeRegistryController::class, 'updateDecision'])
        ->name('scrapping.item-types.decision');
});

Route::prefix('scrapping/consumable-types')->group(function () {
    Route::get('/', [App\Http\Controllers\Scrapping\ConsumableTypeRegistryController::class, 'index'])
        ->name('scrapping.consumable-types.index');
    Route::get('/pending', [App\Http\Controllers\Scrapping\ConsumableTypeRegistryController::class, 'pending'])
        ->name('scrapping.consumable-types.pending');
    Route::patch('/bulk', [App\Http\Controllers\Scrapping\ConsumableTypeRegistryController::class, 'bulkUpdate'])
        ->name('scrapping.consumable-types.bulk');
    Route::post('/move-bulk', [App\Http\Controllers\Scrapping\ConsumableTypeRegistryController::class, 'moveBulkToCategory'])
        ->name('scrapping.consumable-types.move-bulk');
    Route::delete('/{consumableType}', [App\Http\Controllers\Scrapping\ConsumableTypeRegistryController::class, 'destroy'])
        ->name('scrapping.consumable-types.delete');
    Route::post('/{consumableType}/move', [App\Http\Controllers\Scrapping\ConsumableTypeRegistryController::class, 'moveToCategory'])
        ->name('scrapping.consumable-types.move');
    Route::patch('/{consumableType}/decision', [App\Http\Controllers\Scrapping\ConsumableTypeRegistryController::class, 'updateDecision'])
        ->name('scrapping.consumable-types.decision');
});

// Catalogue DofusDB (races monstres)
Route::prefix('scrapping/monster-races')->group(function () {
    Route::get('/', [App\Http\Controllers\Scrapping\DofusDbMonsterRacesCatalogController::class, 'index'])
        ->name('scrapping.monster-races.catalog');
});
});
