<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EntityCharacteristic;
use Illuminate\Database\Seeder;

/**
 * Importe database/seeders/data/entity_characteristics.php vers entity_characteristics.
 *
 * Pour régénérer le fichier depuis la BDD (après modification via l'interface) :
 * php artisan db:export-seeder-data --characteristics
 */
class EntityCharacteristicSeeder extends Seeder
{
    private const DATA_FILE = 'database/seeders/data/entity_characteristics.php';

    public function run(): void
    {
        $path = base_path(self::DATA_FILE);
        if (! is_file($path)) {
            if ($this->command) {
                $this->command->warn('Fichier absent : ' . self::DATA_FILE . '. Exécutez : php artisan db:export-seeder-data --characteristics');
            }

            return;
        }

        $rows = require $path;
        if (! is_array($rows)) {
            $rows = [];
        }

        foreach ($rows as $row) {
            if (! is_array($row) || empty($row['entity'] ?? null) || empty($row['characteristic_key'] ?? null)) {
                continue;
            }

            EntityCharacteristic::updateOrCreate(
                [
                    'entity' => $row['entity'],
                    'characteristic_key' => $row['characteristic_key'],
                ],
                [
                    'name' => $row['name'] ?? $row['characteristic_key'],
                    'short_name' => $row['short_name'] ?? null,
                    'helper' => $row['helper'] ?? null,
                    'descriptions' => $row['descriptions'] ?? null,
                    'icon' => $row['icon'] ?? null,
                    'color' => $row['color'] ?? null,
                    'unit' => $row['unit'] ?? null,
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'db_column' => $row['db_column'] ?? null,
                    'type' => $row['type'] ?? 'string',
                    'min' => isset($row['min']) ? (int) $row['min'] : null,
                    'max' => isset($row['max']) ? (int) $row['max'] : null,
                    'formula' => $row['formula'] ?? null,
                    'formula_display' => $row['formula_display'] ?? null,
                    'computation' => $row['computation'] ?? null,
                    'default_value' => isset($row['default_value']) ? (string) $row['default_value'] : null,
                    'required' => (bool) ($row['required'] ?? false),
                    'validation_message' => $row['validation_message'] ?? null,
                    'forgemagie_allowed' => (bool) ($row['forgemagie_allowed'] ?? false),
                    'forgemagie_max' => (int) ($row['forgemagie_max'] ?? 0),
                    'base_price_per_unit' => isset($row['base_price_per_unit']) ? (float) $row['base_price_per_unit'] : null,
                    'rune_price_per_unit' => isset($row['rune_price_per_unit']) ? (float) $row['rune_price_per_unit'] : null,
                    'applies_to' => $row['applies_to'] ?? null,
                    'is_competence' => (bool) ($row['is_competence'] ?? false),
                    'characteristic_id' => $row['characteristic_id'] ?? null,
                    'alternative_characteristic_id' => $row['alternative_characteristic_id'] ?? null,
                    'skill_type' => $row['skill_type'] ?? null,
                    'value_available' => $row['value_available'] ?? null,
                    'labels' => $row['labels'] ?? null,
                    'validation' => $row['validation'] ?? null,
                    'mastery_value_available' => $row['mastery_value_available'] ?? null,
                    'mastery_labels' => $row['mastery_labels'] ?? null,
                ]
            );
        }

        if ($this->command) {
            $this->command->info('EntityCharacteristicSeeder : ' . count($rows) . ' ligne(s) importée(s).');
        }
    }
}
