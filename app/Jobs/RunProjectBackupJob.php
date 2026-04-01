<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Exécute `project:backup` en file d’attente.
 */
class RunProjectBackupJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public int $tries = 1;

    private const LOCK_KEY = 'project-backup-web';

    private const LOCK_TTL_SECONDS = 3600;

    /**
     * @param  array<string, mixed>  $artisanOptions  Options passées à `Artisan::call('project:backup', …)`
     */
    public function __construct(
        private readonly int $triggeredByUserId,
        private readonly array $artisanOptions = [],
    ) {}

    public function handle(): void
    {
        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_TTL_SECONDS);

        if (! $lock->get()) {
            Log::warning('RunProjectBackupJob : verrou actif, abandon', [
                'user_id' => $this->triggeredByUserId,
            ]);

            throw new \RuntimeException(
                'Une sauvegarde est déjà en cours. Réessayez plus tard.'
            );
        }

        try {
            Log::info('RunProjectBackupJob : démarrage', [
                'user_id' => $this->triggeredByUserId,
                'option_keys' => array_keys($this->artisanOptions),
            ]);

            $code = Artisan::call('project:backup', $this->artisanOptions);

            if ($code !== 0) {
                Log::error('RunProjectBackupJob : commande en échec', [
                    'user_id' => $this->triggeredByUserId,
                    'exit_code' => $code,
                    'output' => Artisan::output(),
                ]);

                throw new \RuntimeException(
                    'La commande project:backup a retourné le code '.$code.'. Voir les logs.'
                );
            }

            Log::info('RunProjectBackupJob : terminé', [
                'user_id' => $this->triggeredByUserId,
            ]);
        } finally {
            $lock->release();
        }
    }

    public function failed(?\Throwable $e): void
    {
        Log::error('RunProjectBackupJob : échec', [
            'user_id' => $this->triggeredByUserId,
            'exception' => $e?->getMessage(),
        ]);
    }
}
