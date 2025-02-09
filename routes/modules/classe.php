<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ClasseController;

Route::prefix('classe')->name("classe.")->group(function () use ($uniqidRegex) {
    Route::get('/', [ClasseController::class, 'index'])->name('index');
    Route::get('/{class:uniqid}', [ClasseController::class, 'show'])->name('show')->where('classe', $uniqidRegex);
    Route::get('/create', [ClasseController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [ClasseController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{classe:uniqid}/edit', [ClasseController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::patch('/{classe:uniqid}', [ClasseController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::delete('/{classe:uniqid}', [ClasseController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::post('/{classe:uniqid}', [ClasseController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::delete('/forcedDelete/{classe:uniqid}', [ClasseController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
});
