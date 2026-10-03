<?php

declare(strict_types=1);

namespace App\Notifications\Concerns;

use App\Support\Queue\ProjectQueues;

/**
 * Pose les notifications mises en file sur {@see ProjectQueues::NOTIFICATIONS}.
 *
 * Sans ça, Laravel les envoie sur `default` : elles restent invisibles dans le centre
 * tant qu’un worker n’a pas écrit la table `notifications`.
 *
 * @example $user->notify(new LastConnectionNotification($loggedAt));
 */
trait QueuesOnNotifications
{
    /**
     * @return array<string, string>
     */
    public function viaQueues(): array
    {
        return [
            'database' => ProjectQueues::NOTIFICATIONS,
            'mail' => ProjectQueues::NOTIFICATIONS,
        ];
    }
}
