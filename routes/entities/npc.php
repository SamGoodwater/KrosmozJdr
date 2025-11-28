<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entity\NpcController;

// Routes publiques (accessibles sans authentification)
Route::prefix('entities/npcs')->name('entities.npcs.')->group(function () {
    Route::get('/', [NpcController::class, 'index'])->name('index');
    Route::get('/{npc}', [NpcController::class, 'show'])->name('show');
});

// Routes protégées (nécessitent une authentification)
Route::prefix('entities/npcs')->name('entities.npcs.')->middleware('auth')->group(function () {
    Route::get('/create', [NpcController::class, 'create'])->name('create');
    Route::post('/', [NpcController::class, 'store'])->name('store');
    Route::get('/{npc}/edit', [NpcController::class, 'edit'])->name('edit');
    Route::patch('/{npc}', [NpcController::class, 'update'])->name('update');
    Route::delete('/{npc}', [NpcController::class, 'delete'])->name('delete');
});
