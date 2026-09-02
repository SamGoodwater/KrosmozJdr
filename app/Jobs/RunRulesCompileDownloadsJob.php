<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Project\ProjectConsoleJobTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Compile le livre de règles (PDF + ODT) en file d’attente.
 */
class RunRulesCompileDownloadsJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 1800;

    public int $tries = 1;

    private const LOCK_KEY = 'rules-compile-downloads-web';

    private const LOCK_TTL_SECONDS = 1800;

    public function __construct(
        private readonly int $triggeredByUserId,
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
            $tracker->markFailed($this->consoleJobId, 'Une compilation des règles est déjà en cours.');

            throw new \RuntimeException('Une compilation des règles est déjà en cours.');
        }

        try {
            Log::info('RunRulesCompileDownloadsJob : démarrage', [
                'user_id' => $this->triggeredByUserId,
            ]);

            $code = $tracker->runArtisan($this->consoleJobId, 'rules:compile-downloads', []);
            if ($code !== 0) {
                $tracker->logArtisanFailure('RunRulesCompileDownloadsJob', $this->triggeredByUserId, $code);

                throw new \RuntimeException(
                    'La commande rules:compile-downloads a retourné le code '.$code.'.'
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
        Log::error('RunRulesCompileDownloadsJob : échec', [
            'user_id' => $this->triggeredByUserId,
            'exception' => $e?->getMessage(),
        ]);
    }
}
