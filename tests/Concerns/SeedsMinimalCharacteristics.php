<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;

/**
 * Fournit le seed minimal des caractéristiques pour les tests lorsque les fichiers
 * database/seeders/data/*.php sont vides ou absents.
 */
trait SeedsMinimalCharacteristics
{
    protected function seedMinimalCharacteristicsIfEmpty(): void
    {
        if (Characteristic::where('key', 'life_creature')->exists()) {
            return;
        }

        $life = Characteristic::create([
            'key' => 'life_creature',
            'name' => 'Vie',
            'short_name' => 'PV',
            'type' => 'int',
            'sort_order' => 0,
        ]);
        CharacteristicCreature::create([
            'characteristic_id' => $life->id,
            'entity' => '*',
            'db_column' => 'life',
            'min' => 1,
            'max' => 10000,
            'sort_order' => 0,
        ]);

        $level = Characteristic::create([
            'key' => 'level_creature',
            'name' => 'Niveau',
            'short_name' => 'Niv',
            'type' => 'int',
            'sort_order' => 1,
        ]);
        CharacteristicCreature::create([
            'characteristic_id' => $level->id,
            'entity' => '*',
            'db_column' => 'level',
            'min' => 1,
            'max' => 200,
            'sort_order' => 0,
        ]);
    }
}
