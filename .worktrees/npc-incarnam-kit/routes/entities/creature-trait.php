<?php

use App\Http\Controllers\Entity\CreatureTraitController;
use Illuminate\Support\Facades\Route;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/creature-traits')->name('entities.creature-traits.')->group(function () {
    Route::get('/', [CreatureTraitController::class, 'index'])->name('index');
    Route::get('/{creatureTrait}', [CreatureTraitController::class, 'show'])->whereNumber('creatureTrait')->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/creature-traits')->name('entities.creature-traits.')->middleware('auth')->group(function () {
    Route::get('/create', [CreatureTraitController::class, 'create'])->name('create');
    Route::post('/', [CreatureTraitController::class, 'store'])->name('store');
    Route::get('/{creatureTrait}/edit', [CreatureTraitController::class, 'edit'])->name('edit');
    Route::get('/{creatureTrait}/pdf', [CreatureTraitController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{creatureTrait}', [CreatureTraitController::class, 'update'])->name('update');
    Route::delete('/{creatureTrait}', [CreatureTraitController::class, 'delete'])->name('delete');
});
