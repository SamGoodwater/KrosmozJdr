<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Project\ProjectConsoleJobTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Recalcule en file les prix automatiques d’un type d’entité.
 */
class RunEntityPricesRecalculateJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 1800;

    public int $tries = 1;

    private const LOCK_KEY = 'entity-prices-recalculate-web';

    private const LOCK_TTL_SECONDS = 1800;

    public function __construct(
        private readonly int $triggeredByUserId,
        private readonly string $entityType,
        private readonly ?string $consoleJobId = null,
    ) {}

    public function handle(): void
    {
        $tracker = app(ProjectConsoleJobTracker::class);

        if ($tracker->isCancelled($this->consoleJobId)) {
            return;
        }

        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_TTL_SECONDS);
        if (! $lock->get()) {
            $tracker->markFailed($this->consoleJobId, 'Un recalcul des prix est déjà en cours.');

            throw new \RuntimeException('Un recalcul des prix est déjà en cours.');
        }

        try {
            Log::info('RunEntityPricesRecalculateJob : démarrage', [
                'user_id' => $this->triggeredByUserId,
                'type' => $this->entityType,
            ]);

            $code = $tracker->runArtisan($this->consoleJobId, 'entities:recalculate-prices', [
                'type' => $this->entityType,
            ]);
            if ($code !== 0) {
                $tracker->logArtisanFailure('RunEntityPricesRecalculateJob', $this->triggeredByUserId, $code);

                throw new \RuntimeException(
                    'La commande entities:recalculate-prices a retourné le code '.$code.'.'
                );
            }
        } finally {
            $lock->release();
        }
    }

    public function failed(?\Throwable $e): void
    {
        $tracker = app(ProjectConsoleJobTracker::class);
        if ($tracker->isCancelled($this->consoleJobId)) {
            return;
        }
        $tracker->markFailed(
            $this->consoleJobId,
            $e?->getMessage() ?? 'Échec inattendu',
        );
        Log::error('RunEntityPricesRecalculateJob : échec', [
            'user_id' => $this->triggeredByUserId,
            'exception' => $e?->getMessage(),
        ]);
    }
}
