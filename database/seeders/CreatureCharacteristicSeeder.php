<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CharacteristicCreature;

/**
 * Seed characteristic_creature (groupe creature : monster, class, npc).
 */
class CreatureCharacteristicSeeder extends CharacteristicGroupSeeder
{
    protected function dataPath(): string
    {
        return 'database/seeders/data/characteristic_creature.php';
    }

    /**
     * @return class-string<\App\Models\CharacteristicCreature>
     */
    protected function modelClass(): string
    {
        return CharacteristicCreature::class;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    protected function mapRowToAttributes(array $row): array
    {
        return $this->commonAttributes($row);
    }
}
