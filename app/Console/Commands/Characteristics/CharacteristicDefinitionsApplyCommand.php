<?php

declare(strict_types=1);

namespace App\Console\Commands\Characteristics;

use App\Console\ArtisanExitCode;
use App\Services\Characteristics\CharacteristicDefinitionQualityService;
use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Applique des correctifs automatiques sûrs sur les JSON + met à jour le CSV statut_editorial.
 *
 * @example php artisan characteristics:definitions-apply --item-types --sync-csv
 */
class CharacteristicDefinitionsApplyCommand extends Command
{
    protected $signature = 'characteristics:definitions-apply
        {--item-types : Ajoute item_type_dofus_ids depuis les helpers (amulette / armes)}
        {--object-skills : Renseigne conversion_formula [d] sur les bonus de compétence objet}
        {--sync-csv : Met à jour statut_editorial dans le CSV selon la qualité}
        {--dry-run : Simulation sans écriture}';

    protected $description = 'Correctifs automatiques sur characteristic-definitions (release 1.3.2)';

    public function handle(CharacteristicDefinitionQualityService $quality): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;

        foreach (CharacteristicDefinitionReader::allDefinitionAbsolutePaths() as $path) {
            $def = CharacteristicDefinitionReader::load($path);
            $helper = (string) ($def['characteristic']['helper'] ?? '');
            $changed = false;

            if ((bool) $this->option('object-skills') && ($def['characteristic']['group'] ?? '') === 'object') {
                $entities = $def['entities'] ?? [];
                if (is_array($entities) && isset($entities['*']) && is_array($entities['*'])) {
                    $parsed = CharacteristicDefinitionNaming::parseCharacteristicKey(
                        (string) ($def['characteristic']['key'] ?? '')
                    );
                    $stem = $parsed['stem'] ?? '';
                    if ($quality->isObjectCompetenceBonusStem($stem) && empty($entities['*']['conversion_formula'])) {
                        $entities['*']['conversion_formula'] = '[d]';
                        $def['entities'] = $entities;
                        $changed = true;
                    }
                }
            }

            if ((bool) $this->option('item-types') && ($def['characteristic']['group'] ?? '') === 'object') {
                $entities = $def['entities'] ?? [];
                if (is_array($entities) && isset($entities['*']) && is_array($entities['*'])) {
                    $suggested = $quality->suggestedDofusTypeIdsForHelper($helper);
                    if ($suggested !== [] && empty($entities['*']['item_type_dofus_ids'])) {
                        $entities['*']['item_type_dofus_ids'] = $suggested;
                        $def['entities'] = $entities;
                        $changed = true;
                    }
                }
            }

            if ($changed && ! $dryRun) {
                File::put($path, json_encode($def, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");
                $updated++;
            }
        }

        if ((bool) $this->option('sync-csv') && ! $dryRun) {
            $this->syncCsvStatuses($quality);
        }

        $this->info($dryRun ? 'Simulation terminée.' : "{$updated} fichier(s) JSON mis à jour.");

        return ArtisanExitCode::SUCCESS;
    }

    private function syncCsvStatuses(CharacteristicDefinitionQualityService $quality): void
    {
        $csvPath = base_path('docs/110- To Do/characteristic_definitions_index.csv');
        if (! is_file($csvPath)) {
            $this->warn('CSV index absent, sync ignorée.');

            return;
        }

        $rows = [];
        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            return;
        }
        $header = fgetcsv($handle);
        if (! is_array($header)) {
            fclose($handle);

            return;
        }
        if (! in_array('statut_editorial', $header, true)) {
            $header[] = 'statut_editorial';
        }
        $keyIdx = array_search('characteristic_key', $header, true) ?: array_search('Clé BDD', $header, true);
        $statusIdx = array_search('statut_editorial', $header, true);
        while (($row = fgetcsv($handle)) !== false) {
            if (! is_array($row)) {
                continue;
            }
            $rows[] = $row;
        }
        fclose($handle);

        $paths = collect(CharacteristicDefinitionReader::allDefinitionAbsolutePaths())
            ->mapWithKeys(function (string $path) {
                $def = CharacteristicDefinitionReader::load($path);

                return [(string) ($def['characteristic']['key'] ?? '') => ['path' => $path, 'def' => $def]];
            });

        foreach ($rows as $i => $row) {
            if ($keyIdx === false) {
                continue;
            }
            $key = $row[$keyIdx] ?? '';
            if ($key === '' || ! isset($paths[$key])) {
                continue;
            }
            $entry = $paths[$key];
            $issues = $quality->qualityIssues($entry['path'], $entry['def']);
            while (count($row) < count($header)) {
                $row[] = '';
            }
            if ($statusIdx === false) {
                $statusIdx = count($header) - 1;
                $row[$statusIdx] = '';
            }
            $row[$statusIdx] = $issues === [] ? 'ok' : 'à revoir — qualité';
        }

        $out = fopen($csvPath, 'w');
        if ($out === false) {
            return;
        }
        fputcsv($out, $header);
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        $this->info('CSV statut_editorial synchronisé.');
    }
}
