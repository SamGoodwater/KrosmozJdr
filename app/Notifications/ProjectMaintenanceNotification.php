<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Mail\NotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notification envoyée aux admin/super_admin après project:init ou project:update.
 *
 * @property string $command 'init'|'update'
 * @property bool $success
 * @property float $durationSeconds
 * @property string $finishedAt Formatted datetime
 * @property string|null $message Optionnel (erreurs, avertissements)
 * @property list<string> $channels Canaux (database, mail)
 */
class ProjectMaintenanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $command,
        public readonly bool $success,
        public readonly float $durationSeconds,
        public readonly string $finishedAt,
        public readonly ?string $message = null,
        public readonly array $channels = ['database'],
    ) {}

    public function via(object $notifiable): array
    {
        $ch = array_intersect($this->channels, ['database', 'mail']);
        return $ch !== [] ? array_values($ch) : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label = $this->command === 'init' ? 'Initialisation' : 'Mise à jour';
        $status = $this->success ? 'réussie' : 'échouée';
        $duration = $this->formatDuration($this->durationSeconds);

        $mail = (new MailMessage)
            ->subject("Projet KrosmozJDR — {$label} {$status}")
            ->greeting('Bonjour')
            ->line("La commande project:{$this->command} s'est terminée le {$this->finishedAt}.")
            ->line("Durée : {$duration}.")
            ->line("Statut : {$status}.");

        if ($this->message !== null) {
            $mail->line($this->message);
        }

        return $mail->line('Merci d\'utiliser Krosmoz JDR.');
    }

    public function toArray(object $notifiable): array
    {
        $label = $this->command === 'init' ? 'Initialisation' : 'Mise à jour';
        $status = $this->success ? 'réussie' : 'échouée';
        $duration = $this->formatDuration($this->durationSeconds);

        return [
            'command' => $this->command,
            'success' => $this->success,
            'duration_seconds' => $this->durationSeconds,
            'duration_human' => $duration,
            'finished_at' => $this->finishedAt,
            'message' => "Project:{$this->command} — {$status} (durée : {$duration}, fin : {$this->finishedAt})."
                . ($this->message ? " {$this->message}" : ''),
            'url' => url('/admin'),
        ];
    }

    private function formatDuration(float $seconds): string
    {
        if ($seconds < 60) {
            return round($seconds, 1) . ' s';
        }
        $m = (int) floor($seconds / 60);
        $s = round($seconds % 60, 1);
        if ($m < 60) {
            return $m . ' min ' . $s . ' s';
        }
        $h = (int) floor($m / 60);
        $m = $m % 60;
        return $h . ' h ' . $m . ' min';
    }
}
