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
        // 1) Création / mise à jour des caractéristiques sans gérer les liens
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
                    // Nouveau : groupe explicite ; peut rester null (calculé par inferPrimaryGroup côté service).
                    'group' => $row['group'] ?? null,
                    // Les liens sont gérés dans un second passage pour garantir que toutes les maîtres existent.
                    'linked_to_characteristic_id' => null,
                ]
            );
        }

        // 2) Deuxième passage : rattacher les caractéristiques liées à leur maître via linked_to_key
        foreach ($rows as $row) {
            if (empty($row['linked_to_key'])) {
                continue;
            }

            $master = Characteristic::where('key', $row['linked_to_key'])->first();
            if (! $master) {
                if ($this->command) {
                    $this->command->warn(sprintf(
                        'CharacteristicSeeder : caractéristique maître introuvable pour %s (linked_to_key=%s).',
                        $row['key'],
                        $row['linked_to_key']
                    ));
                }

                continue;
            }

            Characteristic::where('key', $row['key'])->update([
                'linked_to_characteristic_id' => $master->id,
            ]);
        }

        if ($this->command) {
            $this->command->info('CharacteristicSeeder : ' . count($rows) . ' ligne(s).');
        }
    }
}
