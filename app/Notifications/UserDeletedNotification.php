<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification admin : suppression d'un utilisateur.
 */
class UserDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $deletedUserId,
        public string $deletedUserName,
        public string $deletedUserEmail,
        public User $deleter
    ) {}

    public function via($notifiable): array
    {
        return $notifiable->getChannelsForNotificationType('user_deleted');
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Un utilisateur a été supprimé')
            ->greeting('Bonjour !')
            ->line("L'utilisateur {$this->deletedUserName} ({$this->deletedUserEmail}) a été supprimé par {$this->deleter->name}.")
            ->action('Voir les utilisateurs', url('/users'));
    }

    public function toArray($notifiable): array
    {
        return [
            'deleted_user_id' => $this->deletedUserId,
            'deleted_user_name' => $this->deletedUserName,
            'deleted_user_email' => $this->deletedUserEmail,
            'deleter_id' => $this->deleter->id,
            'deleter_name' => $this->deleter->name,
            'message' => "L'utilisateur {$this->deletedUserName} a été supprimé par {$this->deleter->name}.",
            'url' => url('/users'),
        ];
    }
}
