<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\CampaignController;

Route::prefix('campaign')->name("campaign.")->group(function () use ($uniqidRegex, $slugRegex) {
    Route::get('/', [CampaignController::class, 'index'])->name('index');
    Route::get('/{campaign:slug}', [CampaignController::class, 'show'])->name('show')->where('campaign', $slugRegex);
    Route::get('/create', [CampaignController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/', [CampaignController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{campaign:slug}/edit', [CampaignController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('campaign', $slugRegex);
    Route::patch('/{campaign:uniqid}', [CampaignController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
    Route::delete('/{campaign:uniqid}', [CampaignController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
    Route::post('/{campaign:uniqid}', [CampaignController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
    Route::delete('/forcedDelete/{campaign:uniqid}', [CampaignController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('campaign', $uniqidRegex);
});
