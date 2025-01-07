<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\CapabilityController;

Route::prefix('capability')->name("capability.")->controller(CapabilityController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{capability:uniqid}', 'show')->name('show')->where('capability', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{capability:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::patch('/{capability:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::delete('/{capability:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::post('/{capability:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::delete('/{capability:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
});
