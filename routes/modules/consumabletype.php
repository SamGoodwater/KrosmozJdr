<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ConsumabletypeController;

Route::prefix('consumabletype')->name("consumabletype.")->controller(ConsumabletypeController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{consumabletype:uniqid}', 'show')->name('show')->where('consumabletype', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{consumabletype:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::patch('/{consumabletype:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::delete('/{consumabletype:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::post('/{consumabletype:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::delete('/{consumabletype:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
});
