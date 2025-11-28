<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\MonsterController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/monsters')->name('entities.monsters.')->group(function () {
    Route::get('/', [MonsterController::class, 'index'])->name('index');
    Route::get('/{monster}', [MonsterController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/monsters')->name('entities.monsters.')->middleware('auth')->group(function () {
    Route::get('/create', [MonsterController::class, 'create'])->name('create');
    Route::post('/', [MonsterController::class, 'store'])->name('store');
    Route::get('/{monster}/edit', [MonsterController::class, 'edit'])->name('edit');
    Route::patch('/{monster}', [MonsterController::class, 'update'])->name('update');
    Route::delete('/{monster}', [MonsterController::class, 'delete'])->name('delete');
});
