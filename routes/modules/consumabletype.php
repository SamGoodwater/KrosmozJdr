<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ConsumabletypeController;

Route::prefix('consumabletype')->name("consumabletype.")->group(function () use ($uniqidRegex) {
    Route::get('/', [ConsumabletypeController::class, 'index'])->name('index');
    Route::get('/{consumabletype:uniqid}', [ConsumabletypeController::class, 'show'])->name('show')->where('consumabletype', $uniqidRegex);
    Route::get('/create', [ConsumabletypeController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [ConsumabletypeController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{consumabletype:uniqid}/edit', [ConsumabletypeController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::patch('/{consumabletype:uniqid}', [ConsumabletypeController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::delete('/{consumabletype:uniqid}', [ConsumabletypeController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::post('/{consumabletype:uniqid}', [ConsumabletypeController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
    Route::delete('/forcedDelete/{consumabletype:uniqid}', [ConsumabletypeController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('consumabletype', $uniqidRegex);
});
