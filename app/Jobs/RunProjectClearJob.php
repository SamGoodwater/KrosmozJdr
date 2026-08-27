<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Project\ProjectConsoleJobTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Exécute `project:clear` en file d’attente (preset `--safe` ou `--all`).
 */
class RunProjectClearJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;

    public int $tries = 1;

    private const LOCK_KEY = 'project-clear-web';

    private const LOCK_TTL_SECONDS = 600;

    /**
     * @param  array<string, mixed>  $artisanOptions  Options passées à `Artisan::call('project:clear', …)`
     */
    public function __construct(
        private readonly int $triggeredByUserId,
        private readonly array $artisanOptions = [],
        private readonly ?string $consoleJobId = null,
    ) {}

    public function handle(): void
    {
        $tracker = app(ProjectConsoleJobTracker::class);
        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_TTL_SECONDS);

        if (! $lock->get()) {
            Log::warning('RunProjectClearJob : verrou actif, abandon', [
                'user_id' => $this->triggeredByUserId,
            ]);
            $tracker->markFailed($this->consoleJobId, 'Un nettoyage est déjà en cours.');

            throw new \RuntimeException(
                'Un nettoyage est déjà en cours. Réessayez plus tard.'
            );
        }

        try {
            Log::info('RunProjectClearJob : démarrage', [
                'user_id' => $this->triggeredByUserId,
                'option_keys' => array_keys($this->artisanOptions),
            ]);

            $code = $tracker->runArtisan($this->consoleJobId, 'project:clear', $this->artisanOptions);

            if ($code !== 0) {
                $tracker->logArtisanFailure('RunProjectClearJob', $this->triggeredByUserId, $code);

                throw new \RuntimeException(
                    'La commande project:clear a retourné le code '.$code.'. Voir les logs.'
                );
            }

            Log::info('RunProjectClearJob : terminé', [
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
        Log::error('RunProjectClearJob : échec', [
            'user_id' => $this->triggeredByUserId,
            'exception' => $e?->getMessage(),
        ]);
    }
}
