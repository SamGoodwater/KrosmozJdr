<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\NpcController;

Route::prefix('npc')->name("npc.")->controller(NpcController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{npc:uniqid}', 'show')->name('show')->where('npc', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{npc:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::patch('/{npc:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::delete('/{npc:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::post('/{npc:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
    Route::delete('/{npc:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('npc', $uniqidRegex);
});
