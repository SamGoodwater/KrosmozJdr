<?php

namespace App\Notifications;

use App\Mail\VerifyEmailMail;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

/**
 * Notification de vérification d'email pour les comptes classiques.
 *
 * Remplace la notification Laravel par défaut pour utiliser notre Mailable
 * et layout emails.
 *
 * @see docs/00-Project/EMAIL_SYSTEM.md
 */
class VerifyEmailNotification extends VerifyEmailBase implements ShouldQueue
{
    use Queueable;

    /**
     * Envoie le mail via notre Mailable personnalisé.
     */
    public function toMail($notifiable): VerifyEmailMail
    {
        $mail = new VerifyEmailMail($notifiable);

        return $mail;
    }
}
