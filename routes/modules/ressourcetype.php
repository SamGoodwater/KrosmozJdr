<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\RessourcetypeController;

Route::prefix('ressourcetype')->name("ressourcetype.")->controller(RessourcetypeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{ressourcetype:uniqid}', 'show')->name('show')->where('ressourcetype', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{ressourcetype:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('ressourcetype', $uniqidRegex);
    Route::patch('/{ressourcetype:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('ressourcetype', $uniqidRegex);
    Route::delete('/{ressourcetype:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('ressourcetype', $uniqidRegex);
    Route::post('/{ressourcetype:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->middleware(['auth', 'verified'])->where('ressourcetype', $uniqidRegex);
    Route::delete('/forcedDelete/{ressourcetype:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('ressourcetype', $uniqidRegex);
});
