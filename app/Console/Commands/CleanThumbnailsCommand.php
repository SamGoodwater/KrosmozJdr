<?php

namespace App\Console\Commands;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\ImageService;
use Illuminate\Console\Command;

class CleanThumbnailsCommand extends Command
{
    use GuardsProductionEnvironment;

    protected $signature = 'media:clean-thumbnails {--older-than=86400 : Age en secondes des thumbnails à supprimer}';
    protected $description = 'Nettoie les thumbnails obsolètes';

    public function handle(ImageService $imageService)
    {
        if (! $this->guardDevelopmentOnly()) {
            return self::FAILURE;
        }

        $this->info('Début du nettoyage des thumbnails...');

        $olderThan = $this->option('older-than');
        $imageService->cleanThumbnails($olderThan);

        $this->info('Nettoyage des thumbnails terminé.');
    }
}
