<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Reference;

use App\Models\Characteristic;
use App\Models\CharacteristicObject;

/**
 * Construit le tableau des runes de forgemagie depuis le référentiel des caractéristiques.
 *
 * Une rune existe pour toute caractéristique d'objet dont `forgemagie_max` est
 * strictement positif. Le prix affiché est `rune_price_per_unit` (le double du
 * prix de la caractéristique sur équipement de base) et les équipements
 * autorisés viennent du pivot `characteristic_object_item_type` — une liste
 * vide signifiant « tous les équipements ».
 *
 * @example
 * $rows = (new ForgemagieRuneTableService)->build()['rows'];
 * // [['key' => 'action_points_object', 'max_bonus' => 1, 'rune_price' => 2600.0, ...], ...]
 */
final class ForgemagieRuneTableService
{
    /** Critères de tri acceptés par la section CMS. */
    private const SORT_KEYS = ['name', 'rune_price', 'max_bonus'];

    /**
     * @param  array{sort_by?: string, sort_dir?: string}  $filters
     * @return array{rows: list<array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $sortBy = $this->normalizeSortBy($filters['sort_by'] ?? 'name');
        $sortDir = strtolower(trim((string) ($filters['sort_dir'] ?? 'asc'))) === 'desc' ? 'desc' : 'asc';

        $rows = CharacteristicObject::query()
            ->with(['characteristic', 'allowedItemTypes'])
            ->where('forgemagie_max', '>', 0)
            ->whereNotNull('rune_price_per_unit')
            ->get()
            ->filter(fn (CharacteristicObject $row): bool => $row->characteristic instanceof Characteristic)
            ->map(fn (CharacteristicObject $row): array => $this->mapRow($row))
            // Une caractéristique peut être déclinée par entité (`*`, `panoply`…) :
            // la rune, elle, est unique.
            ->unique('key')
            ->values();

        $sorted = $rows->sortBy(fn (array $row) => match ($sortBy) {
            'rune_price' => $row['rune_price'] ?? 0.0,
            'max_bonus' => $row['max_bonus'] ?? 0,
            default => mb_strtolower((string) $row['name']),
        }, SORT_REGULAR, $sortDir === 'desc')->values();

        return [
            'rows' => $sorted->all(),
            'meta' => [
                'total' => $sorted->count(),
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
                'price_notice' => 'Prix indicatifs : le double du prix de la caractéristique sur équipement de base.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapRow(CharacteristicObject $row): array
    {
        $characteristic = $row->characteristic->effectiveCharacteristic();
        $itemTypes = $row->allowedItemTypes->pluck('name')->filter()->sort()->values()->all();

        return [
            'key' => (string) $characteristic->key,
            'name' => (string) ($characteristic->name ?? $characteristic->key),
            'icon' => $characteristic->icon,
            'color' => $characteristic->color,
            'max_bonus' => (int) $row->forgemagie_max,
            'base_price' => $this->toFloatOrNull($row->base_price_per_unit),
            'rune_price' => $this->toFloatOrNull($row->rune_price_per_unit),
            'item_types' => $itemTypes,
            'restricted' => $itemTypes !== [],
        ];
    }

    private function normalizeSortBy(string $sortBy): string
    {
        $normalized = strtolower(trim($sortBy));

        return in_array($normalized, self::SORT_KEYS, true) ? $normalized : 'name';
    }

    private function toFloatOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '' || ! is_numeric((string) $value)) {
            return null;
        }

        return round((float) $value, 2);
    }
}
