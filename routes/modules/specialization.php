<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SpecializationController;

Route::prefix('specialization')->name("specialization.")->controller(SpecializationController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{specialization:uniqid}', 'show')->name('show')->where('specialization', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{specialization:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::patch('/{specialization:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::delete('/{specialization:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::post('/{specialization:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
    Route::delete('/forcedDelete/{specialization:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('specialization', $uniqidRegex);
});
