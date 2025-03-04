<?php

use App\Http\Controllers\Api\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('/media/{type?}', [MediaController::class, 'index'])->name('api.media.index');
    Route::get('/media/{type}/{directory}/{name}', [MediaController::class, 'show'])->name('api.media.show');
    Route::post('/media/refresh-cache', [MediaController::class, 'refreshCache'])->name('api.media.refresh-cache');
});
