<?php

declare(strict_types=1);

namespace App\Support\Entity;

use App\Models\Characteristic;

/**
 * Caractéristiques objet filtrables (hors métadonnées nom / niveau / prix…).
 *
 * @phpstan-type BonusFilterOption array{value: string, label: string, short_name: string, min: int, max: int}
 */
final class ObjectBonusFilterCatalog
{
    /**
     * @return list<string>
     */
    public static function allowedKeys(): array
    {
        return array_values(array_map(
            static fn (array $row): string => $row['value'],
            self::options()
        ));
    }

    public static function isAllowedKey(string $key): bool
    {
        $safe = JsonBonusValue::sanitizeKey($key);

        return $safe !== null && in_array($safe, self::allowedKeys(), true);
    }

    /**
     * @return list<BonusFilterOption>
     */
    public static function options(): array
    {
        $rows = Characteristic::query()
            ->where('group', 'object')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['key', 'name', 'short_name', 'sort_order']);

        $out = [];
        $seen = [];
        foreach ($rows as $row) {
            $short = JsonBonusValue::sanitizeKey((string) $row->key);
            if ($short === null || JsonBonusValue::isMetaKey($short) || isset($seen[$short])) {
                continue;
            }
            $seen[$short] = true;
            $label = trim((string) ($row->name ?: $short));
            $shortName = trim((string) ($row->short_name ?: ''));
            $out[] = [
                'value' => $short,
                'label' => $label !== '' ? $label : $short,
                'short_name' => $shortName,
                'min' => -200,
                'max' => 200,
            ];
        }

        return $out;
    }
}
