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
 * Fichier généré par : php artisan db:export-seeder-data --scrapping-mappings
 * (après modification des règles via l'UI admin « Mapping scrapping »).
 *
 * @see docs/50-Fonctionnalités/VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md
 */
class ScrappingEntityMappingSeeder extends Seeder
{
    use LoadsSeederDataFile;

    private const DATA_FILE = 'database/seeders/data/scrapping_entity_mappings.php';

    public function run(): void
    {
        $rows = $this->loadDataFile(self::DATA_FILE);
        if ($rows === []) {
            $this->command?->info('Aucun fichier scrapping_entity_mappings.php ou fichier vide. Passez.');

            return;
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
}
