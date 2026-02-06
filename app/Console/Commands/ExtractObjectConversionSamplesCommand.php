<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use App\Services\Scrapping\Core\Collect\CollectService;
use Illuminate\Console\Command;

/**
 * Extrait les samples de conversion Dofus → Krosmoz pour les caractéristiques object
 * à partir des équipements DofusDB uniquement, et écrit le tout dans un fichier JSON.
 *
 * Ne modifie pas le seeder ni la BDD ; sortie : storage/app/characteristics_object_samples.json
 */
class ExtractObjectConversionSamplesCommand extends Command
{
    protected $signature = 'characteristics:extract-object-samples
                            {--output= : Chemin du fichier JSON de sortie (défaut: storage/app/characteristics_object_samples.json)}
                            {--max-items=50000 : Nombre max d\'items équipement à récupérer}
                            {--skip-cache : Ne pas utiliser le cache HTTP}
                            {--chunk=500 : Taille des chunks typeIds (max 500 pour l\'API)}';

    protected $description = 'Extrait les samples de conversion (niveau/valeur) pour les caractéristiques object depuis les équipements DofusDB et les écrit dans un fichier JSON';

    private const EQUIPMENT_SUPER_TYPE_IDS = [1, 2, 3, 4, 5, 7, 10, 11, 12, 13];

    private const LEVEL_BUCKETS = [1, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200];

    /** Niveaux Dofus utilisés par défaut dans l’admin (tableau de conversion). */
    private const REFERENCE_LEVELS = [1, 40, 80, 120, 160, 200];

    /** Niveaux Krosmoz JDR (1–20) utilisés dans l'admin pour les samples cible. */
    private const KROSMOZ_REFERENCE_LEVELS = [1, 4, 8, 12, 16, 20];

    /** Fichier des samples Krosmoz (règles 2.2.1 / 2.2.2). */
    private const KROSMOZ_SAMPLES_PATH = 'resources/scrapping/config/sources/krosmoz/object_krosmoz_samples.json';

    /** Libellés FR pour l’affichage (characteristic_key → label). */
    private const CHARACTERISTIC_KEY_LABELS = [
        'level_object' => 'Niveau',
        'pv_max_object' => 'Points de vie',
        'pa_object' => 'PA',
        'pm_object' => 'PM',
        'strong_object' => 'Force',
        'vitality_object' => 'Vitalité',
        'sagesse_object' => 'Sagesse',
        'chance_object' => 'Chance',
        'agi_object' => 'Agilité',
        'intel_object' => 'Intelligence',
        'invocation_object' => 'Invocation',
        'esquive_pa_object' => 'Esquive PA',
        'esquive_pm_object' => 'Esquive PM',
        'res_50_object' => 'Résistance %',
        'weight_object' => 'Pods',
        'ini_object' => 'Initiative',
        'res_fixe_terre_object' => 'Terre (fixe)',
        'res_fixe_feu_object' => 'Feu (fixe)',
        'res_fixe_eau_object' => 'Eau (fixe)',
        'res_fixe_air_object' => 'Air (fixe)',
    ];

    public function __construct(
        private CollectService $collectService,
        private DofusDbItemTypesCatalogService $itemTypesCatalog
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $outputPath = $this->option('output') ?: storage_path('app/characteristics_object_samples.json');
        $maxItems = (int) $this->option('max-items');
        $skipCache = (bool) $this->option('skip-cache');
        $chunkSize = min(500, max(1, (int) $this->option('chunk')));

        $equipmentTypeIds = $this->resolveEquipmentTypeIds($skipCache);
        $excludedTypeIds = $this->loadExcludedTypeIds();

        $equipmentTypeIds = array_values(array_diff($equipmentTypeIds, $excludedTypeIds));
        $this->info(sprintf('Équipements : %d typeIds (après exclusions).', count($equipmentTypeIds)));

        if (empty($equipmentTypeIds)) {
            $this->error('Aucun typeId équipement trouvé.');

            return self::FAILURE;
        }

        $levelValues = []; // level_object: level -> [level, level, ...] (identity)
        $effectValues = []; // effectId -> levelBucket -> [value, ...]
        $charValues = []; // dofusdb_characteristic_id (effect.characteristic) -> levelBucket -> [value, ...]

        $options = [
            'limit' => 0,
            'page_size' => 50,
            'max_items' => $maxItems,
            'skip_cache' => $skipCache,
        ];

        $typeIdChunks = array_chunk($equipmentTypeIds, $chunkSize);
        $totalCollected = 0;

        foreach ($typeIdChunks as $i => $chunk) {
            if ($totalCollected >= $maxItems) {
                break;
            }
            $remaining = $maxItems - $totalCollected;
            $chunkOptions = array_merge($options, ['max_items' => $remaining]);
            $this->info(sprintf('Chunk typeIds %d/%d (%d types, max_items=%d)...', $i + 1, count($typeIdChunks), count($chunk), $remaining));
            $result = $this->collectService->fetchManyResult('dofusdb', 'item', ['typeIds' => $chunk], $chunkOptions);
            $items = $result['items'] ?? [];
            $meta = $result['meta'] ?? [];
            $collected = $meta['returned'] ?? count($items);
            $totalCollected += $collected;

            foreach ($items as $item) {
                $this->processItem($item, $levelValues, $effectValues, $charValues);
            }

            if ($collected === 0) {
                break;
            }
        }

        $this->info(sprintf('Items traités : %d', $totalCollected));

        $mapping = $this->loadDofusdbCharacteristicMapping();
        $byCharacteristicKey = $this->aggregateLevelObject($levelValues);
        $byEffectId = $this->aggregateByEffectId($effectValues);
        $byDofusdbCharId = $this->aggregateByDofusdbCharacteristic($charValues);

        $byCharacteristicKey = $this->mergeSamplesByCharacteristicKey($byCharacteristicKey, $byDofusdbCharId, $mapping);
        $byCharacteristicKey = $this->addReferenceSamples($byCharacteristicKey);
        $krosmozSamples = $this->loadKrosmozSamples();
        $byCharacteristicKey = $this->mergeKrosmozSamples($byCharacteristicKey, $krosmozSamples);
        $characteristicKeysWithoutSamples = $this->characteristicKeysWithoutSamples($mapping, $byCharacteristicKey);
        $characteristicKeyMetadata = $this->buildCharacteristicKeyMetadata($byCharacteristicKey);

        $payload = [
            'source' => 'dofusdb',
            'entity' => 'item',
            'scope' => 'equipment',
            'meta' => [
                'description' => 'Samples niveau→valeur Dofus pour les caractéristiques object (équipements). Utiliser by_characteristic_key[clé].conversion_dofus_sample_reference pour pré-remplir l’admin (niveaux 1, 40, 80, 120, 160, 200).',
                'extracted_at' => now()->toIso8601String(),
                'equipment_super_type_ids' => self::EQUIPMENT_SUPER_TYPE_IDS,
                'equipment_type_ids_count' => count($equipmentTypeIds),
                'excluded_type_ids' => $excludedTypeIds,
                'item_count' => $totalCollected,
                'level_buckets' => self::LEVEL_BUCKETS,
                'reference_levels' => self::REFERENCE_LEVELS,
                'reference_levels_krosmoz' => self::KROSMOZ_REFERENCE_LEVELS,
                'level_object_sample_levels' => array_keys($byCharacteristicKey['level_object']['conversion_dofus_sample'] ?? []),
                'characteristic_keys_without_samples' => $characteristicKeysWithoutSamples,
            ],
            'notation' => [
                'dofusdb_characteristic_id' => 'id de GET /characteristics (et item.effects[].characteristic)',
                'characteristic_key' => 'clé Krosmoz (ex: pa_object, strong_object)',
                'conversion_dofus_sample' => 'objet { niveau_dofus: valeur_moyenne } par tranche de niveau',
                'conversion_dofus_sample_reference' => 'sous-ensemble aux niveaux reference_levels (1, 40, 80, 120, 160, 200), avec interpolation si besoin.',
                'conversion_krosmoz_sample' => 'objet { niveau_krosmoz: valeur_cible } (niveaux 1, 4, 8, 12, 16, 20), source règles JDR.',
                'conversion_krosmoz_sample_reference' => 'valeurs cibles fiables pour la conversion (même niveaux).',
            ],
            'dofusdb_characteristic_id_to_characteristic_key' => $mapping,
            'characteristic_key_metadata' => $characteristicKeyMetadata,
            'by_characteristic_key' => $byCharacteristicKey,
            'by_dofusdb_characteristic_id' => $byDofusdbCharId,
            'by_effect_id' => $byEffectId,
        ];

        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents(
            $outputPath,
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
        );
        $this->info(sprintf('Écrit : %s', $outputPath));

        return self::SUCCESS;
    }

    /**
     * @return array<int>
     */
    private function resolveEquipmentTypeIds(bool $skipCache): array
    {
        return $this->itemTypesCatalog->getTypeIdsForSuperTypes(
            self::EQUIPMENT_SUPER_TYPE_IDS,
            'fr',
            $skipCache
        );
    }

    /**
     * @return array<int>
     */
    private function loadExcludedTypeIds(): array
    {
        $path = base_path('resources/scrapping/config/sources/dofusdb/item-super-types.json');
        if (!is_file($path)) {
            $path = base_path('resources/scrapping/sources/dofusdb/item-super-types.json');
        }
        if (!is_file($path)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($path), true);
        $ids = $data['excludedTypeIds'] ?? [];

        return array_map('intval', is_array($ids) ? $ids : []);
    }

    /**
     * @param array<string, mixed> $item
     * @param array<int, list<int>> $levelValues level -> [level, ...]
     * @param array<int, array<int, list<int>>> $effectValues effectId -> levelBucket -> [value, ...]
     * @param array<int, array<int, list<int>>> $charValues dofusdb_characteristic_id -> levelBucket -> [value, ...]
     */
    private function processItem(array $item, array &$levelValues, array &$effectValues, array &$charValues): void
    {
        $level = isset($item['level']) ? (int) $item['level'] : 0;
        if ($level < 1) {
            return;
        }

        $levelBucket = $this->levelToBucket($level);
        if (!isset($levelValues[$levelBucket])) {
            $levelValues[$levelBucket] = [];
        }
        $levelValues[$levelBucket][] = $level;

        $effects = $item['effects'] ?? [];
        if (!is_array($effects)) {
            return;
        }

        foreach ($effects as $e) {
            if (!is_array($e)) {
                continue;
            }
            $effectId = (int) ($e['effectId'] ?? $e['effect_id'] ?? 0);
            $charId = isset($e['characteristic']) ? (int) $e['characteristic'] : -1;
            $value = null;
            if (isset($e['value']) && (is_int($e['value']) || is_numeric($e['value']))) {
                $value = (int) round((float) $e['value']);
            } elseif (array_key_exists('from', $e) && array_key_exists('to', $e)) {
                $from = is_numeric($e['from']) ? (float) $e['from'] : 0;
                $to = is_numeric($e['to']) ? (float) $e['to'] : 0;
                $value = (int) round(($from + $to) / 2);
            } elseif (isset($e['min'], $e['max'])) {
                $min = is_numeric($e['min']) ? (float) $e['min'] : 0;
                $max = is_numeric($e['max']) ? (float) $e['max'] : 0;
                $value = (int) round(($min + $max) / 2);
            }
            if ($value === null) {
                continue;
            }
            if ($effectId > 0) {
                if (!isset($effectValues[$effectId])) {
                    $effectValues[$effectId] = [];
                }
                if (!isset($effectValues[$effectId][$levelBucket])) {
                    $effectValues[$effectId][$levelBucket] = [];
                }
                $effectValues[$effectId][$levelBucket][] = $value;
            }
            if ($charId >= 0) {
                if (!isset($charValues[$charId])) {
                    $charValues[$charId] = [];
                }
                if (!isset($charValues[$charId][$levelBucket])) {
                    $charValues[$charId][$levelBucket] = [];
                }
                $charValues[$charId][$levelBucket][] = $value;
            }
        }
    }

    private function levelToBucket(int $level): int
    {
        foreach (array_reverse(self::LEVEL_BUCKETS, true) as $bucket) {
            if ($level >= $bucket) {
                return $bucket;
            }
        }

        return self::LEVEL_BUCKETS[0];
    }

    /**
     * @return array<string, string> dofusdb_characteristic_id (string) => characteristic_key
     */
    private function loadDofusdbCharacteristicMapping(): array
    {
        $path = base_path('resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json');
        if (!is_file($path)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($path), true);
        $mapping = $data['mapping'] ?? [];

        return is_array($mapping) ? $mapping : [];
    }

    /**
     * @param array<int, array<int, list<int>>> $charValues
     * @return array<string, array{conversion_dofus_sample: array<string, int>, item_count: int, level_buckets: list<int>}>
     */
    private function aggregateByDofusdbCharacteristic(array $charValues): array
    {
        $out = [];
        foreach ($charValues as $charId => $byBucket) {
            $sample = [];
            $totalCount = 0;
            foreach ($byBucket as $bucket => $values) {
                if ($values !== []) {
                    $sample[(string) $bucket] = (int) round(array_sum($values) / count($values));
                    $totalCount += count($values);
                }
            }
            ksort($sample, SORT_NUMERIC);
            $out[(string) $charId] = [
                'conversion_dofus_sample' => $sample,
                'item_count' => $totalCount,
                'level_buckets' => array_map('intval', array_keys($sample)),
            ];
        }
        ksort($out, SORT_NUMERIC);

        return $out;
    }

    /**
     * Fusionne les samples par dofusdb_characteristic_id dans by_characteristic_key via le mapping.
     * Si plusieurs ids mappent sur la même clé (ex: 33-37 → res_50_object), les valeurs sont moyennées par tranche.
     *
     * @param array<string, array{conversion_dofus_sample: array<string, int>, raw_level_counts?: array<string, int>}> $byCharacteristicKey
     * @param array<string, array{conversion_dofus_sample: array<string, int>, item_count: int, level_buckets: list<int>}> $byDofusdbCharId
     * @param array<string, string> $mapping
     * @return array<string, array{conversion_dofus_sample: array<string, int>, raw_level_counts?: array<string, int>, item_count?: int, level_buckets?: list<int>}>
     */
    private function mergeSamplesByCharacteristicKey(array $byCharacteristicKey, array $byDofusdbCharId, array $mapping): array
    {
        $keyToCharIds = [];
        foreach ($mapping as $charId => $characteristicKey) {
            if (isset($byDofusdbCharId[$charId])) {
                $keyToCharIds[$characteristicKey][] = $charId;
            }
        }
        foreach ($keyToCharIds as $characteristicKey => $charIds) {
            $mergedSample = [];
            $mergedCount = 0;
            foreach ($charIds as $charId) {
                $data = $byDofusdbCharId[$charId];
                foreach ($data['conversion_dofus_sample'] as $level => $value) {
                    if (!isset($mergedSample[$level])) {
                        $mergedSample[$level] = ['sum' => 0, 'count' => 0];
                    }
                    $mergedSample[$level]['sum'] += $value;
                    $mergedSample[$level]['count']++;
                }
                $mergedCount += $data['item_count'];
            }
            $sample = [];
            foreach ($mergedSample as $level => $agg) {
                $sample[$level] = (int) round($agg['sum'] / $agg['count']);
            }
            ksort($sample, SORT_NUMERIC);
            $byCharacteristicKey[$characteristicKey] = [
                'conversion_dofus_sample' => $sample,
                'item_count' => $mergedCount,
                'level_buckets' => array_map('intval', array_keys($sample)),
            ];
        }

        return $byCharacteristicKey;
    }

    /**
     * Ajoute conversion_dofus_sample_reference (niveaux 1, 40, 80, 120, 160, 200) avec interpolation si besoin.
     *
     * @param array<string, array<string, mixed>> $byCharacteristicKey
     * @return array<string, array<string, mixed>>
     */
    private function addReferenceSamples(array $byCharacteristicKey): array
    {
        foreach ($byCharacteristicKey as $key => $data) {
            $sample = $data['conversion_dofus_sample'] ?? [];
            if (!is_array($sample) || $sample === []) {
                $byCharacteristicKey[$key]['conversion_dofus_sample_reference'] = (object) [];
                continue;
            }
            $sortedLevels = array_map('intval', array_keys($sample));
            sort($sortedLevels, SORT_NUMERIC);
            $ref = [];
            foreach (self::REFERENCE_LEVELS as $level) {
                $levelStr = (string) $level;
                if (isset($sample[$levelStr])) {
                    $ref[$levelStr] = (int) $sample[$levelStr];
                    continue;
                }
                $interpolated = $this->interpolateAtLevel($sample, $sortedLevels, $level);
                if ($interpolated !== null) {
                    $ref[$levelStr] = (int) round($interpolated);
                }
            }
            $byCharacteristicKey[$key]['conversion_dofus_sample_reference'] = $ref;
        }

        return $byCharacteristicKey;
    }

    /**
     * Charge les samples Krosmoz (règles 2.2.1 / 2.2.2) depuis le fichier JSON.
     *
     * @return array<string, array{conversion_krosmoz_sample: array<string, int>, source_rule?: string}>
     */
    private function loadKrosmozSamples(): array
    {
        $path = base_path(self::KROSMOZ_SAMPLES_PATH);
        if (!is_file($path)) {
            return [];
        }
        $json = file_get_contents($path);
        $data = json_decode($json, true);
        if (!is_array($data) || !isset($data['by_characteristic_key'])) {
            return [];
        }
        $out = [];
        foreach ($data['by_characteristic_key'] as $key => $entry) {
            if (!is_array($entry) || !isset($entry['conversion_krosmoz_sample'])) {
                continue;
            }
            $sample = $entry['conversion_krosmoz_sample'];
            if (!is_array($sample)) {
                continue;
            }
            $out[$key] = [
                'conversion_krosmoz_sample' => array_map('intval', $sample),
                'source_rule' => $entry['source_rule'] ?? null,
            ];
        }

        return $out;
    }

    /**
     * Fusionne les samples Krosmoz dans by_characteristic_key (conversion_krosmoz_sample + conversion_krosmoz_sample_reference).
     *
     * @param array<string, array<string, mixed>> $byCharacteristicKey
     * @param array<string, array{conversion_krosmoz_sample: array<string, int>, source_rule?: string}> $krosmozSamples
     * @return array<string, array<string, mixed>>
     */
    private function mergeKrosmozSamples(array $byCharacteristicKey, array $krosmozSamples): array
    {
        foreach ($krosmozSamples as $key => $data) {
            $sample = $data['conversion_krosmoz_sample'] ?? [];
            if ($sample === []) {
                continue;
            }
            if (!isset($byCharacteristicKey[$key])) {
                $byCharacteristicKey[$key] = [];
            }
            $byCharacteristicKey[$key]['conversion_krosmoz_sample'] = $sample;
            $byCharacteristicKey[$key]['conversion_krosmoz_sample_reference'] = $sample;
            if (isset($data['source_rule']) && $data['source_rule'] !== null) {
                $byCharacteristicKey[$key]['conversion_krosmoz_source_rule'] = $data['source_rule'];
            }
        }

        return $byCharacteristicKey;
    }

    /**
     * Interpolation linéaire de la valeur au niveau $level à partir des buckets existants.
     */
    private function interpolateAtLevel(array $sample, array $sortedLevels, int $level): ?float
    {
        if ($sortedLevels === []) {
            return null;
        }
        $min = (int) $sortedLevels[0];
        $max = (int) $sortedLevels[array_key_last($sortedLevels)];
        if ($level <= $min) {
            return (float) ($sample[(string) $min] ?? 0);
        }
        if ($level >= $max) {
            return (float) ($sample[(string) $max] ?? 0);
        }
        $prev = $min;
        $next = $max;
        foreach ($sortedLevels as $l) {
            $l = (int) $l;
            if ($l <= $level) {
                $prev = $l;
            }
            if ($l >= $level && $next > $l) {
                $next = $l;
            }
        }
        if ($prev === $next) {
            return (float) ($sample[(string) $prev] ?? 0);
        }
        $vPrev = (float) ($sample[(string) $prev] ?? 0);
        $vNext = (float) ($sample[(string) $next] ?? 0);
        $ratio = ($level - $prev) / ($next - $prev);

        return $vPrev + ($vNext - $vPrev) * $ratio;
    }

    /**
     * Liste les characteristic_key du mapping qui n’ont aucun sample (jamais vus sur les équipements).
     *
     * @param array<string, string> $mapping
     * @param array<string, mixed> $byCharacteristicKey
     * @return list<string>
     */
    private function characteristicKeysWithoutSamples(array $mapping, array $byCharacteristicKey): array
    {
        $mappedKeys = array_values(array_unique($mapping));
        $without = [];
        foreach ($mappedKeys as $key) {
            if (!isset($byCharacteristicKey[$key])) {
                $without[] = $key;
            }
        }
        sort($without);

        return $without;
    }

    /**
     * Métadonnées par characteristic_key (label_fr) pour affichage.
     *
     * @param array<string, mixed> $byCharacteristicKey
     * @return array<string, array{label_fr: string}>
     */
    private function buildCharacteristicKeyMetadata(array $byCharacteristicKey): array
    {
        $out = [];
        foreach (array_keys($byCharacteristicKey) as $key) {
            $out[$key] = [
                'label_fr' => self::CHARACTERISTIC_KEY_LABELS[$key] ?? $key,
            ];
        }

        return $out;
    }

    /**
     * @param array<int, list<int>> $levelValues
     * @return array<string, array{conversion_dofus_sample: array<string, int>, raw_level_counts?: array<string, int>}>
     */
    private function aggregateLevelObject(array $levelValues): array
    {
        $sample = [];
        $rawCounts = [];
        foreach ($levelValues as $bucket => $values) {
            if ($values !== []) {
                $sample[(string) $bucket] = (int) round(array_sum($values) / count($values));
                $rawCounts[(string) $bucket] = count($values);
            }
        }
        ksort($sample, SORT_NUMERIC);
        ksort($rawCounts, SORT_NUMERIC);

        return [
            'level_object' => [
                'conversion_dofus_sample' => $sample,
                'raw_level_counts' => $rawCounts,
            ],
        ];
    }

    /**
     * @param array<int, array<int, list<int>>> $effectValues
     * @return array<string, array{conversion_dofus_sample: array<string, int>, item_count: int, level_buckets: list<int>}>
     */
    private function aggregateByEffectId(array $effectValues): array
    {
        $out = [];
        foreach ($effectValues as $effectId => $byBucket) {
            $sample = [];
            $totalCount = 0;
            foreach ($byBucket as $bucket => $values) {
                if ($values !== []) {
                    $sample[(string) $bucket] = (int) round(array_sum($values) / count($values));
                    $totalCount += count($values);
                }
            }
            ksort($sample, SORT_NUMERIC);
            $out[(string) $effectId] = [
                'conversion_dofus_sample' => $sample,
                'item_count' => $totalCount,
                'level_buckets' => array_map('intval', array_keys($sample)),
            ];
        }
        ksort($out, SORT_NUMERIC);

        return $out;
    }
}
