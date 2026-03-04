<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DofusdbEffectMapping;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Console\Command;

/**
 * Rapport des mappings d'effets incomplets (characteristic_source=characteristic, key manquante).
 *
 * Produit un regroupement par characteristic DofusDB (GET /effects/{id}.characteristic),
 * trié par fréquence, pour prioriser les prochains ajouts de conversion.
 */
final class ScrappingEffectsMissingCharacteristicsReportCommand extends Command
{
    protected $signature = 'scrapping:effects:report-missing-characteristics
                            {--ids= : Liste d\'effectId séparés par des virgules (optionnel)}
                            {--limit=20 : Nombre max de lignes dans le top}
                            {--lang=fr : Langue API DofusDB}
                            {--json : Sortie JSON}
                            {--skip-cache : Ignore le cache HTTP DofusDB}';

    protected $description = 'Rapport des mappings d\'effets sans characteristic_key, regroupés par characteristic DofusDB';
    protected $aliases = ['dofusdb:report-missing-effect-characteristics'];

    public function handle(
        DofusDbClient $client,
        CharacteristicGetterService $characteristicGetter
    ): int {
        $lang = (string) $this->option('lang');
        $skipCache = (bool) $this->option('skip-cache');
        $limit = max(1, (int) $this->option('limit'));
        $asJson = (bool) $this->option('json');
        $effectIdsFilter = $this->parseIdsOption((string) ($this->option('ids') ?? ''));

        $query = DofusdbEffectMapping::query()
            ->where('characteristic_source', DofusdbEffectMapping::SOURCE_CHARACTERISTIC)
            ->where(function ($q): void {
                $q->whereNull('characteristic_key')->orWhere('characteristic_key', '');
            });

        if ($effectIdsFilter !== []) {
            $query->whereIn('dofusdb_effect_id', $effectIdsFilter);
        }

        /** @var \Illuminate\Support\Collection<int, DofusdbEffectMapping> $rows */
        $rows = $query->orderBy('dofusdb_effect_id')->get();
        if ($rows->isEmpty()) {
            $this->line($asJson ? json_encode(['total_missing_rows' => 0], JSON_PRETTY_PRINT) : 'Aucune ligne manquante trouvée.');
            return self::SUCCESS;
        }

        $spellMapFromDb = $characteristicGetter->getDofusdbToCharacteristicKeyMap('spell');
        $spellMapFromConfig = $this->loadSpellCharacteristicMapFromConfig();

        /**
         * @var array<int|string, array{
         *   dofusdb_characteristic_id:int|null,
         *   count:int,
         *   effect_ids:list<int>,
         *   resolved_key_db:string|null,
         *   resolved_key_config:string|null
         * }>
         */
        $groups = [];
        $apiErrors = 0;
        $apiMissingCharacteristic = 0;

        foreach ($rows as $row) {
            $effectId = (int) $row->dofusdb_effect_id;

            try {
                $effectData = $client->getJson(
                    "https://api.dofusdb.fr/effects/{$effectId}?lang={$lang}",
                    ['skip_cache' => $skipCache]
                );
            } catch (\Throwable) {
                $apiErrors++;
                $bucket = 'api_error';
                if (!isset($groups[$bucket])) {
                    $groups[$bucket] = [
                        'dofusdb_characteristic_id' => null,
                        'count' => 0,
                        'effect_ids' => [],
                        'resolved_key_db' => null,
                        'resolved_key_config' => null,
                    ];
                }
                $groups[$bucket]['count']++;
                $groups[$bucket]['effect_ids'][] = $effectId;
                continue;
            }

            $dofusdbCharacteristicId = isset($effectData['characteristic']) && is_numeric($effectData['characteristic'])
                ? (int) $effectData['characteristic']
                : null;

            if ($dofusdbCharacteristicId === null || $dofusdbCharacteristicId <= 0) {
                $apiMissingCharacteristic++;
                $bucket = 'no_characteristic';
                if (!isset($groups[$bucket])) {
                    $groups[$bucket] = [
                        'dofusdb_characteristic_id' => null,
                        'count' => 0,
                        'effect_ids' => [],
                        'resolved_key_db' => null,
                        'resolved_key_config' => null,
                    ];
                }
                $groups[$bucket]['count']++;
                $groups[$bucket]['effect_ids'][] = $effectId;
                continue;
            }

            if (!isset($groups[$dofusdbCharacteristicId])) {
                $groups[$dofusdbCharacteristicId] = [
                    'dofusdb_characteristic_id' => $dofusdbCharacteristicId,
                    'count' => 0,
                    'effect_ids' => [],
                    'resolved_key_db' => $spellMapFromDb[$dofusdbCharacteristicId] ?? null,
                    'resolved_key_config' => $spellMapFromConfig[$dofusdbCharacteristicId] ?? null,
                ];
            }
            $groups[$dofusdbCharacteristicId]['count']++;
            $groups[$dofusdbCharacteristicId]['effect_ids'][] = $effectId;
        }

        uasort($groups, static fn (array $a, array $b): int => $b['count'] <=> $a['count']);

        $top = array_slice($groups, 0, $limit, true);
        $summary = [
            'total_missing_rows' => $rows->count(),
            'total_groups' => count($groups),
            'api_errors' => $apiErrors,
            'api_missing_characteristic' => $apiMissingCharacteristic,
        ];

        if ($asJson) {
            $payload = [
                'summary' => $summary,
                'groups' => array_map(static function (array $g): array {
                    return [
                        'dofusdb_characteristic_id' => $g['dofusdb_characteristic_id'],
                        'count' => $g['count'],
                        'resolved_key_db' => $g['resolved_key_db'],
                        'resolved_key_config' => $g['resolved_key_config'],
                        'effect_ids' => $g['effect_ids'],
                    ];
                }, array_values($groups)),
            ];

            $this->line((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return self::SUCCESS;
        }

        $this->info('Rapport mappings manquants (characteristic_source=characteristic)');
        $this->table(
            ['Lignes manquantes', 'Groupes', 'Erreurs API', 'Sans characteristic API'],
            [[(string) $summary['total_missing_rows'], (string) $summary['total_groups'], (string) $summary['api_errors'], (string) $summary['api_missing_characteristic']]]
        );

        $tableRows = [];
        foreach ($top as $key => $g) {
            $label = is_int($key) ? (string) $key : (string) $key;
            $sampleIds = implode(',', array_slice($g['effect_ids'], 0, 6));
            $tableRows[] = [
                $label,
                (string) $g['count'],
                (string) ($g['resolved_key_db'] ?? '—'),
                (string) ($g['resolved_key_config'] ?? '—'),
                $sampleIds,
            ];
        }
        $this->table(
            ['Characteristic DofusDB', 'Count', 'Résolution BDD spell', 'Résolution config spell', 'Exemples effectId'],
            $tableRows
        );

        return self::SUCCESS;
    }

    /**
     * @return list<int>
     */
    private function parseIdsOption(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        $ids = [];
        foreach (explode(',', $raw) as $chunk) {
            $trimmed = trim($chunk);
            if ($trimmed === '' || !is_numeric($trimmed)) {
                continue;
            }
            $id = (int) $trimmed;
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * @return array<int, string>
     */
    private function loadSpellCharacteristicMapFromConfig(): array
    {
        $path = resource_path('scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz_spell.json');
        if (!is_file($path)) {
            return [];
        }

        $content = @file_get_contents($path);
        if ($content === false) {
            return [];
        }

        $decoded = json_decode($content, true);
        $mapping = is_array($decoded['mapping'] ?? null) ? $decoded['mapping'] : [];
        $out = [];
        foreach ($mapping as $id => $key) {
            if (is_numeric($id) && is_string($key) && $key !== '') {
                $out[(int) $id] = $key;
            }
        }

        return $out;
    }
}

