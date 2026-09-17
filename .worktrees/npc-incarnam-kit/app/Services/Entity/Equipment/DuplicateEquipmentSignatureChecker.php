<?php

namespace App\Services\Entity\Equipment;

/**
 * Produit une signature stable pour repérer des équipements aux mêmes bonus.
 *
 * @example
 * $signature = $checker->signature(['strength' => 2], 8, 10);
 */
final class DuplicateEquipmentSignatureChecker
{
    /**
     * @param  array<string, int|float|null>  $bonus
     */
    public function signature(array $bonus, ?int $level = null, ?int $itemTypeId = null): string
    {
        $normalized = [];
        foreach ($bonus as $key => $value) {
            if ($value === null || ! is_numeric($value) || (float) $value === 0.0) {
                continue;
            }
            $normalized[(string) $key] = (float) $value;
        }
        ksort($normalized);

        return hash('sha256', json_encode([
            'level' => $level,
            'item_type_id' => $itemTypeId,
            'bonus' => $normalized,
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @param  list<array{signature: string, id?: int|string, name?: string}>  $existing
     * @return list<array{signature: string, id?: int|string, name?: string}>
     */
    public function duplicates(string $signature, array $existing): array
    {
        return array_values(array_filter(
            $existing,
            static fn (array $row): bool => ($row['signature'] ?? null) === $signature
        ));
    }
}
