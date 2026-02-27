<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Tables v2 (TanStack Table, TableResponse typé)
|--------------------------------------------------------------------------
|
| Endpoints retournant des cellules typées Cell{type,value,params}.
| Server opt-in : consommés si la page fournit un serverUrl complet.
|
*/

Route::middleware(['web'])->prefix('tables')->group(function () {
    Route::get('/resources', [App\Http\Controllers\Api\Table\ResourceTableController::class, 'index'])
        ->name('api.tables.resources');
    Route::get('/resource-types', [App\Http\Controllers\Api\Table\ResourceTypeTableController::class, 'index'])
        ->name('api.tables.resource-types');
    Route::get('/items', [App\Http\Controllers\Api\Table\ItemTableController::class, 'index'])
        ->name('api.tables.items');
    Route::get('/item-types', [App\Http\Controllers\Api\Table\ItemTypeTableController::class, 'index'])
        ->name('api.tables.item-types');
    Route::get('/consumable-types', [App\Http\Controllers\Api\Table\ConsumableTypeTableController::class, 'index'])
        ->name('api.tables.consumable-types');
    Route::get('/monster-races', [App\Http\Controllers\Api\Table\MonsterRaceTableController::class, 'index'])
        ->name('api.tables.monster-races');
    Route::get('/spell-categories', [App\Http\Controllers\Api\Table\SpellCategoryTableController::class, 'index'])
        ->name('api.tables.spell-categories');
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
    Route::get('/breeds', [App\Http\Controllers\Api\Table\BreedTableController::class, 'index'])
        ->name('api.tables.breeds');
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
