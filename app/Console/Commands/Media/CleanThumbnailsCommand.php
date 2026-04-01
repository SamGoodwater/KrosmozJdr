<?php

declare(strict_types=1);

namespace App\Console\Commands\Media;

use App\Services\ImageService;
use Illuminate\Console\Command;

/**
 * Nettoie les fichiers du répertoire « thumbnails » géré par {@see ImageService} (hors conversions Spatie).
 *
 * Planifié quotidiennement dans {@see \App\Console\Kernel::schedule} : doit pouvoir s’exécuter en production.
 */
class CleanThumbnailsCommand extends Command
{
    protected $signature = 'media:clean-thumbnails {--older-than=86400 : Age en secondes des thumbnails à supprimer}';

    protected $description = 'Nettoie les thumbnails obsolètes (dossier legacy ImageService)';

    public function handle(ImageService $imageService): int
    {
        $this->info('Début du nettoyage des thumbnails...');

        $olderThan = (int) $this->option('older-than');
        $imageService->cleanThumbnails($olderThan);

        $this->info('Nettoyage des thumbnails terminé.');

        return self::SUCCESS;
    }
}
