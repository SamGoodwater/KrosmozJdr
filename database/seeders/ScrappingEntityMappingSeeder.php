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
 * @see docs/50-Fonctionnalités/VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md
 */
class ScrappingEntityMappingSeeder extends Seeder
{
    use LoadsSeederDataFile;

    private const DATA_FILE = 'database/seeders/data/scrapping_entity_mappings.php';
    private const SOURCE_CONFIG_BASE = 'resources/scrapping/config/sources';

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
            $characteristicId = null;
            if (!empty($row['characteristic_key'])) {
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
                    if (!is_array($target)) {
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

        $this->command?->info('Scrapping entity mappings : ' . count($rows) . ' règle(s) importée(s).');
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
        $sourceDirs = glob($base . '/*', GLOB_ONLYDIR) ?: [];
        foreach ($sourceDirs as $sourceDir) {
            $source = basename($sourceDir);
            $entityFiles = glob($sourceDir . '/entities/*.json') ?: [];
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

                    $rows[] = [
                        'source' => $source,
                        'entity' => $entity,
                        'mapping_key' => isset($entry['key']) && is_string($entry['key']) && $entry['key'] !== '' ? $entry['key'] : $from['path'],
                        'from_path' => $from['path'],
                        'from_lang_aware' => (bool) ($from['langAware'] ?? false),
                        'characteristic_key' => null,
                        'formatters' => is_array($entry['formatters'] ?? null) ? $entry['formatters'] : null,
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
