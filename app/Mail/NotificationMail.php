<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable générique pour les notifications métier (modification entité, profil, etc.).
 *
 * Utilise le layout emails commun pour une cohérence visuelle avec VerifyEmailMail.
 * Compatible avec les notifications Laravel (retourné par toMail()).
 *
 * @see resources/views/emails/notification.blade.php
 * @see docs/00-Project/EMAIL_SYSTEM.md
 */
class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /** Sujet de l'email (nom différent de Mailable::$subject pour éviter conflit de typage). */
    public string $mailSubject;

    /** @param string $subject Sujet de l'email */
    public function __construct(
        string $subject,
        public string $greeting,
        public array $lines = [],
        public ?string $actionUrl = null,
        public ?string $actionText = null,
        public ?string $footer = null,
    ) {
        $this->mailSubject = $subject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->mailSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            text: 'emails.notification-text',
            with: [
                'subject' => $this->mailSubject,
                'greeting' => $this->greeting,
                'lines' => $this->lines,
                'actionUrl' => $this->actionUrl,
                'actionText' => $this->actionText,
                'footer' => $this->footer,
            ]
        );
    }
}
