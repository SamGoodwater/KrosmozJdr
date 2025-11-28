<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\ScenarioController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/scenarios')->name('entities.scenarios.')->group(function () {
    Route::get('/', [ScenarioController::class, 'index'])->name('index');
    Route::get('/{scenario}', [ScenarioController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/scenarios')->name('entities.scenarios.')->middleware('auth')->group(function () {
    Route::get('/create', [ScenarioController::class, 'create'])->name('create');
    Route::post('/', [ScenarioController::class, 'store'])->name('store');
    Route::get('/{scenario}/edit', [ScenarioController::class, 'edit'])->name('edit');
    Route::patch('/{scenario}', [ScenarioController::class, 'update'])->name('update');
    Route::delete('/{scenario}', [ScenarioController::class, 'delete'])->name('delete');
});
