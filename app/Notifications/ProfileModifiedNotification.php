<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\NotificationService;

/**
 * Notification pour la modification du profil utilisateur.
 *
 * @property \App\Models\User $modifiedUser
 * @property \App\Models\User $modifier
 * @property array $channels
 */
class ProfileModifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $modifiedUser;
    public $modifier;
    public $channels;
    public $changes;

    /**
     * @param \App\Models\User $modifiedUser
     * @param \App\Models\User $modifier
     * @param array $channels
     */
    public function __construct($modifiedUser, $modifier, $channels = ['database', 'mail'], $changes = [])
    {
        $this->modifiedUser = $modifiedUser;
        $this->modifier = $modifier;
        $this->channels = $channels;
        $this->changes = $changes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Votre profil a été modifié')
            ->greeting('Bonjour !')
            ->line("Votre profil a été modifié par {$this->modifier->name}.");

        if (!empty($this->changes)) {
            $mail->line('Changements principaux :');
            $displayed = 0;
            foreach ($this->changes as $field => $change) {
                if ($displayed >= 3) break;
                $old = NotificationService::truncateAndSanitize($change['old']);
                $new = NotificationService::truncateAndSanitize($change['new']);
                $line = "• $field : '$old' → '$new'";
                if (!empty($change['image_url'])) {
                    $line .= " (voir l'image : " . $change['image_url'] . ")";
                }
                $mail->line($line);
                $displayed++;
            }
            if (count($this->changes) > 3) {
                $mail->line('...et d\'autres changements.');
            }
        }

        $mail->action('Voir mon profil', url("/users/{$this->modifiedUser->id}"))
            ->line('Si vous n\'êtes pas à l\'origine de cette modification, contactez un administrateur.');
        return $mail;
    }

    /**
     * Get the array representation of the notification (database).
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $changes = [];
        $displayed = 0;
        foreach ($this->changes as $field => $change) {
            if ($displayed >= 3) break;
            $changes[$field] = [
                'old' => NotificationService::truncateAndSanitize($change['old']),
                'new' => NotificationService::truncateAndSanitize($change['new']),
                'image_url' => $change['image_url'] ?? null,
            ];
            $displayed++;
        }
        $more_changes = count($this->changes) > 3;
        return [
            'modified_user_id' => $this->modifiedUser->id,
            'modifier_id' => $this->modifier->id,
            'modifier_name' => $this->modifier->name,
            'message' => "Votre profil a été modifié par {$this->modifier->name}.",
            'url' => url("/users/{$this->modifiedUser->id}"),
            'changes' => $changes,
            'more_changes' => $more_changes,
        ];
    }
}
