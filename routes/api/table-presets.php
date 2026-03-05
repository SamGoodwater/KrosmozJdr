<?php

use App\Http\Controllers\Api\Table\TableFilterPresetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Presets de filtres de tableaux
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])->prefix('table-presets')->name('api.table-presets.')->group(function () {
    Route::get('/', [TableFilterPresetController::class, 'index'])->name('index');
    Route::post('/', [TableFilterPresetController::class, 'store'])->name('store');
    Route::patch('/{tablePreset}', [TableFilterPresetController::class, 'update'])->name('update');
    Route::delete('/{tablePreset}', [TableFilterPresetController::class, 'destroy'])->name('destroy');
});

