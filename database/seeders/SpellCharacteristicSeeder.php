<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use App\Models\CharacteristicSpell;
use Illuminate\Database\Seeder;

/**
 * Seed characteristic_spell (groupe spell).
 */
class SpellCharacteristicSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/data/characteristic_spell.php');
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
            CharacteristicSpell::updateOrCreate(
                [
                    'characteristic_id' => $char->id,
                    'entity' => $row['entity'] ?? 'spell',
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
                ]
            );
        }
        if ($this->command) {
            $this->command->info('SpellCharacteristicSeeder : ' . count($rows) . ' ligne(s).');
        }
    }
}
