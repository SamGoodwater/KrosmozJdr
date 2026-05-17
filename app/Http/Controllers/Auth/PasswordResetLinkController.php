<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Affiche la page de demande de lien de réinitialisation de mot de passe.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Pages/auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
            'statusType' => $request->session()->get('statusType', 'success'),
        ]);
    }

    /**
     * Traite une demande de lien de réinitialisation.
     * N'envoie pas de lien si le compte est OAuth-only (Discord, Steam, GitHub).
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($user && ! $user->hasPassword()) {
            return back()->with([
                'status' => __('Ce compte utilise une connexion via Discord, Steam ou GitHub. Connecte-toi avec l’un de ces services.'),
                'statusType' => 'info',
            ]);
        }

        Password::sendResetLink($request->only('email'));

        return back()->with([
            'status' => __('Si un compte existe avec cet email, un lien de réinitialisation t’a été envoyé.'),
            'statusType' => 'success',
        ]);
    }
}
