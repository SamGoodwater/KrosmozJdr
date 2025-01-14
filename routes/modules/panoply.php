<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\PanoplyController;

Route::prefix('panoply')->name("panoply.")->controller(PanoplyController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{panoply:uniqid}', 'show')->name('show')->where('panoply', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{panoply:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::patch('/{panoply:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::delete('/{panoply:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::post('/{panoply:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::delete('/forcedDelete/{panoply:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
});
