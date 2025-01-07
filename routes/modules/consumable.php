<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ConsumableController;

Route::prefix('consumable')->name("consumable.")->controller(ConsumableController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{consumable:uniqid}', 'show')->name('show')->where('consumable', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{consumable:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::patch('/{consumable:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::delete('/{consumable:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::post('/{consumable:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
    Route::delete('/{consumable:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('consumable', $uniqidRegex);
});
