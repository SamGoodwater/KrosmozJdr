<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ConsumableController;

Route::prefix('consumable')->name("consumable.")->group(function () use ($uniqidRegex) {
    Route::get('/', [ConsumableController::class, 'index'])->name('index');
    Route::get('/{consumable:uniqid}', [ConsumableController::class, 'show'])->name('show')->where('consumable', $uniqidRegex);
    Route::get('/create', [ConsumableController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [ConsumableController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{consumable:uniqid}/edit', [ConsumableController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::patch('/{consumable:uniqid}', [ConsumableController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::delete('/{consumable:uniqid}', [ConsumableController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::post('/{consumable:uniqid}', [ConsumableController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::delete('/forcedDelete/{consumable:uniqid}', [ConsumableController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
});
