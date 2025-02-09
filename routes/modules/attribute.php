<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\AttributeController;

Route::prefix('attribute')->name("attribute.")->group(function () use ($uniqidRegex) {
    Route::get('/', [AttributeController::class, 'index'])->name('index');
    Route::get('/{attribute:uniqid}', [AttributeController::class, 'show'])->name('show')->where('attribute', $uniqidRegex);
    Route::get('/create', [AttributeController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [AttributeController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{attribute:uniqid}/edit', [AttributeController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::patch('/{attribute:uniqid}', [AttributeController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::delete('/{attribute:uniqid}', [AttributeController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::post('/{attribute:uniqid}', [AttributeController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::delete('/forcedDelete/{attribute:uniqid}', [AttributeController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
});
