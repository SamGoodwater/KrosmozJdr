<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\RessourceController;

Route::prefix('ressource')->name("ressource.")->controller(RessourceController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{ressource:uniqid}', 'show')->name('show')->where('ressource', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{ressource:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('ressource', $uniqidRegex);
    Route::patch('/{ressource:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('ressource', $uniqidRegex);
    Route::delete('/{ressource:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('ressource', $uniqidRegex);
    Route::post('/{ressource:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('ressource', $uniqidRegex);
    Route::delete('/{ressource:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('ressource', $uniqidRegex);
});
