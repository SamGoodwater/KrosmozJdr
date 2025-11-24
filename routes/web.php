<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/auth.php';
require __DIR__ . '/user.php';
require __DIR__ . '/file.php';

// Routes publiques
Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
})->name('home');

// STATICS
Route::get('/contribuer', function () {
    return Inertia::render('Statics/contribute');
})->name('contribute');

// PAGES ET SECTIONS
require __DIR__ . '/page.php';

// SERVICES
require __DIR__ . '/services/scrapping.php';