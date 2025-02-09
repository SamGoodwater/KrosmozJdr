<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ShopController;

Route::prefix('shop')->name("shop.")->group(function () use ($uniqidRegex) {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/{shop:uniqid}', [ShopController::class, 'show'])->name('show')->where('shop', $uniqidRegex);
    Route::get('/create', [ShopController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [ShopController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{shop:uniqid}/edit', [ShopController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::patch('/{shop:uniqid}', [ShopController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::delete('/{shop:uniqid}', [ShopController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::post('/{shop:uniqid}', [ShopController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
    Route::delete('/forcedDelete/{shop:uniqid}', [ShopController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('shop', $uniqidRegex);
});
