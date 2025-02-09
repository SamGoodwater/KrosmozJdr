<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ScenarioController;

Route::prefix('scenario')->name("scenario.")->group(function () use ($uniqidRegex, $slugRegex) {
    Route::get('/', [ScenarioController::class, 'index'])->name('index');
    Route::get('/{scenario:slug}', [ScenarioController::class, 'show'])->name('show')->where('scenario', $slugRegex);
    Route::get('/create', [ScenarioController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [ScenarioController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{scenario:slug}/edit', [ScenarioController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('scenario', $slugRegex);
    Route::patch('/{scenario:uniqid}', [ScenarioController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
    Route::delete('/{scenario:uniqid}', [ScenarioController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
    Route::post('/{scenario:uniqid}', [ScenarioController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
    Route::delete('/forcedDelete/{scenario:uniqid}', [ScenarioController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
});
