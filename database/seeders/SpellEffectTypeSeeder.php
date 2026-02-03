<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SpellEffectType;
use Illuminate\Database\Seeder;

/**
 * Seed des types d'effets de sort depuis database/seeders/data/spell_effect_types.php.
 *
 * Pour régénérer le fichier depuis la BDD (après modification via l'interface) :
 * php artisan db:export-seeder-data --spell-effect-types
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/TAXONOMIE_EFFETS_SORTS.md
 */
class SpellEffectTypeSeeder extends Seeder
{
    private const DATA_FILE = 'database/seeders/data/spell_effect_types.php';

    public function run(): void
    {
        $path = base_path(self::DATA_FILE);
        if (! is_file($path)) {
            if ($this->command) {
                $this->command->warn('Fichier absent : ' . self::DATA_FILE);
            }

            return;
        }

        $types = require $path;
        if (! is_array($types)) {
            $types = [];
        }

        foreach ($types as $row) {
            SpellEffectType::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'category' => $row['category'],
                    'description' => $row['description'] ?? null,
                    'value_type' => $row['value_type'] ?? 'fixed',
                    'element' => $row['element'] ?? null,
                    'unit' => $row['unit'] ?? null,
                    'is_positive' => (bool) ($row['is_positive'] ?? false),
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'dofusdb_effect_id' => $row['dofusdb_effect_id'] ?? null,
                ]
            );
        }

        if ($this->command) {
            $this->command->info('SpellEffectTypeSeeder : ' . count($types) . ' types d\'effets créés.');
        }
    }
}
