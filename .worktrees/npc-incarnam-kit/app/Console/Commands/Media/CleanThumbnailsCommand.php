<?php

declare(strict_types=1);

namespace App\Console\Commands\Media;

use App\Console\ArtisanExitCode;
use App\Services\ImageService;
use App\Support\ProjectSchedule\ProjectScheduleCatalog;
use App\Support\ProjectSchedule\ProjectScheduleRegistrar;
use Illuminate\Console\Command;

/**
 * Nettoie les fichiers du répertoire « thumbnails » géré par {@see ImageService} (hors conversions Spatie).
 *
 * En production : planifié par {@see ProjectScheduleRegistrar} (clé `media_clean_thumbnails` dans {@see ProjectScheduleCatalog}) ; branchement Laravel 12 dans `bootstrap/app.php` (`withSchedule`).
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

        return ArtisanExitCode::SUCCESS;
    }
}
