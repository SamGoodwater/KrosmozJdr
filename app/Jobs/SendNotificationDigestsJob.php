<?php

namespace App\Jobs;

use App\Models\NotificationDigestQueue;
use App\Models\User;
use App\Notifications\DigestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Envoie les notifications en mode digest pour une fréquence donnée (daily, weekly, monthly).
 *
 * Planifié dans App\Console\Kernel : daily 00:05, weekly lundi 00:10, monthly 1er à 00:15.
 * En cas d'échec d'envoi, les entrées ne sont pas supprimées et seront retentées au prochain run.
 */
class SendNotificationDigestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        public function __construct(
        public string $frequency
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        if (! in_array($this->frequency, ['daily', 'weekly', 'monthly'], true)) {
            return;
        }

        $rows = NotificationDigestQueue::where('frequency', $this->frequency)
            ->orderBy('user_id')
            ->orderBy('notification_type')
            ->get();
        $grouped = $rows->groupBy(fn ($r) => $r->user_id . '|' . $r->notification_type);

        $itemIds = fn ($items) => $items->pluck('id')->all();

        foreach ($grouped as $key => $items) {
            [$userId, $notificationType] = explode('|', $key, 2);
            $user = User::find($userId);
            if (! $user) {
                // Utilisateur supprimé : on retire les entrées orphelines
                NotificationDigestQueue::whereIn('id', $itemIds($items))->delete();
                continue;
            }
            $payloads = $items->pluck('payload')->all();
            try {
                $user->notify(new DigestNotification($notificationType, $payloads, $this->frequency));
                NotificationDigestQueue::whereIn('id', $itemIds($items))->delete();
            } catch (\Throwable $e) {
                report($e);
                // En cas d'échec, on ne supprime pas : les entrées seront retentées au prochain run
            }
        }
    }
}
