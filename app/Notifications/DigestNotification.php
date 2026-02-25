<?php

namespace App\Notifications;

use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification groupée (digest) : récap d'événements par type (quotidien, hebdo, mensuel).
 *
 * @property string $notificationType
 * @property array $items Liste de payloads (chacun avec message, url, changes, etc.)
 * @property string $frequency
 */
class DigestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $notificationType,
        public array $items,
        public string $frequency = 'daily'
    ) {}

    public function via($notifiable): array
    {
        return $notifiable->getChannelsForNotificationType($this->notificationType);
    }

    public function toMail($notifiable): MailMessage
    {
        $label = config('notifications.types.' . $this->notificationType . '.label', $this->notificationType);
        $mail = (new MailMessage)
            ->subject('Récapitulatif : ' . $label)
            ->greeting('Bonjour !')
            ->line('Voici le récapitulatif de vos notifications (' . count($this->items) . ' élément(s)).');

        $displayed = 0;
        foreach ($this->items as $item) {
            if ($displayed >= 10) {
                $mail->line('... et d\'autres éléments.');
                break;
            }
            $msg = $item['message'] ?? $item['entity_name'] ?? 'Modification';
            $mail->line('• ' . $msg);
            $displayed++;
        }

        $mail->action('Voir les notifications', url('/notifications'));
        return $mail;
    }

    public function toArray($notifiable): array
    {
        $sanitized = [];
        foreach ($this->items as $item) {
            $sanitized[] = self::sanitizePayload($item);
        }
        return [
            'notification_type' => $this->notificationType,
            'frequency' => $this->frequency,
            'count' => count($this->items),
            'message' => 'Récapitulatif : ' . count($this->items) . ' notification(s).',
            'url' => url('/notifications'),
            'items' => $sanitized,
        ];
    }

    /**
     * Nettoie un item de payload pour stockage en BDD (évite contenu trop long ou non sérialisable).
     */
    private static function sanitizePayload(array $item): array
    {
        $out = [];
        foreach (['message', 'url', 'entity_type', 'entity_id', 'entity_name', 'modifier_name'] as $k) {
            if (isset($item[$k])) {
                $out[$k] = is_string($item[$k]) ? NotificationService::truncateAndSanitize($item[$k]) : $item[$k];
            }
        }
        if (isset($item['changes']) && is_array($item['changes'])) {
            $out['changes'] = [];
            foreach ($item['changes'] as $field => $change) {
                if (! is_array($change)) {
                    continue;
                }
                $old = $change['old'] ?? null;
                $new = $change['new'] ?? null;
                $out['changes'][$field] = [
                    'old' => is_scalar($old) ? NotificationService::truncateAndSanitize($old) : null,
                    'new' => is_scalar($new) ? NotificationService::truncateAndSanitize($new) : null,
                    'image_url' => $change['image_url'] ?? null,
                ];
            }
        }
        return $out;
    }
}
