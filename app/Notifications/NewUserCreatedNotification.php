<?php

namespace App\Notifications;

use App\Mail\NotificationMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notification admin : nouveau compte créé (inscription).
 */
class NewUserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $newUser
    ) {}

    public function via($notifiable): array
    {
        return $notifiable->getChannelsForNotificationType('new_account_registered');
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouveau compte créé sur Krosmoz JDR')
            ->greeting('Bonjour !')
            ->line("Un nouveau compte a été créé : {$this->newUser->name} ({$this->newUser->email}).")
            ->action('Voir les utilisateurs', url('/users'));
    }

    public function toArray($notifiable): array
    {
        return [
            'new_user_id' => $this->newUser->id,
            'new_user_name' => $this->newUser->name,
            'new_user_email' => $this->newUser->email,
            'message' => "Nouveau compte créé : {$this->newUser->name} ({$this->newUser->email}).",
            'url' => url('/users'),
        ];
    }
}
