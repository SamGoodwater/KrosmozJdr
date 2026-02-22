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

    private const ICONS_COLORS_FILE = 'database/seeders/data/characteristic_icons_colors.php';

    public function run(): void
    {
        $rows = $this->loadDataFile(self::DATA_FILE);
        $defaults = $this->loadIconsAndColorsDefaults();
        $icons = $defaults['icons'] ?? [];
        $colors = $defaults['colors'] ?? [];
        $descriptions = $defaults['descriptions'] ?? [];

        // 1) Création / mise à jour des caractéristiques sans gérer les liens
        foreach ($rows as $row) {
            $key = $row['key'] ?? '';
            Characteristic::updateOrCreate(
                ['key' => $key],
                [
                    'name' => $row['name'],
                    'short_name' => $row['short_name'] ?? null,
                    'helper' => $row['helper'] ?? null,
                    'descriptions' => $row['descriptions'] ?? $descriptions[$key] ?? null,
                    'icon' => $row['icon'] ?? $icons[$key] ?? null,
                    'color' => $row['color'] ?? $colors[$key] ?? null,
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

    /**
     * Charge le mapping clé → icône et clé → couleur (fichier characteristic_icons_colors.php).
     *
     * @return array{icons: array<string, string>, colors: array<string, string>, descriptions: array<string, string>}
     */
    private function loadIconsAndColorsDefaults(): array
    {
        $path = base_path(self::ICONS_COLORS_FILE);
        if (! is_file($path)) {
            return ['icons' => [], 'colors' => [], 'descriptions' => []];
        }

        $data = require $path;

        return [
            'icons' => is_array($data['icons'] ?? null) ? $data['icons'] : [],
            'colors' => is_array($data['colors'] ?? null) ? $data['colors'] : [],
            'descriptions' => is_array($data['descriptions'] ?? null) ? $data['descriptions'] : [],
        ];
    }
}
