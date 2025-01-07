<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\CampaignController;

Route::prefix('campaign')->name("campaign.")->controller(CampaignController::class)->group(function () use ($uniqidRegex, $slugRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{campaign:slug}', 'show')->name('show')->where('campaign', $slugRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{campaign:slug}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('campaign', $slugRegex);
    Route::patch('/{campaign:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
    Route::delete('/{campaign:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
    Route::post('/{campaign:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
    Route::delete('/{campaign:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
});
