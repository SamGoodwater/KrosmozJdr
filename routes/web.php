<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Auth;

$slugRegex = '[a-z0-9]+(?:-[a-z0-9]+)*';

include_once __DIR__ . '/auth.php';
require __DIR__ . '/user.php';

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
require_once __DIR__ . '/page.php';

// ENTITIES
// Les routes utilisent désormais l'id numérique pour toutes les entités sauf show page (slug pour SEO).
