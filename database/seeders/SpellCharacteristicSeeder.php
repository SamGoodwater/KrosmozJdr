<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CharacteristicSpell;

/**
 * Seed characteristic_spell (groupe spell).
 */
class SpellCharacteristicSeeder extends CharacteristicGroupSeeder
{
    protected function dataPath(): string
    {
        return 'database/seeders/data/characteristic_spell.php';
    }

    protected function defaultEntity(): string
    {
        return 'spell';
    }

    /**
     * @return class-string<\App\Models\CharacteristicSpell>
     */
    protected function modelClass(): string
    {
        return CharacteristicSpell::class;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    protected function mapRowToAttributes(array $row): array
    {
        return array_merge($this->commonAttributes($row), [
            'value_available' => $row['value_available'] ?? null,
        ]);
    }
}
