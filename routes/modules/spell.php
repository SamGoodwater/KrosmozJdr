<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SpellController;

Route::prefix('spell')->name("spell.")->group(function () use ($uniqidRegex) {
    Route::get('/', [SpellController::class, 'index'])->name('index');
    Route::get('/{spell:uniqid}', [SpellController::class, 'show'])->name('show')->where('spell', $uniqidRegex);
    Route::get('/create', [SpellController::class, 'create'])->middleware(['auth', 'verified'])->name('create');
    Route::post('/', [SpellController::class, 'store'])->middleware(['auth', 'verified'])->name('store');
    Route::get('/{spell:uniqid}/edit', [SpellController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::patch('/{spell:uniqid}', [SpellController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::delete('/{spell:uniqid}', [SpellController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::post('/{spell:uniqid}', [SpellController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
    Route::delete('/forcedDelete/{spell:uniqid}', [SpellController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('spell', $uniqidRegex);
});
