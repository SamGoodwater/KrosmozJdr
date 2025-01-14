<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginFilterRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LoginController extends Controller
{
    use AuthorizesRequests;

    public function show(): \Inertia\Response
    {
        return inertia('Auth/Login');
    }

    public function login(LoginFilterRequest $request): RedirectResponse
    {
        // $credential = $request->validate();

        if (Auth::attempt($request)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', 'Vous êtes connecté avec succès.');
        }

        return back()->withErrors([
            'email' => 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('login.show');
    }

}
