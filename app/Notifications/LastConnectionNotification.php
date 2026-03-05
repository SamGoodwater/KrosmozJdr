<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification : enregistrement de la dernière connexion (info pour l'utilisateur).
 */
class LastConnectionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $loggedAtIso
    ) {}

    public function via($notifiable): array
    {
        return $notifiable->getChannelsForNotificationType('last_connection');
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Connexion enregistrée')
            ->greeting('Bonjour !')
            ->line('Ta connexion a été enregistrée le ' . $this->loggedAtIso . '.');
    }

    public function toArray($notifiable): array
    {
        return [
            'logged_at' => $this->loggedAtIso,
            'message' => 'Connexion enregistrée le ' . $this->loggedAtIso . '.',
            'url' => url('/user'),
        ];
    }
}
