<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Project\DevReportsService;
use App\Services\Project\ProjectConsoleJobTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Exécute `project:review` depuis la file d’attente (super-admin web).
 *
 * Risque CPU / durée importante : timeouts élevés, verrou singleton.
 */
class RunProjectReviewJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 10800;

    public int $tries = 1;

    private const LOCK_KEY = 'project-review-web';

    private const LOCK_TTL_SECONDS = 10800;

    /**
     * @param  array<string, bool|string>  $artisanArguments  Flags passés à `Artisan::call('project:review', …)`
     */
    public function __construct(
        private readonly int $triggeredByUserId,
        private readonly string $reportPath,
        private readonly array $artisanArguments = [],
        private readonly ?string $consoleJobId = null,
    ) {}

    public function handle(): void
    {
        $tracker = app(ProjectConsoleJobTracker::class);

        if ($tracker->isCancelled($this->consoleJobId)) {
            return;
        }

        /** @var DevReportsService $devReports */
        $devReports = app(DevReportsService::class);
        if (! $devReports->isAllowedNewReportPath($this->reportPath)) {
            Log::warning('RunProjectReviewJob : chemin rapport refusé (hors dossier sécurisé ou nom invalide)', [
                'user_id' => $this->triggeredByUserId,
                'report_path' => $this->reportPath,
            ]);
            $tracker->markFailed($this->consoleJobId, 'Chemin de rapport invalide.');

            throw new \RuntimeException('Chemin de rapport invalide.');
        }

        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_TTL_SECONDS);

        if (! $lock->get()) {
            Log::warning('RunProjectReviewJob : verrou actif, abandon', [
                'user_id' => $this->triggeredByUserId,
            ]);
            $tracker->markFailed($this->consoleJobId, 'Une génération de rapport review est déjà en cours.');

            throw new \RuntimeException(
                'Une génération de rapport review est déjà en cours. Réessayez plus tard.'
            );
        }

        try {
            Log::info('RunProjectReviewJob : démarrage', [
                'user_id' => $this->triggeredByUserId,
                'report_path' => $this->reportPath,
            ]);

            $params = array_merge([
                '--report-path' => $this->reportPath,
                '--no-cursor-prompts' => true,
            ], $this->artisanArguments);

            $code = $tracker->runArtisan($this->consoleJobId, 'project:review', $params);

            if ($code !== 0) {
                $tracker->logArtisanFailure('RunProjectReviewJob', $this->triggeredByUserId, $code);

                throw new \RuntimeException(
                    'La commande project:review a retourné le code '.$code.'. Voir les logs.'
                );
            }

            Log::info('RunProjectReviewJob : terminé', [
                'user_id' => $this->triggeredByUserId,
            ]);
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
        Log::error('RunProjectReviewJob : échec', [
            'user_id' => $this->triggeredByUserId,
            'exception' => $e?->getMessage(),
        ]);
    }
}
