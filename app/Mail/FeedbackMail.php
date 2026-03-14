<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\UploadedFile;

/**
 * Email envoyé aux admins lors d'un retour utilisateur (bug, erreur, suggestion, autre).
 *
 * @see resources/views/emails/feedback.blade.php
 * @see docs/00-Project/FEEDBACK_SYSTEM.md
 * @see docs/00-Project/EMAIL_SYSTEM.md
 */
class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $typeLabel,
        public string $feedbackMessage,
        public ?string $url = null,
        public ?string $pseudo = null,
        public ?UploadedFile $attachment = null,
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = '[' . config('app.name') . '] Retour utilisateur — ' . $this->typeLabel;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback',
            text: 'emails.feedback-text',
            with: [
                'typeLabel' => $this->typeLabel,
                'feedbackMessage' => $this->feedbackMessage,
                'url' => $this->url,
                'pseudo' => $this->pseudo,
                'hasAttachment' => $this->attachment !== null,
                'attachmentName' => $this->attachment?->getClientOriginalName(),
            ]
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->attachment !== null) {
            $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $this->attachment->get(),
                $this->attachment->getClientOriginalName()
            )->withMime($this->attachment->getMimeType());
        }

        return $attachments;
    }
}
