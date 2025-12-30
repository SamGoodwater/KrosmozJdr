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

Route::prefix('scrapping')->group(function () {
    // Métadonnées des types d'entités
    Route::get('/meta', [App\Http\Controllers\Scrapping\ScrappingController::class, 'meta'])
        ->name('scrapping.meta');

    Route::get('/preview/{type}/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'preview'])
        ->name('scrapping.preview')
        ->where('type', 'class|monster|item|spell|panoply')
        ->whereNumber('id');

    Route::prefix('import')->group(function () {
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
        
        // Import d'une panoplie
        Route::post('/panoply/{id}', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importPanoply'])
            ->name('scrapping.import.panoply')
            ->where('id', '[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|1000'); // 1-1000
        
        // Import en lot
        Route::post('/batch', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importBatch'])
            ->name('scrapping.import.batch');

        // Import d'une plage d'ID
        Route::post('/range', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importRange'])
            ->name('scrapping.import.range');

        // Import complet
        Route::post('/all', [App\Http\Controllers\Scrapping\ScrappingController::class, 'importAll'])
            ->name('scrapping.import.all');
    });
});

/*
|--------------------------------------------------------------------------
| Registry des typeId DofusDB (Ressources)
|--------------------------------------------------------------------------
|
| Endpoints pour valider/blacklister les nouveaux typeId détectés
| sans modifier le code.
|
*/
// IMPORTANT:
// Ces endpoints sont consommés depuis l'UI Inertia (session/cookies).
// On applique donc le middleware `web` pour activer la session + CSRF, puis `auth`.
Route::middleware(['web', 'auth'])->prefix('scrapping/resource-types')->group(function () {
    Route::get('/', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'index'])
        ->name('scrapping.resource-types.index');
    Route::get('/pending', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'pending'])
        ->name('scrapping.resource-types.pending');
    Route::patch('/bulk', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'bulkUpdate'])
        ->name('scrapping.resource-types.bulk');
    Route::patch('/{resourceType}/decision', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'updateDecision'])
        ->name('scrapping.resource-types.decision');
    Route::get('/{resourceType}/pending-items', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'pendingItems'])
        ->name('scrapping.resource-types.pending-items');
    Route::post('/{resourceType}/replay', [App\Http\Controllers\Scrapping\ResourceTypeRegistryController::class, 'replayPending'])
        ->name('scrapping.resource-types.replay');
});

/*
|--------------------------------------------------------------------------
| Datasets pour tables (TanStack Table - mode client)
|--------------------------------------------------------------------------
|
| Endpoints limités destinés à charger un gros lot d'entités afin de permettre
| tri/filtre/recherche/pagination côté frontend.
|
*/
// Même raison : endpoints utilisés par l'UI Inertia (axios) via session.
Route::middleware(['web', 'auth'])->prefix('entity-table')->group(function () {
    Route::get('/resources', [App\Http\Controllers\Api\EntityTableDataController::class, 'resources'])
        ->name('api.entity-table.resources');
    Route::get('/resource-types', [App\Http\Controllers\Api\EntityTableDataController::class, 'resourceTypes'])
        ->name('api.entity-table.resource-types');
});

/*
|--------------------------------------------------------------------------
| Table v2 (TanStack Table) — TableResponse typé
|--------------------------------------------------------------------------
|
| Endpoints retournant des cellules typées `Cell{type,value,params}`.
| Server opt-in : consommés uniquement si la page fournit un `serverUrl` complet.
|
*/
Route::middleware(['web'])->prefix('tables')->group(function () {
    Route::get('/resources', [App\Http\Controllers\Api\Table\ResourceTableController::class, 'index'])
        ->name('api.tables.resources');
    Route::get('/resource-types', [App\Http\Controllers\Api\Table\ResourceTypeTableController::class, 'index'])
        ->name('api.tables.resource-types');
    Route::get('/items', [App\Http\Controllers\Api\Table\ItemTableController::class, 'index'])
        ->name('api.tables.items');
    Route::get('/spells', [App\Http\Controllers\Api\Table\SpellTableController::class, 'index'])
        ->name('api.tables.spells');
    Route::get('/monsters', [App\Http\Controllers\Api\Table\MonsterTableController::class, 'index'])
        ->name('api.tables.monsters');
    Route::get('/npcs', [App\Http\Controllers\Api\Table\NpcTableController::class, 'index'])
        ->name('api.tables.npcs');
    Route::get('/campaigns', [App\Http\Controllers\Api\Table\CampaignTableController::class, 'index'])
        ->name('api.tables.campaigns');
    Route::get('/scenarios', [App\Http\Controllers\Api\Table\ScenarioTableController::class, 'index'])
        ->name('api.tables.scenarios');
    Route::get('/attributes', [App\Http\Controllers\Api\Table\AttributeTableController::class, 'index'])
        ->name('api.tables.attributes');
    Route::get('/capabilities', [App\Http\Controllers\Api\Table\CapabilityTableController::class, 'index'])
        ->name('api.tables.capabilities');
    Route::get('/classes', [App\Http\Controllers\Api\Table\ClasseTableController::class, 'index'])
        ->name('api.tables.classes');
    Route::get('/specializations', [App\Http\Controllers\Api\Table\SpecializationTableController::class, 'index'])
        ->name('api.tables.specializations');
    Route::get('/creatures', [App\Http\Controllers\Api\Table\CreatureTableController::class, 'index'])
        ->name('api.tables.creatures');
    Route::get('/consumables', [App\Http\Controllers\Api\Table\ConsumableTableController::class, 'index'])
        ->name('api.tables.consumables');
    Route::get('/panoplies', [App\Http\Controllers\Api\Table\PanoplyTableController::class, 'index'])
        ->name('api.tables.panoplies');
    Route::get('/shops', [App\Http\Controllers\Api\Table\ShopTableController::class, 'index'])
        ->name('api.tables.shops');
});

/*
|--------------------------------------------------------------------------
| Bulk update (UI tables)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->prefix('entities')->group(function () {
    Route::patch('/resources/bulk', [App\Http\Controllers\Api\ResourceBulkController::class, 'bulkUpdate'])
        ->name('api.entities.resources.bulk');
    Route::patch('/items/bulk', [App\Http\Controllers\Api\ItemBulkController::class, 'bulkUpdate'])
        ->name('api.entities.items.bulk');
    Route::patch('/spells/bulk', [App\Http\Controllers\Api\SpellBulkController::class, 'bulkUpdate'])
        ->name('api.entities.spells.bulk');
        ->name('api.entities.items.bulk');
});
