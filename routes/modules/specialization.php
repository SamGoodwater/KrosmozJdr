<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SpecializationController;

Route::prefix('specialization')->name("specialization.")->group(function () use ($uniqidRegex) {
    Route::get('/', [SpecializationController::class, 'index'])->name('index');
    Route::get('/{specialization:uniqid}', [SpecializationController::class, 'show'])->name('show')->where('specialization', $uniqidRegex);
    Route::get('/create', [SpecializationController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [SpecializationController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{specialization:uniqid}/edit', [SpecializationController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::patch('/{specialization:uniqid}', [SpecializationController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::delete('/{specialization:uniqid}', [SpecializationController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::post('/{specialization:uniqid}', [SpecializationController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::delete('/forcedDelete/{specialization:uniqid}', [SpecializationController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
});
