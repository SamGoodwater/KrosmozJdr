<?php

use App\Http\Controllers\Api\BreedBulkController;
use App\Http\Controllers\Api\CampaignBulkController;
use App\Http\Controllers\Api\CapabilityBulkController;
use App\Http\Controllers\Api\ConditionBulkController;
use App\Http\Controllers\Api\ConsumableBulkController;
use App\Http\Controllers\Api\CreatureBulkController;
use App\Http\Controllers\Api\CreatureTraitBulkController;
use App\Http\Controllers\Api\EntityDeletionController;
use App\Http\Controllers\Api\EntityDofusdbRefreshController;
use App\Http\Controllers\Api\EntityStateController;
use App\Http\Controllers\Api\ItemBulkController;
use App\Http\Controllers\Api\MonsterBulkController;
use App\Http\Controllers\Api\NpcBulkController;
use App\Http\Controllers\Api\PanoplyBulkController;
use App\Http\Controllers\Api\ResourceBulkController;
use App\Http\Controllers\Api\ResourceImageUploadController;
use App\Http\Controllers\Api\ScenarioBulkController;
use App\Http\Controllers\Api\ShopBulkController;
use App\Http\Controllers\Api\SpecializationBulkController;
use App\Http\Controllers\Api\SpellBulkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Entités (bulk update, upload)
|--------------------------------------------------------------------------
|
| Actions en lot et upload d'images pour les tables UI.
|
*/

Route::middleware(['web', 'auth'])->prefix('entities')->group(function () {
    Route::post('/resources/upload-image', [ResourceImageUploadController::class, 'upload'])
        ->name('api.entities.resources.upload-image');
    Route::patch('/{entityType}/{id}/state', [EntityStateController::class, 'update'])
        ->where('entityType', '[a-z-]+')
        ->whereNumber('id')
        ->name('api.entities.state.update');
    Route::post('/{entityType}/{id}/dofusdb-refresh', EntityDofusdbRefreshController::class)
        ->middleware(['role:game_master', 'throttle:12,1'])
        ->where('entityType', '[a-z-]+')
        ->whereNumber('id')
        ->name('api.entities.dofusdb-refresh');
    Route::get('/{entityType}/{id}/delete-impact', [EntityDeletionController::class, 'impact'])
        ->where('entityType', '[a-z-]+')
        ->whereNumber('id')
        ->name('api.entities.delete-impact');
    Route::delete('/{entityType}/{id}', [EntityDeletionController::class, 'delete'])
        ->where('entityType', '[a-z-]+')
        ->whereNumber('id')
        ->name('api.entities.delete');
    Route::post('/{entityType}/{id}/restore', [EntityDeletionController::class, 'restore'])
        ->where('entityType', '[a-z-]+')
        ->whereNumber('id')
        ->name('api.entities.restore');
    Route::delete('/{entityType}/{id}/force', [EntityDeletionController::class, 'forceDelete'])
        ->where('entityType', '[a-z-]+')
        ->whereNumber('id')
        ->name('api.entities.force-delete');
    Route::patch('/resources/bulk', [ResourceBulkController::class, 'bulkUpdate'])
        ->name('api.entities.resources.bulk');
    Route::patch('/items/bulk', [ItemBulkController::class, 'bulkUpdate'])
        ->name('api.entities.items.bulk');
    Route::patch('/spells/bulk', [SpellBulkController::class, 'bulkUpdate'])
        ->name('api.entities.spells.bulk');
    Route::patch('/monsters/bulk', [MonsterBulkController::class, 'bulkUpdate'])
        ->name('api.entities.monsters.bulk');
    Route::patch('/campaigns/bulk', [CampaignBulkController::class, 'bulkUpdate'])
        ->name('api.entities.campaigns.bulk');
    Route::patch('/scenarios/bulk', [ScenarioBulkController::class, 'bulkUpdate'])
        ->name('api.entities.scenarios.bulk');
    Route::patch('/conditions/bulk', [ConditionBulkController::class, 'bulkUpdate'])
        ->name('api.entities.conditions.bulk');
    Route::patch('/panoplies/bulk', [PanoplyBulkController::class, 'bulkUpdate'])
        ->name('api.entities.panoplies.bulk');
    Route::patch('/capabilities/bulk', [CapabilityBulkController::class, 'bulkUpdate'])
        ->name('api.entities.capabilities.bulk');
    Route::patch('/specializations/bulk', [SpecializationBulkController::class, 'bulkUpdate'])
        ->name('api.entities.specializations.bulk');
    Route::patch('/shops/bulk', [ShopBulkController::class, 'bulkUpdate'])
        ->name('api.entities.shops.bulk');
    Route::patch('/creatures/bulk', [CreatureBulkController::class, 'bulkUpdate'])
        ->name('api.entities.creatures.bulk');
    Route::patch('/npcs/bulk', [NpcBulkController::class, 'bulkUpdate'])
        ->name('api.entities.npcs.bulk');
    Route::patch('/breeds/bulk', [BreedBulkController::class, 'bulkUpdate'])
        ->name('api.entities.breeds.bulk');
    Route::patch('/creature-traits/bulk', [CreatureTraitBulkController::class, 'bulkUpdate'])
        ->name('api.entities.creature-traits.bulk');
    Route::patch('/consumables/bulk', [ConsumableBulkController::class, 'bulkUpdate'])
        ->name('api.entities.consumables.bulk');
});
