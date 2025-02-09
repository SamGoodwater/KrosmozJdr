<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\MobraceController;

Route::prefix('mobrace')->name("mobrace.")->group(function () use ($uniqidRegex) {
    Route::inertia('/', [MobraceController::class, 'index'])->name('index');
    Route::inertia('/{mobrace:uniqid}', [MobraceController::class, 'show'])->name('show')->where('mobrace', $uniqidRegex);
    Route::inertia('/create', [MobraceController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [MobraceController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{mobrace:uniqid}/edit', [MobraceController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::patch('/{mobrace:uniqid}', [MobraceController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::delete('/{mobrace:uniqid}', [MobraceController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::post('/{mobrace:uniqid}', [MobraceController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
    Route::delete('/forcedDelete/{mobrace:uniqid}', [MobraceController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('mobrace', $uniqidRegex);
});
