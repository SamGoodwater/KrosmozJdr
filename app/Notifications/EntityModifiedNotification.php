<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\NotificationService;

/**
 * Notification générique pour la modification d'une entité (page, section, campagne, etc.).
 *
 * @property string $entityType
 * @property int $entityId
 * @property string $entityName
 * @property \App\Models\User $modifier
 * @property array $channels
 * @property array $changes
 */
class EntityModifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $entityType;
    public $entityId;
    public $entityName;
    public $modifier;
    public $channels;
    public $changes;

    /**
     * @param string $entityType
     * @param int $entityId
     * @param string $entityName
     * @param \App\Models\User $modifier
     * @param array $channels
     * @param array $changes
     */
    public function __construct($entityType, $entityId, $entityName, $modifier, $channels = ['database'], $changes = [])
    {
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->entityName = $entityName;
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
            ->subject('Modification d\'une entité')
            ->greeting('Bonjour !')
            ->line("L'entité {$this->entityType} : '{$this->entityName}' (ID: {$this->entityId}) a été modifiée par {$this->modifier->name}.");

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

        $mail->action('Voir l\'entité', url("/{$this->entityType}/{$this->entityId}"))
            ->line('Merci d\'utiliser Krosmoz JDR.');
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
            ];
            $displayed++;
        }
        $more_changes = count($this->changes) > 3;
        return [
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'entity_name' => $this->entityName,
            'modifier_id' => $this->modifier->id,
            'modifier_name' => $this->modifier->name,
            'message' => "L'entité {$this->entityType} : '{$this->entityName}' a été modifiée par {$this->modifier->name}.",
            'url' => url("/{$this->entityType}/{$this->entityId}"),
            'changes' => $changes,
            'more_changes' => $more_changes,
        ];
    }
}
