<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\CapabilityController;

Route::prefix('capability')->name("capability.")->group(function () use ($uniqidRegex) {
    Route::get('/', [CapabilityController::class, 'index'])->name('index');
    Route::get('/{capability:uniqid}', [CapabilityController::class, 'show'])->name('show')->where('capability', $uniqidRegex);
    Route::get('/create', [CapabilityController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [CapabilityController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{capability:uniqid}/edit', [CapabilityController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::patch('/{capability:uniqid}', [CapabilityController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::delete('/{capability:uniqid}', [CapabilityController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::post('/{capability:uniqid}', [CapabilityController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
    Route::delete('/forcedDelete/{capability:uniqid}', [CapabilityController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('capability', $uniqidRegex);
});
