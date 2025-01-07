<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SpellController;

Route::prefix('spell')->name("spell.")->controller(SpellController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{spell:uniqid}', 'show')->name('show')->where('spell', $uniqidRegex);
    Route::inertia('/create', 'create')->middleware(['auth', 'verified'])->name('create');
    Route::post('/', 'store')->middleware(['auth', 'verified'])->name('store');
    Route::inertia('/{spell:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::patch('/{spell:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::delete('/{spell:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::post('/{spell:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::delete('/{spell:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
});
