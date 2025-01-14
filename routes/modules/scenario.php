<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ScenarioController;

Route::prefix('scenario')->name("scenario.")->controller(ScenarioController::class)->group(function () use ($uniqidRegex, $slugRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{scenario:slug}', 'show')->name('show')->where('scenario', $slugRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{scenario:slug}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('scenario', $slugRegex);
    Route::patch('/{scenario:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
    Route::delete('/{scenario:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
    Route::post('/{scenario:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
    Route::delete('/forcedDelete/{scenario:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('scenario', $uniqidRegex);
});
