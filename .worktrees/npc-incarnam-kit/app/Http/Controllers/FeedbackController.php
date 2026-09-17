<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Mail\FeedbackMail;
use App\Mail\FeedbackRecapMail;
use App\Models\FeedbackMessage;
use App\Models\FeedbackThread;
use App\Models\User;
use App\Notifications\FeedbackThreadNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Contrôleur pour les retours utilisateur (bugs, erreurs, suggestions).
 *
 * Envoie un email aux admins. Accessible sans authentification.
 *
 * @see routes/web/feedback.php
 * @see docs/features/feedback/README.md
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

        $user = $request->user();
        if ($user !== null) {
            return $this->storeAuthenticatedFeedback($request, $validated, $user);
        }

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

    /**
     * Crée une conversation persistante pour les utilisateurs connectés.
     *
     * @param  array<string, mixed>  $validated
     */
    private function storeAuthenticatedFeedback(StoreFeedbackRequest $request, array $validated, User $user): RedirectResponse
    {
        $type = (string) $validated['type'];
        $message = (string) $validated['message'];
        $attachment = $validated['attachment'] ?? null;
        $attachmentPath = $attachment ? $attachment->store('feedback/attachments', 'public') : null;

        $thread = DB::transaction(function () use ($user, $type, $validated, $message, $attachmentPath, $attachment): FeedbackThread {
            $thread = FeedbackThread::create([
                'user_id' => $user->id,
                'type' => $type,
                'status' => FeedbackThread::STATUS_OPEN,
                'url' => $validated['url'] ?? null,
                'subject_preview' => str($message)->squish()->limit(160)->toString(),
                'last_message_at' => now(),
                'staff_unread_count' => 1,
            ]);

            FeedbackMessage::create([
                'feedback_thread_id' => $thread->id,
                'author_id' => $user->id,
                'author_role' => FeedbackMessage::AUTHOR_USER,
                'body' => $message,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachment?->getClientOriginalName(),
            ]);

            return $thread;
        });

        try {
            User::query()
                ->where('role', '>=', User::ROLE_ADMIN)
                ->where('id', '!=', $user->id)
                ->get()
                ->each(function (User $admin) use ($thread, $user): void {
                    if ($admin->wantsNotificationForType('feedback_new_thread')) {
                        $admin->notify(new FeedbackThreadNotification($thread, $user, 'feedback_new_thread'));
                    }
                });

            if ($request->boolean('email_recap') && is_string($user->email) && trim($user->email) !== '') {
                Mail::to($user->email)->send(new FeedbackRecapMail(
                    typeLabel: self::TYPE_LABELS[$type] ?? $type,
                    feedbackMessage: $message,
                    url: $validated['url'] ?? null,
                ));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('feedback.show', $thread)
            ->with('success', 'Merci pour ton retour ! Une conversation a été créée.');
    }
}
