<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use Illuminate\Database\Seeder;

/**
 * Seed la table générale characteristics.
 */
class CharacteristicSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/data/characteristics.php');
        if (! is_file($path)) {
            return;
        }
        $rows = require $path;
        if (! is_array($rows)) {
            $rows = [];
        }
        foreach ($rows as $row) {
            Characteristic::updateOrCreate(
                ['key' => $row['key']],
                [
                    'name' => $row['name'],
                    'short_name' => $row['short_name'] ?? null,
                    'helper' => $row['helper'] ?? null,
                    'descriptions' => $row['descriptions'] ?? null,
                    'icon' => $row['icon'] ?? null,
                    'color' => $row['color'] ?? null,
                    'unit' => $row['unit'] ?? null,
                    'type' => $row['type'] ?? 'string',
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                ]
            );
        }
        if ($this->command) {
            $this->command->info('CharacteristicSeeder : ' . count($rows) . ' ligne(s).');
        }
    }
}
