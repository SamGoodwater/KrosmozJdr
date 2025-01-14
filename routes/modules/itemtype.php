<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ItemtypeController;

Route::prefix('itemtype')->name("itemtype.")->controller(ItemtypeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{itemtype:uniqid}', 'show')->name('show')->where('itemtype', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{itemtype:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::patch('/{itemtype:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::delete('/{itemtype:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::post('/{itemtype:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::delete('/forcedDelete/{itemtype:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
});
