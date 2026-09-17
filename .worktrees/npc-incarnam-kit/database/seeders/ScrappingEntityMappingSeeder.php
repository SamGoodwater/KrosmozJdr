<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use App\Models\Scrapping\ScrappingEntityMapping;
use App\Models\Scrapping\ScrappingEntityMappingTarget;
use Database\Seeders\Concerns\LoadsSeederDataFile;
use Illuminate\Database\Seeder;

/**
 * Seed des règles de mapping scrapping depuis database/seeders/data/scrapping_entity_mappings.php.
 *
 * Fichier généré par : php artisan scrapping:seeders:export --scrapping-mappings
 * (après modification des règles via l'UI admin « Mapping scrapping »).
 *
 * @see docs/features/scrapping/README.md
 */
class ScrappingEntityMappingSeeder extends Seeder
{
    use LoadsSeederDataFile;

    private const DATA_FILE = 'database/seeders/data/scrapping_entity_mappings.php';

    private const SOURCE_CONFIG_BASE = 'resources/scrapping/config/sources';

    /** @var array<string, string> */
    private const MONSTER_NUMERIC_CHARACTERISTICS = [
        'strength' => 'strength_creature',
        'intelligence' => 'intelligence_creature',
        'agility' => 'agility_creature',
        'wisdom' => 'wisdom_creature',
        'chance' => 'chance_creature',
        'pa' => 'action_points_creature',
        'pm' => 'movement_points_creature',
        'po' => 'range_creature',
        'dodge_pa' => 'dodge_action_points_creature',
        'dodge_pm' => 'dodge_movement_points_creature',
        'tackle' => 'tackle_creature',
        'dodge' => 'dodge_creature',
        'ini' => 'initiative_creature',
        'vitality' => 'vitality_creature',
        'res_neutre' => 'resistance_neutral_creature',
        'res_terre' => 'resistance_earth_creature',
        'res_feu' => 'resistance_fire_creature',
        'res_air' => 'resistance_air_creature',
        'res_eau' => 'resistance_water_creature',
        'critical_hit' => 'critical_hit_creature',
        'heal_bonus' => 'heal_bonus_creature',
    ];

    public function run(): void
    {
        $rows = $this->loadDataFile(self::DATA_FILE);
        if ($rows === []) {
            $rows = $this->loadRowsFromEntityJson();
            if ($rows === []) {
                $this->command?->info('Aucun mapping scrapping trouvé (data file + JSON). Passez.');

                return;
            }
            $this->command?->info('ScrappingEntityMappingSeeder : bootstrap depuis les JSON d’entité.');
        }

        ScrappingEntityMappingTarget::query()->delete();
        ScrappingEntityMapping::query()->delete();

        foreach ($rows as $row) {
            $row = $this->normalizeParametricMapping($row);
            $characteristicId = null;
            if (! empty($row['characteristic_key'])) {
                $char = Characteristic::where('key', (string) $row['characteristic_key'])->first();
                $characteristicId = $char?->id;
            }

            $rule = ScrappingEntityMapping::create([
                'source' => (string) ($row['source'] ?? 'dofusdb'),
                'entity' => (string) ($row['entity'] ?? ''),
                'mapping_key' => (string) ($row['mapping_key'] ?? ''),
                'from_path' => (string) ($row['from_path'] ?? ''),
                'from_lang_aware' => (bool) ($row['from_lang_aware'] ?? false),
                'characteristic_id' => $characteristicId,
                'formatters' => $row['formatters'] ?? null,
                'spell_level_aggregation' => isset($row['spell_level_aggregation']) ? (string) $row['spell_level_aggregation'] : null,
                'sort_order' => (int) ($row['sort_order'] ?? 0),
            ]);

            $targets = $row['targets'] ?? [];
            if (is_array($targets)) {
                foreach ($targets as $i => $target) {
                    if (! is_array($target)) {
                        continue;
                    }
                    $model = (string) ($target['target_model'] ?? '');
                    $field = (string) ($target['target_field'] ?? '');
                    if ($model === '' || $field === '') {
                        continue;
                    }
                    ScrappingEntityMappingTarget::create([
                        'scrapping_entity_mapping_id' => $rule->id,
                        'target_model' => $model,
                        'target_field' => $field,
                        'sort_order' => (int) ($target['sort_order'] ?? $i),
                    ]);
                }
            }
        }

        $this->command?->info('Scrapping entity mappings : '.count($rows).' règle(s) importée(s).');
    }

    /**
     * Normalise les anciens snapshots vers le formatter numérique paramétrable.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizeParametricMapping(array $row): array
    {
        if (($row['source'] ?? null) !== 'dofusdb' || ($row['entity'] ?? null) !== 'monster') {
            return $row;
        }

        $mappingKey = (string) ($row['mapping_key'] ?? '');
        if (in_array($mappingKey, ['dofusdb_id', 'name', 'description', 'image', 'size', 'race'], true)) {
            $row['characteristic_key'] = null;
        }
        $characteristicKey = self::MONSTER_NUMERIC_CHARACTERISTICS[$mappingKey] ?? null;
        if ($characteristicKey === null) {
            return $row;
        }

        $row['characteristic_key'] = $characteristicKey;
        $row['formatters'] = [['name' => 'convertCharacteristic', 'args' => []]];

        return $row;
    }

    /**
     * Bootstrap de secours: convertit les mappings JSON des entités en lignes seeder.
     *
     * @return list<array<string, mixed>>
     */
    private function loadRowsFromEntityJson(): array
    {
        $base = base_path(self::SOURCE_CONFIG_BASE);
        if (! is_dir($base)) {
            return [];
        }

        $rows = [];
        $sourceDirs = glob($base.'/*', GLOB_ONLYDIR) ?: [];
        foreach ($sourceDirs as $sourceDir) {
            $source = basename($sourceDir);
            $entityFiles = glob($sourceDir.'/entities/*.json') ?: [];
            foreach ($entityFiles as $entityFile) {
                $entityData = $this->readJsonFile($entityFile);
                if (! is_array($entityData)) {
                    continue;
                }
                $entity = isset($entityData['entity']) && is_string($entityData['entity'])
                    ? $entityData['entity']
                    : basename($entityFile, '.json');
                $mapping = $entityData['mapping'] ?? [];
                if (! is_array($mapping)) {
                    continue;
                }
                foreach ($mapping as $idx => $entry) {
                    if (! is_array($entry)) {
                        continue;
                    }
                    $from = $entry['from'] ?? null;
                    $to = $entry['to'] ?? null;
                    if (! is_array($from) || ! isset($from['path']) || ! is_string($from['path'])) {
                        continue;
                    }
                    if (! is_array($to) || $to === []) {
                        continue;
                    }

                    $targets = [];
                    foreach ($to as $targetIdx => $target) {
                        if (! is_array($target)) {
                            continue;
                        }
                        $targetModel = isset($target['model']) ? (string) $target['model'] : '';
                        $targetField = isset($target['field']) ? (string) $target['field'] : '';
                        if ($targetModel === '' || $targetField === '') {
                            continue;
                        }
                        $targets[] = [
                            'target_model' => $targetModel,
                            'target_field' => $targetField,
                            'sort_order' => $targetIdx,
                        ];
                    }
                    if ($targets === []) {
                        continue;
                    }

                    $formatters = is_array($entry['formatters'] ?? null) ? $entry['formatters'] : null;
                    $characteristicKey = isset($entry['characteristic_key']) && is_string($entry['characteristic_key'])
                        ? $entry['characteristic_key']
                        : $this->inferCharacteristicKeyFromFormatters($formatters);

                    $rows[] = [
                        'source' => $source,
                        'entity' => $entity,
                        'mapping_key' => isset($entry['key']) && is_string($entry['key']) && $entry['key'] !== '' ? $entry['key'] : $from['path'],
                        'from_path' => $from['path'],
                        'from_lang_aware' => (bool) ($from['langAware'] ?? false),
                        'characteristic_key' => $characteristicKey,
                        'formatters' => $formatters,
                        'spell_level_aggregation' => isset($entry['spell_level_aggregation']) && is_string($entry['spell_level_aggregation'])
                            ? $entry['spell_level_aggregation']
                            : null,
                        'sort_order' => is_numeric($idx) ? (int) $idx : 0,
                        'targets' => $targets,
                    ];
                }
            }
        }

        return $rows;
    }

    /**
     * @param  list<array<string, mixed>>|null  $formatters
     */
    private function inferCharacteristicKeyFromFormatters(?array $formatters): ?string
    {
        if ($formatters === null) {
            return null;
        }
        foreach ($formatters as $formatter) {
            if (! is_array($formatter)) {
                continue;
            }
            $name = $formatter['name'] ?? null;
            if (! in_array($name, ['clampToCharacteristic', 'convertCharacteristic'], true)) {
                continue;
            }
            $args = is_array($formatter['args'] ?? null) ? $formatter['args'] : [];
            $candidate = $args['characteristicId'] ?? $args['characteristic_key'] ?? null;
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readJsonFile(string $path): ?array
    {
        if (! is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }
}
