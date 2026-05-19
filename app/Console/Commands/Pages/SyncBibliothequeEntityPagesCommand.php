<?php

namespace App\Console\Commands\Pages;

use App\Services\BibliothequeEntityPageService;
use Illuminate\Console\Command;

/**
 * Synchronise les sous-pages CMS des bibliothèques Classes / Spécialisations.
 */
class SyncBibliothequeEntityPagesCommand extends Command
{
    protected $signature = 'pages:sync-bibliotheque-entities';

    protected $description = 'Crée ou met à jour les sous-pages menu par classe et par spécialisation';

    public function handle(BibliothequeEntityPageService $service): int
    {
        $stats = $service->syncAll();
        $this->info(sprintf(
            'Synchronisation terminée : %d classes, %d spécialisations (%d retirées du menu).',
            $stats['breeds'],
            $stats['specializations'],
            $stats['removed']
        ));

        return self::SUCCESS;
    }
}
