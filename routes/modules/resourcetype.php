<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ResourcetypeController;

Route::prefix('resourcetype')->name("resourcetype.")->group(function () use ($uniqidRegex) {
    Route::get('/', [ResourcetypeController::class, 'index'])->name('index');
    Route::get('/{resourcetype:uniqid}', [ResourcetypeController::class, 'show'])->name('show')->where('resourcetype', $uniqidRegex);
    Route::get('/create', [ResourcetypeController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [ResourcetypeController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{resourcetype:uniqid}/edit', [ResourcetypeController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('resourcetype', $uniqidRegex);
    Route::patch('/{resourcetype:uniqid}', [ResourcetypeController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('resourcetype', $uniqidRegex);
    Route::delete('/{resourcetype:uniqid}', [ResourcetypeController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('resourcetype', $uniqidRegex);
    Route::post('/{resourcetype:uniqid}', [ResourcetypeController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('resourcetype', $uniqidRegex);
    Route::delete('/forcedDelete/{resourcetype:uniqid}', [ResourcetypeController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('resourcetype', $uniqidRegex);
});
