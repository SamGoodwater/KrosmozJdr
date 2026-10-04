<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Services\Project\ProjectBackupService;
use Illuminate\Console\Command;
use Throwable;

/**
 * Liste les archives de sauvegarde (ZIP v2 et legacy).
 *
 * @example php artisan project:backup:list
 */
class ProjectBackupListCommand extends Command
{
    protected $signature = 'project:backup:list
        {--path= : Répertoire des sauvegardes (défaut : config ou storage/app/backups)}';

    protected $description = 'Liste les sauvegardes disponibles (nom, date, taille, intégrité)';

    public function handle(): int
    {
        try {
            $pathOpt = $this->option('path');
            $service = ProjectBackupService::fromConfig(
                is_string($pathOpt) && $pathOpt !== '' ? $pathOpt : null
            );
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $items = $service->listBackups();
        if ($items === []) {
            $this->info('Aucune sauvegarde dans '.$service->resolvedBackupDirectory());

            return ArtisanExitCode::SUCCESS;
        }

        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                $item['name'],
                $item['format'],
                implode(',', $item['components']),
                $item['integrity'],
                $this->formatBytes((int) $item['size']),
                $item['mtime'] > 0 ? date('Y-m-d H:i:s', (int) $item['mtime']) : '—',
            ];
        }

        $this->table(['Nom', 'Format', 'Composants', 'Intégrité', 'Taille', 'Modifié'], $rows);
        $this->line('Répertoire : '.$service->resolvedBackupDirectory());

        return ArtisanExitCode::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' KiB';
        }

        return round($bytes / (1024 * 1024), 1).' MiB';
    }
}
