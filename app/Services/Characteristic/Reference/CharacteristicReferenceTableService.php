<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Reference;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use Illuminate\Support\Collection;

/**
 * Construit le dataset tabulaire de référence des caractéristiques à partir de la DB courante.
 */
final class CharacteristicReferenceTableService
{
    /**
     * @param  array{
     *     group?: string,
     *     entity?: string,
     *     search?: string,
     *     sort_by?: string,
     *     sort_dir?: string,
     *     status_filter?: string,
     *     include_status?: bool,
     *     show_only_with_equipment?: bool
     * }  $filters
     * @return array{rows: list<array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $group = $this->normalizeGroup($filters['group'] ?? 'all');
        $entityFilter = trim((string) ($filters['entity'] ?? '*'));
        $search = mb_strtolower(trim((string) ($filters['search'] ?? '')));
        $sortBy = $this->normalizeSortBy($filters['sort_by'] ?? 'group');
        $sortDir = $this->normalizeSortDir($filters['sort_dir'] ?? 'asc');
        $statusFilter = $this->normalizeStatusFilter($filters['status_filter'] ?? 'all');
        $includeStatus = (bool) ($filters['include_status'] ?? true);
        $onlyWithEquipment = (bool) ($filters['show_only_with_equipment'] ?? false);

        $rows = collect();
        if ($group === 'all' || $group === 'creature') {
            $rows = $rows->merge($this->buildGroupRows('creature', CharacteristicCreature::query()->with('characteristic')->get()));
        }
        if ($group === 'all' || $group === 'object') {
            $rows = $rows->merge($this->buildGroupRows('object', CharacteristicObject::query()->with('characteristic')->get()));
        }
        if ($group === 'all' || $group === 'spell') {
            $rows = $rows->merge($this->buildGroupRows('spell', CharacteristicSpell::query()->with('characteristic')->get()));
        }

        if ($entityFilter !== '' && $entityFilter !== '*') {
            $rows = $rows->filter(fn (array $row): bool => $row['entity'] === $entityFilter);
        }
        if ($search !== '') {
            $rows = $rows->filter(function (array $row) use ($search): bool {
                $haystack = mb_strtolower(implode(' ', [
                    (string) ($row['key'] ?? ''),
                    (string) ($row['name'] ?? ''),
                    (string) ($row['group'] ?? ''),
                    (string) ($row['entity'] ?? ''),
                    (string) ($row['db_column'] ?? ''),
                ]));

                return str_contains($haystack, $search);
            });
        }
        if ($includeStatus && $statusFilter !== 'all') {
            $rows = $rows->filter(fn (array $row): bool => (string) ($row['status'] ?? '') === $statusFilter);
        }
        if ($onlyWithEquipment) {
            $rows = $rows->filter(fn (array $row): bool => ($row['equipment_max_bonus'] !== null) || ($row['forgemagie_max_bonus'] !== null));
        }

        $rows = $this->sortRows($rows, $sortBy, $sortDir)->values();
        if (! $includeStatus) {
            $rows = $rows->map(function (array $row): array {
                $row['status'] = null;

                return $row;
            })->values();
        }

        return [
            'rows' => $rows->all(),
            'meta' => [
                'total' => $rows->count(),
                'group' => $group,
                'entity' => $entityFilter === '' ? '*' : $entityFilter,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
                'status_filter' => $includeStatus ? $statusFilter : 'all',
                'status_visible' => $includeStatus,
                'price_notice' => 'Prix indicatifs: valeurs de référence, non contractuelles.',
            ],
        ];
    }

    /**
     * @param  Collection<int, CharacteristicCreature|CharacteristicObject|CharacteristicSpell>  $pivotRows
     * @return Collection<int, array<string, mixed>>
     */
    private function buildGroupRows(string $group, Collection $pivotRows): Collection
    {
        return $pivotRows
            ->filter(fn ($row) => $row->characteristic instanceof Characteristic)
            ->map(function ($row) use ($group): array {
                $char = $row->characteristic->effectiveCharacteristic();

                $equipmentMax = $group === 'object' ? $this->toIntOrNull($row->max) : null;
                $forgemagieMax = $group === 'object' ? $this->toIntOrNull($row->forgemagie_max ?? null) : null;
                $basePrice = $group === 'object' ? $this->toFloatOrNull($row->base_price_per_unit ?? null) : null;
                $runePrice = $group === 'object' ? $this->toFloatOrNull($row->rune_price_per_unit ?? null) : null;

                return [
                    'group' => $group,
                    'entity' => (string) $row->entity,
                    'key' => (string) $char->key,
                    'name' => (string) ($char->name ?? $char->key),
                    'icon' => $char->icon,
                    'color' => $char->color,
                    'status' => $char->status,
                    'db_column' => $row->db_column,
                    'formula' => $row->formula,
                    'formula_display' => $row->formula_display,
                    'min' => $row->min,
                    'max' => $row->max,
                    'default_value' => $row->default_value,
                    'equipment_max_bonus' => $equipmentMax,
                    'equipment_price_per_unit' => $basePrice,
                    'forgemagie_max_bonus' => $forgemagieMax,
                    'forgemagie_price_per_unit' => $runePrice,
                ];
            });
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return Collection<int, array<string, mixed>>
     */
    private function sortRows(Collection $rows, string $sortBy, string $sortDir): Collection
    {
        $sorted = $rows->sortBy(function (array $row) use ($sortBy) {
            return match ($sortBy) {
                'entity' => strtolower((string) $row['entity']),
                'name' => strtolower((string) $row['name']),
                'key' => strtolower((string) $row['key']),
                'equipment_max_bonus' => $row['equipment_max_bonus'] ?? PHP_INT_MIN,
                'forgemagie_max' => $row['forgemagie_max_bonus'] ?? PHP_INT_MIN,
                default => strtolower((string) $row['group']),
            };
        });

        return $sortDir === 'desc' ? $sorted->reverse()->values() : $sorted->values();
    }

    private function normalizeGroup(string $group): string
    {
        $g = strtolower(trim($group));

        return in_array($g, ['all', 'creature', 'object', 'spell'], true) ? $g : 'all';
    }

    private function normalizeSortBy(string $sortBy): string
    {
        $s = strtolower(trim($sortBy));

        return in_array($s, ['group', 'entity', 'name', 'key', 'equipment_max_bonus', 'forgemagie_max'], true)
            ? $s
            : 'group';
    }

    private function normalizeSortDir(string $sortDir): string
    {
        return strtolower(trim($sortDir)) === 'desc' ? 'desc' : 'asc';
    }

    private function normalizeStatusFilter(string $statusFilter): string
    {
        $s = strtolower(trim($statusFilter));

        return in_array($s, ['all', 'a_valider', 'en_cours_de_validation', 'validee'], true) ? $s : 'all';
    }

    private function toIntOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (! is_numeric((string) $value)) {
            return null;
        }

        return (int) round((float) $value);
    }

    private function toFloatOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (! is_numeric((string) $value)) {
            return null;
        }

        return round((float) $value, 2);
    }
}

