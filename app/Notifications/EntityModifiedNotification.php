<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\NotificationMail;
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

    /** @var string|null URL vers l'entité (page/section avec slug si fourni) */
    public $url;

    /**
     * @param string $entityType
     * @param int $entityId
     * @param string $entityName
     * @param \App\Models\User $modifier
     * @param array $channels
     * @param array $changes
     * @param string|null $url
     */
    public function __construct($entityType, $entityId, $entityName, $modifier, $channels = ['database'], $changes = [], $url = null)
    {
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->entityName = $entityName;
        $this->modifier = $modifier;
        $this->channels = $channels;
        $this->changes = $changes;
        $this->url = $url;
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
        $linkUrl = $this->url ?? url("/{$this->entityType}/{$this->entityId}");
        $lines = ["L'entité {$this->entityType} : '{$this->entityName}' (ID: {$this->entityId}) a été modifiée par {$this->modifier->name}."];
        if (!empty($this->changes)) {
            $lines[] = 'Changements principaux :';
            $displayed = 0;
            foreach ($this->changes as $field => $change) {
                if ($displayed >= 3) break;
                $old = NotificationService::truncateAndSanitize($change['old']);
                $new = NotificationService::truncateAndSanitize($change['new']);
                $line = "• $field : '$old' → '$new'";
                if (!empty($change['image_url'])) {
                    $line .= " (voir l'image : " . $change['image_url'] . ")";
                }
                $lines[] = $line;
                $displayed++;
            }
            if (count($this->changes) > 3) {
                $lines[] = '...et d\'autres changements.';
            }
        }
        return new NotificationMail(
            subject: 'Modification d\'une entité',
            greeting: 'Bonjour !',
            lines: $lines,
            actionUrl: $linkUrl,
            actionText: 'Voir l\'entité',
            footer: 'Merci d\'utiliser Krosmoz JDR.',
        );
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
        $linkUrl = $this->url ?? url("/{$this->entityType}/{$this->entityId}");
        return [
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'entity_name' => $this->entityName,
            'modifier_id' => $this->modifier->id,
            'modifier_name' => $this->modifier->name,
            'message' => "L'entité {$this->entityType} : '{$this->entityName}' a été modifiée par {$this->modifier->name}.",
            'url' => $linkUrl,
            'changes' => $changes,
            'more_changes' => $more_changes,
        ];
    }
}
