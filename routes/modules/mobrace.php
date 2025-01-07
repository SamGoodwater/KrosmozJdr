<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\MobraceController;

Route::prefix('mobrace')->name("mobrace.")->controller(MobraceController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{mobrace:uniqid}', 'show')->name('show')->where('mobrace', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{mobrace:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::patch('/{mobrace:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::delete('/{mobrace:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::post('/{mobrace:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::delete('/{mobrace:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
});
