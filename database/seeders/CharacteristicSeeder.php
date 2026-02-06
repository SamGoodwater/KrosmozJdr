<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use Database\Seeders\Concerns\LoadsSeederDataFile;
use Illuminate\Database\Seeder;

/**
 * Seed la table générale characteristics.
 */
class CharacteristicSeeder extends Seeder
{
    use LoadsSeederDataFile;

    private const DATA_FILE = 'database/seeders/data/characteristics.php';

    public function run(): void
    {
        $rows = $this->loadDataFile(self::DATA_FILE);
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
