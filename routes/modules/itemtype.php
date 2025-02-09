<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ItemtypeController;

Route::prefix('itemtype')->name("itemtype.")->group(function () use ($uniqidRegex) {
    Route::get('/', [ItemtypeController::class, 'index'])->name('index');
    Route::get('/create', [ItemtypeController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::get('/{itemtype:uniqid}', [ItemtypeController::class, 'show'])->name('show')->where('itemtype', $uniqidRegex);
    Route::get('/{itemtype:uniqid}/edit', [ItemtypeController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::post('/', [ItemtypeController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::patch('/{itemtype:uniqid}', [ItemtypeController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::delete('/{itemtype:uniqid}', [ItemtypeController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::post('/{itemtype:uniqid}', [ItemtypeController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
    Route::delete('/forcedDelete/{itemtype:uniqid}', [ItemtypeController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('itemtype', $uniqidRegex);
});
