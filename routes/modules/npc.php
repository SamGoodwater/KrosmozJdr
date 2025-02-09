<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\NpcController;

Route::prefix('npc')->name("npc.")->group(function () use ($uniqidRegex) {
    Route::get('/', [NpcController::class, 'index'])->name('index');
    Route::get('/{npc:uniqid}', [NpcController::class, 'show'])->name('show')->where('npc', $uniqidRegex);
    Route::get('/create', [NpcController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [NpcController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{npc:uniqid}/edit', [NpcController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::patch('/{npc:uniqid}', [NpcController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::delete('/{npc:uniqid}', [NpcController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::post('/{npc:uniqid}', [NpcController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::delete('/forcedDelete/{npc:uniqid}', [NpcController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
});
