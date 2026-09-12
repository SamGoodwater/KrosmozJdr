<?php

declare(strict_types=1);

namespace App\Console\Commands\Entity;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Seeder\Item\ItemSeederExporter;
use App\Services\Seeder\Item\ItemSeederFileRepository;
use Illuminate\Console\Command;

/**
 * Base → seeder : écrit les équipements de la base dans les fichiers JSON versionnés.
 *
 * @example php artisan items:seeder-export
 * @example php artisan items:seeder-export --state=playable --state=draft --prune
 */
final class ItemsSeederExportCommand extends Command
{
    use GuardsProductionEnvironment;

    protected $signature = 'items:seeder-export
        {--state=* : États retenus (défaut : playable). Vide + --all = tous}
        {--all : Exporte tous les états}
        {--id=* : Restreint à des identifiants d’items}
        {--prune : Supprime les fichiers qui ne correspondent plus à la sélection}';

    protected $description = 'Écrit les équipements de la base vers database/seeders/data/entities/items (JSON versionnés)';

    public function handle(ItemSeederExporter $exporter, ItemSeederFileRepository $files): int
    {
        if (! $this->guardDevelopmentOnly()) {
            return ArtisanExitCode::FAILURE;
        }

        $states = $this->option('all') ? [] : $this->states();
        $ids = array_values(array_map('intval', array_filter((array) $this->option('id'), 'is_numeric')));

        $result = $exporter->export($states, $ids, (bool) $this->option('prune'));

        $this->info(sprintf(
            '%d fichier(s) écrit(s) dans %s.',
            count($result['written']),
            ItemSeederFileRepository::RELATIVE_ROOT
        ));
        if ($result['removed'] !== []) {
            $this->line(sprintf('  %d fichier(s) supprimé(s) (--prune).', count($result['removed'])));
        }
        foreach ($result['skipped'] as $reason) {
            $this->warn('  ignoré : '.$reason);
        }
        if ($result['written'] === []) {
            $this->comment('Aucun item ne correspond au filtre.');

            return ArtisanExitCode::SUCCESS;
        }

        foreach (array_slice($result['written'], 0, 10) as $path) {
            $this->line('  '.$files->relative($path));
        }
        if (count($result['written']) > 10) {
            $this->line(sprintf('  … et %d autre(s).', count($result['written']) - 10));
        }

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function states(): array
    {
        $raw = array_values(array_filter(array_map(
            static fn ($value): string => is_scalar($value) ? strtolower(trim((string) $value)) : '',
            (array) $this->option('state')
        )));

        return $raw === [] ? ['playable'] : $raw;
    }
}
