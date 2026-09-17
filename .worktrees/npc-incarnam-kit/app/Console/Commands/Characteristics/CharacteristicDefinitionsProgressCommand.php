<?php

declare(strict_types=1);

namespace App\Console\Commands\Characteristics;

use App\Console\ArtisanExitCode;
use App\Services\Characteristics\CharacteristicDefinitionQualityService;
use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use Illuminate\Console\Command;

/**
 * Progression éditoriale des 282 définitions JSON + CSV index.
 *
 * @example php artisan characteristics:definitions-progress
 */
class CharacteristicDefinitionsProgressCommand extends Command
{
    protected $signature = 'characteristics:definitions-progress
        {--csv= : Chemin CSV (défaut docs/110- To Do/characteristic_definitions_index.csv)}
        {--only-pending : Afficher uniquement les clés non ok}';

    protected $description = 'Statistiques de validation éditoriale des characteristic-definitions';

    public function handle(CharacteristicDefinitionQualityService $quality): int
    {
        $csvPath = $this->option('csv') ?: base_path('docs/110- To Do/characteristic_definitions_index.csv');
        $statusByKey = $this->loadCsvStatuses($csvPath);

        $byGroup = ['creature' => ['total' => 0, 'ok' => 0, 'issues' => 0], 'object' => ['total' => 0, 'ok' => 0, 'issues' => 0], 'spell' => ['total' => 0, 'ok' => 0, 'issues' => 0]];
        $pending = [];

        foreach (CharacteristicDefinitionReader::allDefinitionAbsolutePaths() as $path) {
            $def = CharacteristicDefinitionReader::load($path);
            $key = (string) ($def['characteristic']['key'] ?? '');
            $parsed = CharacteristicDefinitionNaming::parseCharacteristicKey($key);
            $group = $parsed['group'] ?? 'unknown';
            if (! isset($byGroup[$group])) {
                continue;
            }
            $byGroup[$group]['total']++;
            $issues = $quality->qualityIssues($path, $def);
            $csvStatus = $statusByKey[$key] ?? '';
            $isOk = $issues === [] && ($csvStatus === '' || str_starts_with(mb_strtolower($csvStatus), 'ok'));

            if ($isOk) {
                $byGroup[$group]['ok']++;
            } else {
                $byGroup[$group]['issues']++;
                if ((bool) $this->option('only-pending')) {
                    $pending[] = [
                        'key' => $key,
                        'group' => $group,
                        'csv' => $csvStatus,
                        'issues' => implode('; ', $issues),
                    ];
                }
            }
        }

        $total = array_sum(array_column($byGroup, 'total'));
        $ok = array_sum(array_column($byGroup, 'ok'));
        $this->table(
            ['Groupe', 'Total', 'OK (qualité)', 'À traiter'],
            collect($byGroup)->map(fn ($row, $g) => [$g, $row['total'], $row['ok'], $row['issues']])->values()->all()
        );
        $pct = $total > 0 ? round(100 * $ok / $total, 1) : 0;
        $this->info("Progression globale : {$ok}/{$total} ({$pct} %)");

        if ($pending !== []) {
            $this->newLine();
            $this->table(['Clé', 'Groupe', 'Statut CSV', 'Écarts'], $pending);
        }

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @return array<string, string>
     */
    private function loadCsvStatuses(string $csvPath): array
    {
        if (! is_file($csvPath)) {
            $this->warn("CSV absent : {$csvPath}");

            return [];
        }

        $out = [];
        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            return [];
        }
        $header = fgetcsv($handle);
        if (! is_array($header)) {
            fclose($handle);

            return [];
        }
        $statusCol = 'statut_editorial';
        if (! in_array($statusCol, $header, true)) {
            $statusCol = 'Statut';
        }
        $keyIdx = array_search('characteristic_key', $header, true);
        if ($keyIdx === false) {
            $keyIdx = array_search('Clé BDD', $header, true);
        }
        $statusIdx = array_search($statusCol, $header, true);
        if ($statusIdx === false) {
            $statusIdx = array_search('Statut', $header, true);
        }
        while (($row = fgetcsv($handle)) !== false) {
            if (! is_array($row) || $keyIdx === false) {
                continue;
            }
            $key = $row[$keyIdx] ?? '';
            if ($key === '') {
                continue;
            }
            $out[$key] = $statusIdx !== false ? (string) ($row[$statusIdx] ?? '') : '';
        }
        fclose($handle);

        return $out;
    }
}
