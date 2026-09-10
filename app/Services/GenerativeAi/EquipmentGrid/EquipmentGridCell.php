<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\EquipmentGrid;

/**
 * Une case niveau × slot × voie.
 *
 * @phpstan-type Representative array{id: int, name: string, state: string, dofusdb_id: string|null, official_id: string|null}
 */
final class EquipmentGridCell
{
    /**
     * @param  Representative|null  $representative
     * @param  list<Representative>  $duplicates
     */
    public function __construct(
        public readonly string $slotKey,
        public readonly string $slotLabel,
        public readonly string $voieKey,
        public readonly string $voieLabel,
        public readonly int $level,
        public readonly bool $isHole,
        public readonly ?array $representative,
        public readonly array $duplicates,
    ) {}
}
