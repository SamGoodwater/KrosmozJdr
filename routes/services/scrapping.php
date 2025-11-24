<?php

use Illuminate\Support\Facades\Route;

// SCRAPPING
Route::middleware(['auth'])->group(function () {
    Route::get('/scrapping', [App\Http\Controllers\Scrapping\ScrappingDashboardController::class, 'index'])
        ->name('scrapping.index');
});