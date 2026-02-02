<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use App\Models\CharacteristicEntity;
use Illuminate\Database\Seeder;

/**
 * Importe config/characteristics.php (characteristics.characteristics) vers les tables characteristics et characteristic_entities.
 */
class CharacteristicConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $definitions = config('characteristics.characteristics', []);

        if (empty($definitions)) {
            if ($this->command) {
                $this->command->warn('Aucune définition dans config("characteristics.characteristics"). Vérifiez que la config est chargée.');
            }

            return;
        }

        foreach ($definitions as $id => $def) {
            if (! is_array($def)) {
                continue;
            }

            $forgemagie = $def['forgemagie'] ?? ['allowed' => false, 'max' => 0];

            $row = [
                'id' => $id,
                'db_column' => $def['db_column'] ?? $id,
                'name' => $def['name'] ?? $id,
                'short_name' => $def['short_name'] ?? null,
                'description' => $def['description'] ?? null,
                'type' => $def['type'] ?? 'int',
                'unit' => $def['unit'] ?? null,
                'icon' => $def['icon'] ?? null,
                'color' => $def['color'] ?? null,
                'sort_order' => (int) ($def['order'] ?? 0),
                'applies_to' => $def['applies_to'] ?? [],
                'is_competence' => (bool) ($def['is_competence'] ?? false),
                'characteristic_id' => $def['characteristic'] ?? null,
                'alternative_characteristic_id' => $def['alternative_characteristic'] ?? null,
                'skill_type' => $def['skill_type'] ?? null,
                'value_available' => $def['value_available'] ?? null,
                'labels' => $def['labels'] ?? null,
                'validation' => $def['validation'] ?? null,
                'mastery_value_available' => $def['mastery_value_available'] ?? null,
                'mastery_labels' => $def['mastery_labels'] ?? null,
            ];

            Characteristic::updateOrCreate(['id' => $id], $row);

            $entities = $def['entities'] ?? [];
            foreach ($entities as $entity => $entityDef) {
                if (! is_array($entityDef)) {
                    continue;
                }

                $defaultVal = $entityDef['default'] ?? $def['default'] ?? null;
                $defaultValue = $defaultVal !== null ? (string) $defaultVal : null;

                $entityRow = [
                    'min' => isset($entityDef['min']) ? (int) $entityDef['min'] : null,
                    'max' => isset($entityDef['max']) ? (int) $entityDef['max'] : null,
                    'formula' => $entityDef['formula'] ?? null,
                    'formula_display' => $entityDef['formula_display'] ?? null,
                    'default_value' => $defaultValue,
                    'required' => (bool) ($entityDef['required'] ?? false),
                    'validation_message' => $entityDef['validation_message'] ?? null,
                ];

                if ($entity === 'item') {
                    $entityRow['forgemagie_allowed'] = (bool) ($forgemagie['allowed'] ?? false);
                    $entityRow['forgemagie_max'] = (int) ($forgemagie['max'] ?? 0);
                    $entityRow['base_price_per_unit'] = isset($def['base_price_per_unit']) ? (float) $def['base_price_per_unit'] : null;
                    $entityRow['rune_price_per_unit'] = isset($def['rune_price_per_unit']) ? (float) $def['rune_price_per_unit'] : null;
                }

                CharacteristicEntity::updateOrCreate(
                    [
                        'characteristic_id' => $id,
                        'entity' => $entity,
                    ],
                    $entityRow
                );
            }
        }

        if ($this->command) {
            $this->command->info('CharacteristicConfigSeeder : ' . count($definitions) . ' caractéristiques importées.');
        }
    }
}
