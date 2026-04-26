<?php

use App\Models\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    $joinPage = Page::query()->where('slug', 'nous-rejoindre')->first();
    if ($joinPage && $joinPage->canBeViewedBy(Auth::user())) {
        return redirect()->route('pages.show', $joinPage->slug);
    }

    return Inertia::render('Pages/statics/Contribute');
})->name('contribute');
