<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageService;

class CleanThumbnailsCommand extends Command
{
    protected $signature = 'media:clean-thumbnails {--older-than=86400 : Age en secondes des thumbnails à supprimer}';
    protected $description = 'Nettoie les thumbnails obsolètes';

    public function handle(ImageService $imageService)
    {
        $this->info('Début du nettoyage des thumbnails...');

        $olderThan = $this->option('older-than');
        $imageService->cleanThumbnails($olderThan);

        $this->info('Nettoyage des thumbnails terminé.');
    }
}
