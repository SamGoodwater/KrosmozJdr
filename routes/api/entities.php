<?php

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
    Route::post('/resources/upload-image', [App\Http\Controllers\Api\ResourceImageUploadController::class, 'upload'])
        ->name('api.entities.resources.upload-image');
    Route::patch('/resources/bulk', [App\Http\Controllers\Api\ResourceBulkController::class, 'bulkUpdate'])
        ->name('api.entities.resources.bulk');
    Route::patch('/items/bulk', [App\Http\Controllers\Api\ItemBulkController::class, 'bulkUpdate'])
        ->name('api.entities.items.bulk');
    Route::patch('/spells/bulk', [App\Http\Controllers\Api\SpellBulkController::class, 'bulkUpdate'])
        ->name('api.entities.spells.bulk');
    Route::patch('/monsters/bulk', [App\Http\Controllers\Api\MonsterBulkController::class, 'bulkUpdate'])
        ->name('api.entities.monsters.bulk');
    Route::patch('/campaigns/bulk', [App\Http\Controllers\Api\CampaignBulkController::class, 'bulkUpdate'])
        ->name('api.entities.campaigns.bulk');
    Route::patch('/scenarios/bulk', [App\Http\Controllers\Api\ScenarioBulkController::class, 'bulkUpdate'])
        ->name('api.entities.scenarios.bulk');
    Route::patch('/attributes/bulk', [App\Http\Controllers\Api\AttributeBulkController::class, 'bulkUpdate'])
        ->name('api.entities.attributes.bulk');
    Route::patch('/panoplies/bulk', [App\Http\Controllers\Api\PanoplyBulkController::class, 'bulkUpdate'])
        ->name('api.entities.panoplies.bulk');
    Route::patch('/capabilities/bulk', [App\Http\Controllers\Api\CapabilityBulkController::class, 'bulkUpdate'])
        ->name('api.entities.capabilities.bulk');
    Route::patch('/specializations/bulk', [App\Http\Controllers\Api\SpecializationBulkController::class, 'bulkUpdate'])
        ->name('api.entities.specializations.bulk');
    Route::patch('/shops/bulk', [App\Http\Controllers\Api\ShopBulkController::class, 'bulkUpdate'])
        ->name('api.entities.shops.bulk');
    Route::patch('/creatures/bulk', [App\Http\Controllers\Api\CreatureBulkController::class, 'bulkUpdate'])
        ->name('api.entities.creatures.bulk');
    Route::patch('/npcs/bulk', [App\Http\Controllers\Api\NpcBulkController::class, 'bulkUpdate'])
        ->name('api.entities.npcs.bulk');
    Route::patch('/breeds/bulk', [App\Http\Controllers\Api\BreedBulkController::class, 'bulkUpdate'])
        ->name('api.entities.breeds.bulk');
    Route::patch('/consumables/bulk', [App\Http\Controllers\Api\ConsumableBulkController::class, 'bulkUpdate'])
        ->name('api.entities.consumables.bulk');
});
