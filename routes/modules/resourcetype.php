<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ResourcetypeController;

Route::prefix('resourcetype')->name("resourcetype.")->controller(ResourcetypeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{resourcetype:uniqid}', 'show')->name('show')->where('resourcetype', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{resourcetype:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('resourcetype', $uniqidRegex);
    Route::patch('/{resourcetype:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('resourcetype', $uniqidRegex);
    Route::delete('/{resourcetype:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('resourcetype', $uniqidRegex);
    Route::post('/{resourcetype:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->middleware(['auth', 'verified'])->where('resourcetype', $uniqidRegex);
    Route::delete('/forcedDelete/{resourcetype:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('resourcetype', $uniqidRegex);
});
