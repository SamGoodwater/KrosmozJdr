<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SpelltypeController;

Route::prefix('spelltype')->name("spelltype.")->controller(SpelltypeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{spelltype:uniqid}', 'show')->name('show')->where('spelltype', $uniqidRegex);
    Route::inertia('/create', 'create')->middleware(['auth', 'verified'])->name('create');
    Route::post('/', 'store')->middleware(['auth', 'verified'])->name('store');
    Route::inertia('/{spelltype:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::patch('/{spelltype:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::delete('/{spelltype:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::post('/{spelltype:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::delete('/forcedDelete/{spelltype:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
});
