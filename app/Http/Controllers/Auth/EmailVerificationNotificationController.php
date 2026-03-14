<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('user.show', $user, absolute: false));
        }

        if (empty(trim((string) ($user->getEmailForVerification() ?? '')))) {
            return redirect()->route('verification.notice')
                ->with('error', 'Impossible d\'envoyer l\'email : aucune adresse associée à ce compte.');
        }

        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice')
            ->with('status', 'verification-link-sent')
            ->with('success', 'Un nouveau lien de vérification a été envoyé à ton adresse email.');
    }
}
