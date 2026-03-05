<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Page;

/*
|--------------------------------------------------------------------------
| Web — Pages statiques (accueil, contribute, etc.)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $homePage = Page::query()->where('slug', 'accueil')->first();
    if ($homePage && $homePage->canBeViewedBy(Auth::user())) {
        return redirect()->route('pages.show', $homePage->slug);
    }

    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

Route::get('/contribuer', function () {
    return Inertia::render('Statics/contribute');
})->name('contribute');
