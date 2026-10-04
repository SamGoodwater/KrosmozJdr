<?php

declare(strict_types=1);

namespace App\Support\Queue;

use App\Services\Project\ProjectConsoleQueueKicker;

/**
 * Files dédiées. Le worker ponctuel ({@see ProjectConsoleQueueKicker})
 * ne mélange plus notifications, sauvegarde, scrapping et IA.
 *
 * @example ProjectQueues::listenList()
 */
final class ProjectQueues
{
    public const NOTIFICATIONS = 'notifications';

    public const BACKUP = 'backup';

    public const SCRAPPING = 'scrapping';

    public const IA = 'ia';

    public const RULES_DOWNLOADS = 'rules-downloads';

    public const MAINTENANCE = 'maintenance';

    public const PRIVACY = 'privacy';

    public const MEDIA = 'media';

    /** Timeout du `queue:listen` de dev : doit rester sous `retry_after` (12 000 s). */
    public const LISTENER_TIMEOUT = 10800;

    /**
     * @var array<string, array{timeout: int, max_jobs: int|null}>
     */
    private const WORKERS = [
        self::NOTIFICATIONS => ['timeout' => 120, 'max_jobs' => null],
        self::BACKUP => ['timeout' => 7200, 'max_jobs' => 1],
        self::SCRAPPING => ['timeout' => 7200, 'max_jobs' => null],
        self::IA => ['timeout' => 180, 'max_jobs' => 1],
        self::RULES_DOWNLOADS => ['timeout' => 1800, 'max_jobs' => 1],
        self::MAINTENANCE => ['timeout' => 10800, 'max_jobs' => 1],
        self::PRIVACY => ['timeout' => 600, 'max_jobs' => 1],
        self::MEDIA => ['timeout' => 300, 'max_jobs' => null],
    ];

    public static function isManaged(string $queue): bool
    {
        return isset(self::WORKERS[$queue]);
    }

    public static function workerTimeout(string $queue): int
    {
        return self::WORKERS[$queue]['timeout'] ?? 90;
    }

    public static function maxJobs(string $queue): ?int
    {
        return self::WORKERS[$queue]['max_jobs'] ?? 1;
    }

    /**
     * Files écoutées par `project:dev --queue` (`default` en dernier pour l’existant).
     */
    public static function listenList(): string
    {
        return implode(',', [...array_keys(self::WORKERS), 'default']);
    }
}
