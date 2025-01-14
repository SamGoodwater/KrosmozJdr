<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\MobController;

Route::prefix('mob')->name("mob.")->controller(MobController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{mob:uniqid}', 'show')->name('show')->where('mob', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{mob:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::patch('/{mob:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::delete('/{mob:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::post('/{mob:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::delete('/forcedDelete/{mob:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
});
