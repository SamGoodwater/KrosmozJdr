<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ItemController;

Route::prefix('item')->name("item.")->group(function () use ($uniqidRegex) {
    Route::get('/', [ItemController::class, 'index'])->name('index');
    Route::get('/{item:uniqid}', [ItemController::class, 'show'])->name('show')->where('item', $uniqidRegex);
    Route::get('/create', [ItemController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [ItemController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{item:uniqid}/edit', [ItemController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::patch('/{item:uniqid}', [ItemController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::delete('/{item:uniqid}', [ItemController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::post('/{item:uniqid}', [ItemController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::delete('/forcedDelete/{item:uniqid}', [ItemController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
});
