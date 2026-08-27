<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Project\ProjectConsoleJobTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Exécute `project:data sync` en file d’attente (évite un HTTP long).
 *
 * Verrou global pour éviter deux syncs massives en parallèle.
 */
class RunProjectDataSyncJob implements ShouldQueue
{
    use Queueable;

    /** Sync DofusDB peut dépasser plusieurs minutes. */
    public int $timeout = 7200;

    public int $tries = 1;

    private const LOCK_KEY = 'project-data-sync';

    private const LOCK_TTL_SECONDS = 7200;

    /**
     * @param  array<string, mixed>  $artisanParameters  Arguments pour `Artisan::call('project:data', ...)`
     */
    public function __construct(
        private readonly int $triggeredByUserId,
        private readonly array $artisanParameters,
        private readonly ?string $consoleJobId = null,
    ) {}

    public function handle(): void
    {
        $tracker = app(ProjectConsoleJobTracker::class);
        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_TTL_SECONDS);

        if (! $lock->get()) {
            Log::warning('RunProjectDataSyncJob : verrou actif, abandon', [
                'user_id' => $this->triggeredByUserId,
            ]);
            $tracker->markFailed($this->consoleJobId, 'Une synchronisation des données est déjà en cours.');

            throw new \RuntimeException(
                'Une synchronisation des données est déjà en cours. Réessayez plus tard.'
            );
        }

        try {
            Log::info('RunProjectDataSyncJob : démarrage', [
                'user_id' => $this->triggeredByUserId,
                'params_keys' => array_keys($this->artisanParameters),
            ]);

            $code = $tracker->runArtisan($this->consoleJobId, 'project:data', $this->artisanParameters);

            if ($code !== 0) {
                $tracker->logArtisanFailure('RunProjectDataSyncJob', $this->triggeredByUserId, $code);

                throw new \RuntimeException(
                    'La commande project:data sync a retourné le code '.$code.'. Voir les logs.'
                );
            }

            Log::info('RunProjectDataSyncJob : terminé', [
                'user_id' => $this->triggeredByUserId,
            ]);
        } finally {
            $lock->release();
        }
    }

    public function failed(?\Throwable $e): void
    {
        app(ProjectConsoleJobTracker::class)->markFailed(
            $this->consoleJobId,
            $e?->getMessage() ?? 'Échec inattendu',
        );
        Log::error('RunProjectDataSyncJob : échec', [
            'user_id' => $this->triggeredByUserId,
            'exception' => $e?->getMessage(),
        ]);
    }
}
