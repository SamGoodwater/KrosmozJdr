<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ConditionController;

Route::prefix('condition')->name("condition.")->group(function () use ($uniqidRegex) {
    Route::get('/', [ConditionController::class, 'index'])->name('index');
    Route::get('/{condition:uniqid}', [ConditionController::class, 'show'])->name('show')->where('condition', $uniqidRegex);
    Route::get('/create', [ConditionController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [ConditionController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{condition:uniqid}/edit', [ConditionController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::patch('/{condition:uniqid}', [ConditionController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::delete('/{condition:uniqid}', [ConditionController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::post('/{condition:uniqid}', [ConditionController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::delete('/forcedDelete/{condition:uniqid}', [ConditionController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
});
