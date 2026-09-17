<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\EquipmentGrid;

/**
 * Rapport de couverture de la grille.
 */
final class EquipmentGridReport
{
    /**
     * @param  list<EquipmentGridCell>  $cells
     */
    public function __construct(
        public readonly EquipmentGridDefinition $definition,
        public readonly array $cells,
        public readonly int $filled,
        public readonly int $holes,
        public readonly int $duplicateCount,
        public readonly int $outsideGrid,
        public readonly int $unclassified,
        public readonly int $scanned,
    ) {}

    /**
     * @return list<EquipmentGridCell>
     */
    public function holeCells(): array
    {
        return array_values(array_filter(
            $this->cells,
            static fn (EquipmentGridCell $cell): bool => $cell->isHole
        ));
    }
}
