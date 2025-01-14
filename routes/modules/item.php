<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ItemController;

Route::prefix('item')->name("item.")->controller(ItemController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{item:uniqid}', 'show')->name('show')->where('item', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{item:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::patch('/{item:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::delete('/{item:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::post('/{item:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
    Route::delete('/forcedDelete/{item:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('item', $uniqidRegex);
});
