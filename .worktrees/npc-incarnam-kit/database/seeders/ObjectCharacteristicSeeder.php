<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CharacteristicObject;

/**
 * Seed characteristic_object (groupe object : item, consumable, resource, panoply).
 */
class ObjectCharacteristicSeeder extends CharacteristicGroupSeeder
{
    /**
     * @return class-string<CharacteristicObject>
     */
    protected function modelClass(): string
    {
        return CharacteristicObject::class;
    }

    protected function jsonGroupSubdirectory(): string
    {
        return 'object';
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function mapRowToAttributes(array $row): array
    {
        return array_merge($this->commonAttributes($row), [
            'forgemagie_max' => (int) ($row['forgemagie_max'] ?? 0),
            'base_price_per_unit' => isset($row['base_price_per_unit']) ? (float) $row['base_price_per_unit'] : null,
            'rune_price_per_unit' => isset($row['rune_price_per_unit']) ? (float) $row['rune_price_per_unit'] : null,
            'value_available' => isset($row['value_available']) ? $row['value_available'] : null,
        ]);
    }
}
