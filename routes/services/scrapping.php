<?php

use Illuminate\Support\Facades\Route;

// SCRAPPING — accès réservé aux administrateurs (lecture et écriture)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/scrapping', [App\Http\Controllers\Scrapping\ScrappingDashboardController::class, 'index'])
        ->name('scrapping.index');
});