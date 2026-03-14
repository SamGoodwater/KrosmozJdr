<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
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
 * Envoi synchrone pour que l'utilisateur puisse récupérer le lien immédiatement (logs).
 *
 * @see docs/00-Project/EMAIL_SYSTEM.md
 */
class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        $email = trim((string) ($this->user->getEmailForVerification() ?? ''));
        if ($email === '') {
            throw new \InvalidArgumentException('Cannot send verification email: user has no email address.');
        }

        return new Envelope(
            to: [$email],
            subject: 'Vérifie ton adresse email',
        );
    }

    public function content(): Content
    {
        $verificationUrl = $this->verificationUrl();

        return new Content(
            view: 'emails.verify-email',
            text: 'emails.verify-email-text',
            with: [
                'userName' => $this->user->name,
                'verificationUrl' => $verificationUrl,
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
