<?php

declare(strict_types=1);

namespace App\Console\Commands\GenerativeAi;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridAnalyzer;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridCell;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridDefinition;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridHoleFiller;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridItemRepository;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Rapport de couverture de la grille d’équipements JDR (niveau × slot × voie), puis remplissage optionnel des trous.
 *
 * @example php artisan ia:equipment-grid
 * @example php artisan ia:equipment-grid --write --limit=20
 */
final class EquipmentGridCommand extends Command
{
    use GuardsProductionEnvironment;

    protected $signature = 'ia:equipment-grid
        {--write : Crée des objets draft pour les cases vides (jamais playable)}
        {--slot= : Restreint à un slot (amulet, ring, belt, boots, hat, cape, weapon)}
        {--voie= : Restreint à une voie (terre, feu, eau, air, neutre)}
        {--level= : Restreint à un niveau 1–20}
        {--limit= : Nombre max d’objets créés avec --write}
        {--json= : Écrit le rapport JSON (chemin fichier)}';

    protected $description = 'Couverture de la grille d’équipements JDR (niveau × slot × voie) ; --write remplit les trous en draft';

    public function handle(
        EquipmentGridAnalyzer $analyzer,
        EquipmentGridItemRepository $repository,
        EquipmentGridHoleFiller $filler,
    ): int {
        $definition = EquipmentGridDefinition::loadDefault();
        $report = $analyzer->analyze($definition, $repository->forDefinition($definition));
        try {
            $cells = $this->filteredCells($report, $definition);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $this->info(sprintf(
            'Grille : %d cases (%d slots × %d voies × niveaux %d–%d).',
            $definition->cellCount(),
            count($definition->slots()),
            count($definition->activeVoieKeys()),
            $definition->minLevel,
            $definition->maxLevel,
        ));
        $this->info(sprintf(
            'Parcouru : %d objets. Occupées : %d. Trous : %d. Doublons : %d. Hors slot : %d. Sans voie : %d.',
            $report->scanned,
            $report->filled,
            $report->holes,
            $report->duplicateCount,
            $report->outsideGrid,
            $report->unclassified,
        ));

        $this->renderPreview($cells);

        $jsonPath = $this->option('json');
        if (is_string($jsonPath) && $jsonPath !== '') {
            $this->writeJson($jsonPath, $report, $cells);
            $this->info('Rapport JSON : '.$jsonPath);
        }

        if (! $this->option('write')) {
            $this->comment('Lecture seule. Passe --write pour créer les trous en draft (interdit en production).');

            return ArtisanExitCode::SUCCESS;
        }

        if (! $this->guardNotProduction('ia:equipment-grid --write est interdit en production.')) {
            return ArtisanExitCode::FAILURE;
        }

        $holes = array_values(array_filter(
            $cells,
            static fn (EquipmentGridCell $cell): bool => $cell->isHole
        ));
        $limitRaw = $this->option('limit');
        $limit = is_numeric($limitRaw) ? max(1, (int) $limitRaw) : null;

        try {
            $created = $filler->fillHoles($definition, $holes, $limit);
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $this->info(sprintf('%d objet(s) draft créé(s). Les fiches Dofus n’ont pas été modifiées.', count($created)));
        foreach ($created as $row) {
            $this->line(sprintf('  #%d %s (%s)', $row['id'], $row['name'], $row['official_id']));
        }

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @return list<EquipmentGridCell>
     */
    private function filteredCells(EquipmentGridReport $report, EquipmentGridDefinition $definition): array
    {
        $slot = $this->normalizedOption('slot');
        $voie = $this->normalizedOption('voie');
        $levelRaw = $this->option('level');
        $level = is_numeric($levelRaw) ? (int) $levelRaw : null;

        if ($slot !== null && $definition->slot($slot) === null) {
            throw new \InvalidArgumentException('Slot inconnu : '.$slot);
        }
        if ($voie !== null && $definition->voie($voie) === null) {
            throw new \InvalidArgumentException('Voie inconnue : '.$voie);
        }

        return array_values(array_filter(
            $report->cells,
            static function (EquipmentGridCell $cell) use ($slot, $voie, $level): bool {
                if ($slot !== null && $cell->slotKey !== $slot) {
                    return false;
                }
                if ($voie !== null && $cell->voieKey !== $voie) {
                    return false;
                }
                if ($level !== null && $cell->level !== $level) {
                    return false;
                }

                return true;
            }
        ));
    }

    /**
     * @param  list<EquipmentGridCell>  $cells
     */
    private function renderPreview(array $cells): void
    {
        $holes = array_values(array_filter($cells, static fn (EquipmentGridCell $cell): bool => $cell->isHole));
        $filled = array_values(array_filter($cells, static fn (EquipmentGridCell $cell): bool => ! $cell->isHole));

        $this->newLine();
        $this->comment('Aperçu des cases occupées (max 15) :');
        $rows = [];
        foreach (array_slice($filled, 0, 15) as $cell) {
            $rep = $cell->representative;
            $rows[] = [
                $cell->slotLabel,
                $cell->voieLabel,
                (string) $cell->level,
                $rep['name'] ?? '—',
                isset($rep['id']) ? '#'.$rep['id'] : '—',
                $rep['state'] ?? '',
            ];
        }
        if ($rows === []) {
            $this->line('  (aucune)');
        } else {
            $this->table(['Slot', 'Voie', 'Niv', 'Représentant', 'Id', 'État'], $rows);
        }

        $this->comment(sprintf('Trous dans le filtre : %d', count($holes)));
        $holePreview = [];
        foreach (array_slice($holes, 0, 12) as $cell) {
            $holePreview[] = sprintf('%s %s %d', $cell->slotLabel, $cell->voieLabel, $cell->level);
        }
        if ($holePreview !== []) {
            $this->line('  '.implode(' · ', $holePreview).(count($holes) > 12 ? ' …' : ''));
        }
    }

    /**
     * @param  list<EquipmentGridCell>  $cells
     */
    private function writeJson(string $path, EquipmentGridReport $report, array $cells): void
    {
        $payload = [
            'scanned' => $report->scanned,
            'filled' => $report->filled,
            'holes' => $report->holes,
            'duplicates' => $report->duplicateCount,
            'outside_grid' => $report->outsideGrid,
            'unclassified' => $report->unclassified,
            'cells' => array_map(static fn (EquipmentGridCell $cell): array => [
                'slot' => $cell->slotKey,
                'voie' => $cell->voieKey,
                'level' => $cell->level,
                'hole' => $cell->isHole,
                'representative' => $cell->representative,
                'duplicates' => $cell->duplicates,
            ], $cells),
        ];
        $dir = dirname($path);
        if ($dir !== '.' && $dir !== '' && ! is_dir($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        File::put($path, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }

    private function normalizedOption(string $name): ?string
    {
        $value = $this->option($name);
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return strtolower(trim($value));
    }
}
