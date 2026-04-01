<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Exécute `project:deps` en file d’attente (interdit en production côté commande).
 */
class RunProjectDepsJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 7200;

    public int $tries = 1;

    private const LOCK_KEY = 'project-deps-web';

    private const LOCK_TTL_SECONDS = 7200;

    /**
     * @param  array<string, mixed>  $artisanOptions  Options passées à `Artisan::call('project:deps', …)`
     */
    public function __construct(
        private readonly int $triggeredByUserId,
        private readonly array $artisanOptions = [],
    ) {}

    public function handle(): void
    {
        if (app()->environment('production')) {
            Log::warning('RunProjectDepsJob : ignoré en production', [
                'user_id' => $this->triggeredByUserId,
            ]);

            throw new \RuntimeException(
                'La mise à jour de la stack (project:deps) n’est pas disponible en production depuis l’interface.'
            );
        }

        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_TTL_SECONDS);

        if (! $lock->get()) {
            Log::warning('RunProjectDepsJob : verrou actif, abandon', [
                'user_id' => $this->triggeredByUserId,
            ]);

            throw new \RuntimeException(
                'Une mise à jour de stack est déjà en cours. Réessayez plus tard.'
            );
        }

        try {
            Log::info('RunProjectDepsJob : démarrage', [
                'user_id' => $this->triggeredByUserId,
                'option_keys' => array_keys($this->artisanOptions),
            ]);

            $code = Artisan::call('project:deps', $this->artisanOptions);

            if ($code !== 0) {
                Log::error('RunProjectDepsJob : commande en échec', [
                    'user_id' => $this->triggeredByUserId,
                    'exit_code' => $code,
                    'output' => Artisan::output(),
                ]);

                throw new \RuntimeException(
                    'La commande project:deps a retourné le code '.$code.'. Voir les logs.'
                );
            }

            Log::info('RunProjectDepsJob : terminé', [
                'user_id' => $this->triggeredByUserId,
            ]);
        } finally {
            $lock->release();
        }
    }

    public function failed(?\Throwable $e): void
    {
        Log::error('RunProjectDepsJob : échec', [
            'user_id' => $this->triggeredByUserId,
            'exception' => $e?->getMessage(),
        ]);
    }
}
