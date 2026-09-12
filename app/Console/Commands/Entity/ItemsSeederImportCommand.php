<?php

declare(strict_types=1);

namespace App\Console\Commands\Entity;

use App\Console\ArtisanExitCode;
use App\Services\Seeder\Item\ItemSeederFileRepository;
use App\Services\Seeder\Item\ItemSeederImporter;
use Illuminate\Console\Command;

/**
 * Seeder → base : rejoue les fichiers JSON d'équipements versionnés dans la base.
 *
 * @example php artisan items:seeder-import
 * @example php artisan items:seeder-import --dry-run
 */
final class ItemsSeederImportCommand extends Command
{
    protected $signature = 'items:seeder-import
        {--dry-run : Affiche ce qui serait créé ou mis à jour, sans écrire}';

    protected $description = 'Rejoue database/seeders/data/entities/items vers la base (upsert par dofusdb_id)';

    public function handle(ItemSeederImporter $importer, ItemSeederFileRepository $files): int
    {
        $paths = $files->paths();
        if ($paths === []) {
            $this->comment('Aucun fichier dans '.ItemSeederFileRepository::RELATIVE_ROOT.'. Rien à importer.');

            return ArtisanExitCode::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $result = $importer->import($dryRun);

        $this->info(sprintf(
            '%d fichier(s) lu(s) : %d création(s), %d mise(s) à jour, %d ignoré(s).%s',
            count($paths),
            count($result['created']),
            count($result['updated']),
            count($result['skipped']),
            $dryRun ? ' [simulation]' : ''
        ));
        foreach ($result['skipped'] as $reason) {
            $this->warn('  ignoré : '.$reason);
        }

        return ArtisanExitCode::SUCCESS;
    }
}
