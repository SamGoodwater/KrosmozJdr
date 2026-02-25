<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web — Médias (images, thumbnails)
|--------------------------------------------------------------------------
*/

Route::prefix('media')->group(function () {
    Route::get('/images/{path}', [ImageController::class, 'show'])
        ->where('path', '.*')
        ->name('media.show');
    Route::get('/thumbnails/{path}', [ImageController::class, 'thumbnail'])
        ->where('path', '.*')
        ->name('media.thumbnail');
    Route::post('/clean-thumbnails', [ImageController::class, 'cleanThumbnails'])
        ->name('media.clean-thumbnails');
});
