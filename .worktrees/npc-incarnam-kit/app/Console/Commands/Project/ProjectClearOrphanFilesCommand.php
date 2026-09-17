<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Models\MediaCleanupJob;
use App\Services\Media\MediaCleanupDispatcher;
use App\Services\Media\OrphanPublicMediaCleanupService;
use Illuminate\Console\Command;
use RuntimeException;
use Throwable;

/**
 * Nettoie prudemment les fichiers publics MediaLibrary sans référence en base.
 *
 * @example php artisan project:clear-orphan-files
 * @example php artisan project:clear-orphan-files --delete
 * @example php artisan project:clear-orphan-files --queue --delete
 */
class ProjectClearOrphanFilesCommand extends Command
{
    public function __construct(
        private readonly OrphanPublicMediaCleanupService $cleanupService,
        private readonly MediaCleanupDispatcher $dispatcher,
    ) {
        parent::__construct();
    }

    protected $signature = 'project:clear-orphan-files
        {--delete : Supprime réellement les fichiers candidats. Sans cette option, dry-run uniquement}
        {--limit=200 : Nombre maximum de candidats affichés dans le rapport (mode synchrone)}
        {--queue : Enfile un job suivi (MediaCleanupJob) au lieu d’exécuter en synchrone}
        {--skip-notify : N’envoie pas de notification admin en fin de job (mode --queue)}';

    protected $description = 'Liste ou supprime les fichiers MediaLibrary publics sans référence en base (dry-run par défaut)';

    public function handle(): int
    {
        $delete = (bool) $this->option('delete');
        $queue = (bool) $this->option('queue');
        $skipNotify = (bool) $this->option('skip-notify');
        $limit = max(1, (int) $this->option('limit'));

        if ($queue) {
            return $this->dispatchQueued($delete, $skipNotify);
        }

        return $this->runSynchronous($delete, $limit);
    }

    private function dispatchQueued(bool $delete, bool $skipNotify): int
    {
        $mode = $delete ? MediaCleanupJob::MODE_DELETE : MediaCleanupJob::MODE_DRY_RUN;

        if ($delete) {
            $this->warn('Mode file d’attente + suppression : un worker doit traiter ProcessMediaCleanupJob.');
        } else {
            $this->info('Mode file d’attente + dry-run : aucun fichier ne sera supprimé.');
        }

        try {
            $job = $this->dispatcher->dispatch($mode, null, [], $skipNotify);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $this->info('Job enfilé : '.$job->id);
        $this->line('Statut : '.$job->status.' | Mode : '.$job->mode);

        return ArtisanExitCode::SUCCESS;
    }

    private function runSynchronous(bool $delete, int $limit): int
    {
        if ($delete) {
            $this->warn('Mode suppression actif : seuls les chemins MediaLibrary autorisés sont concernés.');
        } else {
            $this->info('Mode dry-run : aucun fichier ne sera supprimé. Ajoutez --delete pour appliquer.');
        }

        try {
            $result = $this->cleanupService->scan(delete: $delete, limit: $limit);
        } catch (Throwable $e) {
            $this->error('Impossible de scanner les fichiers orphelins : '.$e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $this->line('Racines scannées : '.implode(', ', $result['scannedRoots']));
        $this->line('Fichiers scannés : '.$result['scannedFiles']);
        $this->line('Candidats orphelins : '.$result['candidateCount']);

        if ($result['candidates'] !== []) {
            $this->table(
                ['Chemin', 'Taille', 'Raison'],
                array_map(
                    fn (array $candidate): array => [
                        $candidate['path'],
                        $candidate['size'] === null ? '?' : $this->formatBytes((int) $candidate['size']),
                        $candidate['reason'],
                    ],
                    $result['candidates']
                )
            );
        }

        if ($result['candidateCount'] > count($result['candidates'])) {
            $this->warn('Rapport tronqué par --limit='.$limit.'.');
        }

        if ($delete) {
            $this->info('Fichiers supprimés : '.$result['deletedCount']);
        } else {
            $this->warn('Dry-run terminé : relancez avec --delete pour supprimer ces candidats.');
        }

        return ArtisanExitCode::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' o';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' Ko';
        }

        return round($bytes / (1024 * 1024), 1).' Mo';
    }
}
