<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CharacteristicSpell;

/**
 * Seed characteristic_spell (groupe spell).
 */
class SpellCharacteristicSeeder extends CharacteristicGroupSeeder
{
    protected function defaultEntity(): string
    {
        return 'spell';
    }

    /**
     * @return class-string<CharacteristicSpell>
     */
    protected function modelClass(): string
    {
        return CharacteristicSpell::class;
    }

    protected function jsonGroupSubdirectory(): string
    {
        return 'spell';
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function mapRowToAttributes(array $row): array
    {
        return array_merge($this->commonAttributes($row), [
            'value_available' => $row['value_available'] ?? null,
        ]);
    }
}
