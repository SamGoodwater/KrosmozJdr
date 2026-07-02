<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password page.
     */
    public function show(): Response
    {
        return Inertia::render('auth/ConfirmPassword');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if ($user->password === null || $user->password === '') {
            return back()->withErrors([
                'password' => 'Ce compte n\'a pas de mot de passe local. Utilise ta connexion OAuth ou définis un mot de passe dans Mon compte.',
            ]);
        }

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => __('auth.password'),
            ]);
        }

        $now = time();
        $request->session()->put('auth.password_confirmed_at', $now);
        $request->session()->put('auth.password_last_activity_at', $now);

        return redirect()->intended('/');
    }
}
