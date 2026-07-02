<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Copie récapitulative envoyée à l'utilisateur connecté après un retour feedback.
 *
 * @see FeedbackMail
 * @see docs/features/feedback/README.md
 */
class FeedbackRecapMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $typeLabel,
        public string $feedbackMessage,
        public ?string $url = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '['.config('app.name').'] Copie de ton retour — '.$this->typeLabel,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback-recap',
            text: 'emails.feedback-recap-text',
            with: [
                'typeLabel' => $this->typeLabel,
                'feedbackMessage' => $this->feedbackMessage,
                'url' => $this->url,
            ],
        );
    }
}
