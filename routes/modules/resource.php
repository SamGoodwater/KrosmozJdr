<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ResourceController;

Route::prefix('resource')->name("resource.")->controller(ResourceController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{resource:uniqid}', 'show')->name('show')->where('resource', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{resource:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('resource', $uniqidRegex);
    Route::patch('/{resource:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('resource', $uniqidRegex);
    Route::delete('/{resource:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('resource', $uniqidRegex);
    Route::post('/{resource:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('resource', $uniqidRegex);
    Route::delete('/forcedDelete/{resource:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('resource', $uniqidRegex);
});
