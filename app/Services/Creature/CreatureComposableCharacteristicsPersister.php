<?php

declare(strict_types=1);

namespace App\Services\Creature;

use App\Models\Entity\Creature;
use App\Support\Creature\CreatureComposableColumns;

/**
 * Persiste les totaux explicites et bonus contextuels (`*_context`) d'une créature.
 *
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 * @see App\Support\Creature\CreatureComposableColumns
 */
final class CreatureComposableCharacteristicsPersister
{
    /**
     * @return list<string>
     */
    public static function storableKeys(): array
    {
        return array_values(array_unique([
            ...CreatureComposableColumns::all(),
            ...CreatureComposableColumns::contextColumns(),
        ]));
    }

    /**
     * Filtre un tableau validé pour ne garder que les clés composables présentes.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, string|null>
     */
    public function extractPayload(array $validated): array
    {
        $payload = [];
        foreach (self::storableKeys() as $key) {
            if (! array_key_exists($key, $validated)) {
                continue;
            }
            $payload[$key] = $this->normalizeValue($validated[$key]);
        }

        return $payload;
    }

    /**
     * @param  array<string, string|null>  $payload
     */
    public function apply(Creature $creature, array $payload): void
    {
        if ($payload === []) {
            return;
        }

        $creature->update($payload);
    }

    /**
     * Chaîne vide → null (total/contexte effacé).
     */
    public function normalizeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        if (! is_string($value)) {
            return null;
        }
        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
