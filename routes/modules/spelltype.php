<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\SpelltypeController;

Route::prefix('spelltype')->name("spelltype.")->group(function () use ($uniqidRegex) {
    Route::get('/', [SpelltypeController::class, 'index'])->name('index');
    Route::get('/{spelltype:uniqid}', [SpelltypeController::class, 'show'])->name('show')->where('spelltype', $uniqidRegex);
    Route::get('/create', [SpelltypeController::class, 'create'])->middleware(['auth', 'verified'])->name('create');
    Route::post('/', [SpelltypeController::class, 'store'])->middleware(['auth', 'verified'])->name('store');
    Route::get('/{spelltype:uniqid}/edit', [SpelltypeController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::patch('/{spelltype:uniqid}', [SpelltypeController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::delete('/{spelltype:uniqid}', [SpelltypeController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::post('/{spelltype:uniqid}', [SpelltypeController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
    Route::delete('/forcedDelete/{spelltype:uniqid}', [SpelltypeController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('spelltype', $uniqidRegex);
});
