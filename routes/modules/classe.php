<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ClasseController;

Route::prefix('classe')->name("classe.")->controller(ClasseController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{class:uniqid}', 'show')->name('show')->where('classe', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{classe:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::patch('/{classe:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::delete('/{classe:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::post('/{classe:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
    Route::delete('/{classe:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('classe', $uniqidRegex);
});
