<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

/**
 * Email de vérification d'adresse pour les comptes classiques.
 *
 * Envoie un lien signé permettant de marquer l'email comme vérifié.
 * Utilisé par VerifyEmailNotification (envoi déclenché par MustVerifyEmail).
 *
 * @see docs/00-Project/EMAIL_SYSTEM.md
 */
class VerifyEmailMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vérifie ton adresse email',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-email',
            with: [
                'userName' => $this->user->name,
                'verificationUrl' => $this->verificationUrl(),
            ]
        );
    }

    /**
     * URL signée temporaire pour la vérification (60 min, compatible Auth).
     */
    protected function verificationUrl(): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes((int) config('auth.verification.expire', 60)),
            [
                'id' => $this->user->getKey(),
                'hash' => sha1($this->user->getEmailForVerification()),
            ]
        );
    }
}
