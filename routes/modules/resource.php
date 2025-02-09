<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ResourceController;

Route::prefix('resource')->name("resource.")->group(function () use ($uniqidRegex) {
    Route::inertia('/', [ResourceController::class, 'index'])->name('index');
    Route::inertia('/{resource:uniqid}', [ResourceController::class, 'show'])->name('show')->where('resource', $uniqidRegex);
    Route::inertia('/create', [ResourceController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [ResourceController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{resource:uniqid}/edit', [ResourceController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('resource', $uniqidRegex);
    Route::patch('/{resource:uniqid}', [ResourceController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('resource', $uniqidRegex);
    Route::delete('/{resource:uniqid}', [ResourceController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('resource', $uniqidRegex);
    Route::post('/{resource:uniqid}', [ResourceController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('resource', $uniqidRegex);
    Route::delete('/forcedDelete/{resource:uniqid}', [ResourceController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('resource', $uniqidRegex);
});
