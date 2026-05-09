<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\SpecializationController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/specializations')->name('entities.specializations.')->group(function () {
    Route::get('/', [SpecializationController::class, 'index'])->name('index');
    Route::get('/{specialization}', [SpecializationController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/specializations')->name('entities.specializations.')->middleware('auth')->group(function () {
    Route::get('/create', [SpecializationController::class, 'create'])->name('create');
    Route::post('/', [SpecializationController::class, 'store'])->name('store');
    Route::get('/{specialization}/edit', [SpecializationController::class, 'edit'])->name('edit');
    Route::get('/{specialization}/pdf', [SpecializationController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{specialization}', [SpecializationController::class, 'update'])->name('update');
    Route::patch('/{specialization}/spells', [SpecializationController::class, 'updateSpells'])->name('updateSpells');
    Route::patch('/{specialization}/capabilities', [SpecializationController::class, 'updateCapabilities'])->name('updateCapabilities');
    Route::patch('/{specialization}/creature-traits', [SpecializationController::class, 'updateCreatureTraits'])->name('updateCreatureTraits');
    Route::patch('/{specialization}/consumables', [SpecializationController::class, 'updateConsumables'])->name('updateConsumables');
    Route::patch('/{specialization}/resources', [SpecializationController::class, 'updateResources'])->name('updateResources');
    Route::patch('/{specialization}/items', [SpecializationController::class, 'updateItems'])->name('updateItems');
    Route::patch('/{specialization}/sections', [SpecializationController::class, 'updateSections'])->name('updateSections');
    Route::delete('/{specialization}', [SpecializationController::class, 'delete'])->name('delete');
});
