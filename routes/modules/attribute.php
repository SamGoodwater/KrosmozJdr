<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\AttributeController;

Route::prefix('attribute')->name("attribute.")->controller(AttributeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{attribute:uniqid}', 'show')->name('show')->where('attribute', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{attribute:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::patch('/{attribute:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::delete('/{attribute:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::post('/{attribute:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
    Route::delete('/forcedDelete/{attribute:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('attribute', $uniqidRegex);
});
