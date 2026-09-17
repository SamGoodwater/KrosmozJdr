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
    public function show(Request $request): Response
    {
        return Inertia::render('auth/ConfirmPassword', [
            'intendedUrl' => $request->session()->get('url.intended', url('/')),
        ]);
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if ($user->password === null || $user->password === '') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Ce compte n\'a pas de mot de passe local.',
                    'errors' => [
                        'password' => ['Ce compte n\'a pas de mot de passe local. Utilise ta connexion OAuth ou définis un mot de passe dans Mon compte.'],
                    ],
                ], 422);
            }

            return back()->withErrors([
                'password' => 'Ce compte n\'a pas de mot de passe local. Utilise ta connexion OAuth ou définis un mot de passe dans Mon compte.',
            ]);
        }

        if (! Hash::check($request->password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('auth.password'),
                    'errors' => ['password' => [__('auth.password')]],
                ], 422);
            }

            return back()->withErrors([
                'password' => __('auth.password'),
            ]);
        }

        $now = time();
        $request->session()->put('auth.password_confirmed_at', $now);
        $request->session()->put('auth.password_last_activity_at', $now);

        $redirectUrl = redirect()->intended('/')->getTargetUrl();

        if ($request->expectsJson()) {
            return response()->json([
                'confirmed' => true,
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->intended('/');
    }
}
