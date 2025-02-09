<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\MobController;

Route::prefix('mob')->name("mob.")->group(function () use ($uniqidRegex) {
    Route::get('/', [MobController::class, 'index'])->name('index');
    Route::get('/{mob:uniqid}', [MobController::class, 'show'])->name('show')->where('mob', $uniqidRegex);
    Route::get('/create', [MobController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [MobController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{mob:uniqid}/edit', [MobController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::patch('/{mob:uniqid}', [MobController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::delete('/{mob:uniqid}', [MobController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::post('/{mob:uniqid}', [MobController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
    Route::delete('/forcedDelete/{mob:uniqid}', [MobController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('mob', $uniqidRegex);
});
