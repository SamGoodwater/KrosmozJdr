<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ShopController;

Route::prefix('shop')->name("shop.")->controller(ShopController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{shop:uniqid}', 'show')->name('show')->where('shop', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{shop:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::patch('/{shop:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::delete('/{shop:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::post('/{shop:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::delete('/{shop:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
});
