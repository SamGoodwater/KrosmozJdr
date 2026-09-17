<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Page Inertia des favoris utilisateur.
 *
 * @example
 * Route::get('/favoris', [FavoritePageController::class, 'index']);
 */
class FavoritePageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Pages/favorites/Index', [
            'authRequired' => Auth::guest(),
        ]);
    }
}
