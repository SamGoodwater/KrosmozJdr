<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;
// Routes publiques pour les médias
Route::prefix('media')->group(function () {
    // Afficher une image
    Route::get('/images/{path}', [ImageController::class, 'show'])
        ->where('path', '.*')
        ->name('media.show');

    // Générer un thumbnail
    Route::get('/thumbnails/{path}', [ImageController::class, 'thumbnail'])
        ->where('path', '.*')
        ->name('media.thumbnail');

    // Nettoyer les thumbnails
    Route::post('/clean-thumbnails', [ImageController::class, 'cleanThumbnails'])
        ->name('media.clean-thumbnails');
});
