<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Scrapping\Core\Collect\CollectService;
use Illuminate\Console\Command;

/**
 * Extrait les samples de conversion Dofus → Krosmoz pour les caractéristiques creature (monstres)
 * à partir des grades des monstres DofusDB, fusionne avec les samples Krosmoz (règles), et écrit un JSON.
 *
 * Ne modifie pas le seeder ni la BDD ; sortie : storage/app/characteristics_creature_samples.json
 */
class ExtractCreatureConversionSamplesCommand extends Command
{
    protected $signature = 'characteristics:extract-creature-samples
                            {--output= : Chemin du fichier JSON de sortie (défaut: storage/app/characteristics_creature_samples.json)}
                            {--max-monsters=3000 : Nombre max de monstres à récupérer}
                            {--skip-cache : Ne pas utiliser le cache HTTP}
                            {--page-size=50 : Taille de page pour l\'API}';

    protected $description = 'Extrait les samples de conversion (niveau/valeur) pour les caractéristiques creature depuis les monstres DofusDB (grades) et les écrit dans un fichier JSON';

    private const LEVEL_BUCKETS = [1, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200];

    private const REFERENCE_LEVELS = [1, 40, 80, 120, 160, 200];

    private const KROSMOZ_REFERENCE_LEVELS = [1, 4, 8, 12, 16, 20];

    private const GRADE_MAPPING_PATH = 'resources/scrapping/config/sources/dofusdb/dofusdb_monster_grade_to_creature.json';

    private const KROSMOZ_SAMPLES_PATH = 'resources/scrapping/config/sources/krosmoz/creature_krosmoz_samples.json';

    public function __construct(
        private CollectService $collectService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $outputPath = $this->option('output') ?: storage_path('app/characteristics_creature_samples.json');
        $maxMonsters = (int) $this->option('max-monsters');
        $skipCache = (bool) $this->option('skip-cache');
        $pageSize = min(100, max(1, (int) $this->option('page-size')));

        $mapping = $this->loadGradeMapping();
        if ($mapping === []) {
            $this->error('Mapping grade → characteristic_key introuvable : ' . self::GRADE_MAPPING_PATH);

            return self::FAILURE;
        }

        $charValues = []; // characteristic_key -> levelBucket -> [value, ...]

        $options = [
            'limit' => 0,
            'page_size' => $pageSize,
            'max_items' => $maxMonsters,
            'skip_cache' => $skipCache,
        ];

        $this->info('Récupération des monstres DofusDB...');
        $result = $this->collectService->fetchManyResult('dofusdb', 'monster', [], $options);
        $monsters = $result['items'] ?? [];
        $returned = $result['meta']['returned'] ?? count($monsters);

        $this->info(sprintf('Monstres récupérés : %d', $returned));

        $gradeCount = 0;
        foreach ($monsters as $monster) {
            $grades = $monster['grades'] ?? [];
            if (! is_array($grades)) {
                continue;
            }
            foreach ($grades as $grade) {
                if (! is_array($grade)) {
                    continue;
                }
                $this->processGrade($grade, $mapping, $charValues);
                $gradeCount++;
            }
        }

        $this->info(sprintf('Grades traités : %d', $gradeCount));

        $byCharacteristicKey = $this->aggregateByCharacteristicKey($charValues);
        $byCharacteristicKey = $this->addReferenceSamples($byCharacteristicKey);
        $krosmozSamples = $this->loadKrosmozSamples();
        $byCharacteristicKey = $this->mergeKrosmozSamples($byCharacteristicKey, $krosmozSamples);

        $payload = [
            'source' => 'dofusdb',
            'entity' => 'monster',
            'scope' => 'grades',
            'meta' => [
                'description' => 'Samples Dofus (niveaux 1–200) + Krosmoz (1, 4, 8, 12, 16, 20) pour caractéristiques creature.',
                'extracted_at' => now()->toIso8601String(),
                'monster_count' => $returned,
                'grade_count' => $gradeCount,
                'level_buckets' => self::LEVEL_BUCKETS,
                'reference_levels' => self::REFERENCE_LEVELS,
                'reference_levels_krosmoz' => self::KROSMOZ_REFERENCE_LEVELS,
            ],
            'notation' => [
                'characteristic_key' => 'clé Krosmoz creature (ex: level_creature, pa_creature)',
                'conversion_dofus_sample' => 'objet { niveau_dofus: valeur_moyenne } (niveau = grade.level)',
                'conversion_dofus_sample_reference' => 'sous-ensemble aux niveaux 1, 40, 80, 120, 160, 200',
                'conversion_krosmoz_sample' => 'objet { niveau_krosmoz: valeur_cible } (règles JDR)',
                'conversion_krosmoz_sample_reference' => 'valeurs cibles pour la conversion',
            ],
            'grade_field_to_characteristic_key' => $mapping,
            'by_characteristic_key' => $byCharacteristicKey,
        ];

        $dir = dirname($outputPath);
        if (! is_dir($dir)) {
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
     * @return array<string, string> grade field name => characteristic_key
     */
    private function loadGradeMapping(): array
    {
        $path = base_path(self::GRADE_MAPPING_PATH);
        if (! is_file($path)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($path), true);
        $mapping = $data['mapping'] ?? [];

        return is_array($mapping) ? $mapping : [];
    }

    /**
     * Lit une valeur dans le grade par clé simple ou chemin (ex. "bonusCharacteristics.tackleBlock").
     *
     * @param array<string, mixed> $grade
     * @return mixed
     */
    private function getGradeValueByPath(array $grade, string $field): mixed
    {
        if (str_contains($field, '.')) {
            $parts = explode('.', $field);
            $current = $grade;
            foreach ($parts as $part) {
                if (! is_array($current) || ! array_key_exists($part, $current)) {
                    return null;
                }
                $current = $current[$part];
            }

            return $current;
        }

        return $grade[$field] ?? null;
    }

    /**
     * @param array<string, mixed> $grade
     * @param array<string, string> $mapping
     * @param array<string, array<int, list<int|float>>> $charValues
     */
    private function processGrade(array $grade, array $mapping, array &$charValues): void
    {
        $level = isset($grade['level']) ? (int) $grade['level'] : 0;
        if ($level < 1) {
            return;
        }

        $levelBucket = $this->levelToBucket($level);

        foreach ($mapping as $field => $characteristicKey) {
            $value = $this->getGradeValueByPath($grade, $field);
            if ($value === null) {
                continue;
            }
            if (is_numeric($value)) {
                $value = (float) $value;
            } else {
                continue;
            }
            if (! isset($charValues[$characteristicKey])) {
                $charValues[$characteristicKey] = [];
            }
            if (! isset($charValues[$characteristicKey][$levelBucket])) {
                $charValues[$characteristicKey][$levelBucket] = [];
            }
            $charValues[$characteristicKey][$levelBucket][] = $value;
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
     * @param array<string, array<int, list<int|float>>> $charValues
     * @return array<string, array{conversion_dofus_sample: array<string, int>, item_count: int, level_buckets: list<int>}>
     */
    private function aggregateByCharacteristicKey(array $charValues): array
    {
        $out = [];
        foreach ($charValues as $characteristicKey => $byBucket) {
            $sample = [];
            $totalCount = 0;
            foreach ($byBucket as $bucket => $values) {
                if ($values !== []) {
                    $sample[(string) $bucket] = (int) round(array_sum($values) / count($values));
                    $totalCount += count($values);
                }
            }
            ksort($sample, SORT_NUMERIC);
            $out[$characteristicKey] = [
                'conversion_dofus_sample' => $sample,
                'item_count' => $totalCount,
                'level_buckets' => array_map('intval', array_keys($sample)),
            ];
        }
        ksort($out);

        return $out;
    }

    /**
     * @param array<string, array{conversion_dofus_sample: array<string, int>, ...}> $byCharacteristicKey
     * @return array<string, array<string, mixed>>
     */
    private function addReferenceSamples(array $byCharacteristicKey): array
    {
        foreach ($byCharacteristicKey as $key => $data) {
            $sample = $data['conversion_dofus_sample'] ?? [];
            if (! is_array($sample) || $sample === []) {
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
     * @return array<string, array{conversion_krosmoz_sample: array<string, int>, source_rule?: string}>
     */
    private function loadKrosmozSamples(): array
    {
        $path = base_path(self::KROSMOZ_SAMPLES_PATH);
        if (! is_file($path)) {
            return [];
        }
        $json = file_get_contents($path);
        $data = json_decode($json, true);
        if (! is_array($data) || ! isset($data['by_characteristic_key'])) {
            return [];
        }
        $out = [];
        foreach ($data['by_characteristic_key'] as $key => $entry) {
            if (! is_array($entry) || ! isset($entry['conversion_krosmoz_sample'])) {
                continue;
            }
            $sample = $entry['conversion_krosmoz_sample'];
            if (! is_array($sample)) {
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
            if (! isset($byCharacteristicKey[$key])) {
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
}
