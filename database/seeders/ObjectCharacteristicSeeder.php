<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use App\Models\CharacteristicObject;
use Illuminate\Database\Seeder;

/**
 * Seed characteristic_object (groupe object : item, consumable, resource, panoply).
 */
class ObjectCharacteristicSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/data/characteristic_object.php');
        if (! is_file($path)) {
            return;
        }
        $rows = require $path;
        if (! is_array($rows)) {
            $rows = [];
        }
        foreach ($rows as $row) {
            $char = Characteristic::where('key', $row['characteristic_key'])->first();
            if ($char === null) {
                continue;
            }
            CharacteristicObject::updateOrCreate(
                [
                    'characteristic_id' => $char->id,
                    'entity' => $row['entity'],
                ],
                [
                    'db_column' => $row['db_column'] ?? null,
                    'min' => $row['min'] ?? null,
                    'max' => $row['max'] ?? null,
                    'formula' => $row['formula'] ?? null,
                    'formula_display' => $row['formula_display'] ?? null,
                    'default_value' => $row['default_value'] ?? null,
                    'required' => (bool) ($row['required'] ?? false),
                    'validation_message' => $row['validation_message'] ?? null,
                    'conversion_formula' => $row['conversion_formula'] ?? null,
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'forgemagie_allowed' => (bool) ($row['forgemagie_allowed'] ?? false),
                    'forgemagie_max' => (int) ($row['forgemagie_max'] ?? 0),
                    'base_price_per_unit' => isset($row['base_price_per_unit']) ? (float) $row['base_price_per_unit'] : null,
                    'rune_price_per_unit' => isset($row['rune_price_per_unit']) ? (float) $row['rune_price_per_unit'] : null,
                ]
            );
        }
        if ($this->command) {
            $this->command->info('ObjectCharacteristicSeeder : ' . count($rows) . ' ligne(s).');
        }
    }
}
