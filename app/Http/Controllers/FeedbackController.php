<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Mail\FeedbackMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

/**
 * Contrôleur pour les retours utilisateur (bugs, erreurs, suggestions).
 *
 * Envoie un email aux admins. Accessible sans authentification.
 *
 * @see routes/web/feedback.php
 * @see docs/00-Project/FEEDBACK_SYSTEM.md
 */
class FeedbackController extends Controller
{
    /**
     * Libellés des types de feedback pour l'email.
     *
     * @var array<string, string>
     */
    private const TYPE_LABELS = [
        'bug' => 'Bug',
        'error' => 'Erreur',
        'suggestion' => 'Suggestion',
        'other' => 'Autre',
    ];

    /**
     * Enregistre un retour utilisateur et envoie un email aux admins.
     */
    public function store(StoreFeedbackRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $type = (string) $validated['type'];
        $typeLabel = self::TYPE_LABELS[$type] ?? $type;

        $recipients = User::query()
            ->where('role', '>=', User::ROLE_ADMIN)
            ->pluck('email')
            ->filter()
            ->values()
            ->all();

        if (empty($recipients)) {
            $fallback = config('feedback.fallback_email') ?? config('mail.from.address');
            if ($fallback) {
                $recipients = [$fallback];
            }
        }

        if (empty($recipients)) {
            return back()->with('error', 'Impossible d\'envoyer le message. Aucun destinataire configuré.');
        }

        try {
            $mailable = new FeedbackMail(
                typeLabel: $typeLabel,
                feedbackMessage: (string) $validated['message'],
                url: $validated['url'] ?? null,
                pseudo: $validated['pseudo'] ?? null,
                attachment: $validated['attachment'] ?? null,
            );

            Mail::to($recipients)->send($mailable);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Une erreur est survenue lors de l\'envoi. Réessaie plus tard.');
        }

        return back()->with('success', 'Merci pour ton retour ! Il a bien été envoyé aux administrateurs.');
    }
}
