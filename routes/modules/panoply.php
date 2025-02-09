<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\PanoplyController;

Route::prefix('panoply')->name("panoply.")->group(function () use ($uniqidRegex) {
    Route::get('/', [PanoplyController::class, 'index'])->name('index');
    Route::get('/{panoply:uniqid}', [PanoplyController::class, 'show'])->name('show')->where('panoply', $uniqidRegex);
    Route::get('/create', [PanoplyController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [PanoplyController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{panoply:uniqid}/edit', [PanoplyController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::patch('/{panoply:uniqid}', [PanoplyController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::delete('/{panoply:uniqid}', [PanoplyController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::post('/{panoply:uniqid}', [PanoplyController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
    Route::delete('/forcedDelete/{panoply:uniqid}', [PanoplyController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('panoply', $uniqidRegex);
});
