<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ConditionController;

Route::prefix('condition')->name("condition.")->controller(ConditionController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{condition:uniqid}', 'show')->name('show')->where('condition', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{condition:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::patch('/{condition:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::delete('/{condition:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::post('/{condition:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
    Route::delete('/{condition:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('condition', $uniqidRegex);
});
