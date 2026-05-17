<?php

use App\Http\Controllers\Api\Table\BreedTableController;
use App\Http\Controllers\Api\Table\CampaignTableController;
use App\Http\Controllers\Api\Table\CapabilityTableController;
use App\Http\Controllers\Api\Table\ConditionTableController;
use App\Http\Controllers\Api\Table\ConsumableTableController;
use App\Http\Controllers\Api\Table\ConsumableTypeTableController;
use App\Http\Controllers\Api\Table\CreatureTableController;
use App\Http\Controllers\Api\Table\CreatureTraitTableController;
use App\Http\Controllers\Api\Table\ItemTableController;
use App\Http\Controllers\Api\Table\ItemTypeTableController;
use App\Http\Controllers\Api\Table\MonsterRaceTableController;
use App\Http\Controllers\Api\Table\MonsterTableController;
use App\Http\Controllers\Api\Table\NpcTableController;
use App\Http\Controllers\Api\Table\PanoplyTableController;
use App\Http\Controllers\Api\Table\ResourceTableController;
use App\Http\Controllers\Api\Table\ResourceTypeTableController;
use App\Http\Controllers\Api\Table\ScenarioTableController;
use App\Http\Controllers\Api\Table\ShopTableController;
use App\Http\Controllers\Api\Table\SpecializationTableController;
use App\Http\Controllers\Api\Table\SpellCategoryTableController;
use App\Http\Controllers\Api\Table\SpellTableController;
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
    Route::get('/resources', [ResourceTableController::class, 'index'])
        ->name('api.tables.resources');
    Route::get('/resource-types', [ResourceTypeTableController::class, 'index'])
        ->name('api.tables.resource-types');
    Route::get('/items', [ItemTableController::class, 'index'])
        ->name('api.tables.items');
    Route::get('/item-types', [ItemTypeTableController::class, 'index'])
        ->name('api.tables.item-types');
    Route::get('/consumable-types', [ConsumableTypeTableController::class, 'index'])
        ->name('api.tables.consumable-types');
    Route::get('/monster-races', [MonsterRaceTableController::class, 'index'])
        ->name('api.tables.monster-races');
    Route::get('/spell-categories', [SpellCategoryTableController::class, 'index'])
        ->name('api.tables.spell-categories');
    Route::get('/spells', [SpellTableController::class, 'index'])
        ->name('api.tables.spells');
    Route::get('/monsters', [MonsterTableController::class, 'index'])
        ->name('api.tables.monsters');
    Route::get('/npcs', [NpcTableController::class, 'index'])
        ->name('api.tables.npcs');
    Route::get('/campaigns', [CampaignTableController::class, 'index'])
        ->name('api.tables.campaigns');
    Route::get('/scenarios', [ScenarioTableController::class, 'index'])
        ->name('api.tables.scenarios');
    Route::get('/conditions', [ConditionTableController::class, 'index'])
        ->name('api.tables.conditions');
    Route::get('/capabilities', [CapabilityTableController::class, 'index'])
        ->name('api.tables.capabilities');
    Route::get('/breeds', [BreedTableController::class, 'index'])
        ->name('api.tables.breeds');
    Route::get('/specializations', [SpecializationTableController::class, 'index'])
        ->name('api.tables.specializations');
    Route::get('/creatures', [CreatureTableController::class, 'index'])
        ->name('api.tables.creatures');
    Route::get('/consumables', [ConsumableTableController::class, 'index'])
        ->name('api.tables.consumables');
    Route::get('/panoplies', [PanoplyTableController::class, 'index'])
        ->name('api.tables.panoplies');
    Route::get('/creature-traits', [CreatureTraitTableController::class, 'index'])
        ->name('api.tables.creature-traits');
    Route::get('/shops', [ShopTableController::class, 'index'])
        ->name('api.tables.shops');
});
