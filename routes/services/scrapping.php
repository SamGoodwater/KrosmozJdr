<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Auth;

// SCRAPPING
Route::middleware(['auth'])->group(function () {
    Route::get('/scrapping', [App\Http\Controllers\Scrapping\ScrappingDashboardController::class, 'index'])
        ->name('scrapping.index');
});