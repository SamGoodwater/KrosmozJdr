<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Reference;

use App\Models\Characteristic;
use App\Models\CharacteristicObject;
use App\Models\Type\ItemType;
use App\Services\Characteristic\Formula\FormulaConfigDecoder;
use Illuminate\Support\Collection;

/**
 * Projection MJ des plafonds de bonus d’équipement (slot × carac × bandes de 2 niveaux).
 *
 * Les valeurs de bandes viennent de la table JSON `formula`, pas de `norms_grid`.
 */
final class EquipmentBonusTableService
{
    /** Clés métier (suffixe `_object` ignoré) qui ne sont pas des bonus. */
    private const META_KEYS = ['name', 'description', 'level', 'rarity', 'price', 'weight'];

    /**
     * @return list<array{from: int, to: int, label: string}>
     */
    public static function bands(): array
    {
        $bands = [];
        for ($from = 1; $from <= 19; $from += 2) {
            $to = $from + 1;
            $bands[] = [
                'from' => $from,
                'to' => $to,
                'label' => $from.'–'.$to,
            ];
        }

        return $bands;
    }

    /**
     * Plafond affiché pour une bande : plus grand seuil de `formula` ≤ niveau de début.
     * `0`, « – » ou formule `"0"` = non débloqué (`null`).
     */
    public function bandValueForFormula(?string $formula, int $bandStart): int|float|null
    {
        if ($formula === null) {
            return null;
        }

        $trimmed = trim($formula);
        if ($this->isLockedToken($trimmed)) {
            return null;
        }

        $decoded = FormulaConfigDecoder::decode($formula);
        if ($decoded['type'] === 'formula') {
            $expression = trim((string) ($decoded['expression'] ?? ''));
            if ($this->isLockedToken($expression)) {
                return null;
            }
            if (is_numeric($expression)) {
                return $this->normalizeNumericValue((float) $expression);
            }

            return null;
        }

        $bestFrom = null;
        $bestValue = null;
        foreach ($decoded['entries'] as $entry) {
            $from = (int) ($entry['from'] ?? 0);
            if ($from > $bandStart) {
                continue;
            }
            if ($bestFrom === null || $from > $bestFrom) {
                $bestFrom = $from;
                $bestValue = $entry['value'] ?? null;
            }
        }

        return $this->normalizeBandValue($bestValue);
    }

    /**
     * @return array{
     *     bands: list<array{from: int, to: int, label: string}>,
     *     groups: list<array{item_type_id: int|null, item_type_name: string, rows: list<array<string, mixed>>}>
     * }
     */
    public function build(): array
    {
        $bands = self::bands();
        $rows = CharacteristicObject::query()
            ->whereIn('entity', [CharacteristicObject::ENTITY_ITEM, CharacteristicObject::ENTITY_ALL])
            ->with(['characteristic.masterCharacteristic', 'allowedItemTypes'])
            ->get()
            ->groupBy('characteristic_id')
            ->map(fn (Collection $charRows) => $this->preferItemEntity($charRows))
            ->filter()
            ->values();

        /** @var array<string, array{item_type_id: int|null, item_type_name: string, rows: list<array<string, mixed>>}> $groups */
        $groups = [];

        foreach ($rows as $pivot) {
            if (! $pivot instanceof CharacteristicObject || ! $pivot->characteristic instanceof Characteristic) {
                continue;
            }

            $char = $pivot->characteristic->effectiveCharacteristic();
            if ($this->isMetaKey((string) $char->key)) {
                continue;
            }

            $bandValues = [];
            $hasUnlocked = false;
            foreach ($bands as $band) {
                $value = $this->bandValueForFormula($pivot->formula, $band['from']);
                $bandValues[] = $value;
                if ($value !== null) {
                    $hasUnlocked = true;
                }
            }
            if (! $hasUnlocked) {
                continue;
            }

            $rowPayload = [
                'key' => (string) $char->key,
                'name' => (string) ($char->name ?? $char->key),
                'icon' => $char->icon,
                'color' => $char->color,
                'bands' => $bandValues,
                'price_per_unit' => $this->toFloatOrNull($pivot->base_price_per_unit),
                'forgemagie_max' => (int) ($pivot->forgemagie_max ?? 0),
                'rune_price' => $this->toFloatOrNull($pivot->rune_price_per_unit),
            ];

            $types = $pivot->allowedItemTypes
                ->filter(fn ($type) => $type instanceof ItemType)
                ->unique('id')
                ->values();

            if ($types->isEmpty()) {
                $this->pushRow($groups, 'all', null, 'Tous types', $rowPayload);

                continue;
            }

            foreach ($types as $type) {
                $this->pushRow(
                    $groups,
                    'type-'.$type->id,
                    (int) $type->id,
                    (string) $type->name,
                    $rowPayload
                );
            }
        }

        $named = collect($groups)
            ->filter(fn (array $group): bool => $group['item_type_id'] !== null)
            ->sortBy(fn (array $group): string => mb_strtolower($group['item_type_name']), SORT_NATURAL)
            ->values();
        $fallback = collect($groups)
            ->filter(fn (array $group): bool => $group['item_type_id'] === null)
            ->values();

        $ordered = $named->concat($fallback)->map(function (array $group): array {
            usort($group['rows'], static fn (array $a, array $b): int => strcasecmp((string) $a['name'], (string) $b['name']));

            return $group;
        })->values()->all();

        return [
            'bands' => $bands,
            'groups' => $ordered,
        ];
    }

    /**
     * @param  Collection<int, CharacteristicObject>  $charRows
     */
    private function preferItemEntity(Collection $charRows): ?CharacteristicObject
    {
        return $charRows->firstWhere('entity', CharacteristicObject::ENTITY_ITEM)
            ?? $charRows->firstWhere('entity', CharacteristicObject::ENTITY_ALL)
            ?? $charRows->first();
    }

    /**
     * @param  array<string, array{item_type_id: int|null, item_type_name: string, rows: list<array<string, mixed>>}>  $groups
     * @param  array<string, mixed>  $row
     */
    private function pushRow(array &$groups, string $groupKey, ?int $typeId, string $typeName, array $row): void
    {
        if (! isset($groups[$groupKey])) {
            $groups[$groupKey] = [
                'item_type_id' => $typeId,
                'item_type_name' => $typeName,
                'rows' => [],
            ];
        }
        $groups[$groupKey]['rows'][] = $row;
    }

    private function isMetaKey(string $key): bool
    {
        $canonical = (string) preg_replace('/_(creature|object|spell)$/i', '', $key);

        return in_array($canonical, self::META_KEYS, true);
    }

    private function isLockedToken(string $value): bool
    {
        return $value === '' || $value === '0' || $value === '-' || $value === '–';
    }

    private function normalizeBandValue(mixed $value): int|float|null
    {
        if ($value === null) {
            return null;
        }
        if (is_string($value) && $this->isLockedToken(trim($value))) {
            return null;
        }
        if (is_numeric($value)) {
            return $this->normalizeNumericValue((float) $value);
        }

        return null;
    }

    private function normalizeNumericValue(float $value): int|float|null
    {
        if ($value == 0.0) {
            return null;
        }

        $asInt = (int) $value;

        return $value == $asInt ? $asInt : $value;
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
