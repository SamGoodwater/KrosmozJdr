<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\EquipmentGrid;

/**
 * Ligne d’équipement déjà chargée, prête pour la grille (pas d’Eloquent).
 */
final class EquipmentGridItem
{
    /**
     * @param  array<string, float>  $bonuses
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $level,
        public readonly string $state,
        public readonly ?int $itemTypeId,
        public readonly ?int $dofusdbTypeId,
        public readonly ?string $dofusdbId,
        public readonly ?string $officialId,
        public readonly array $bonuses,
    ) {}

    public function isGridGenerated(string $prefix): bool
    {
        if ($this->officialId === null || $this->officialId === '') {
            return false;
        }

        return str_starts_with($this->officialId, $prefix.':');
    }

    public function hasDofusSource(): bool
    {
        return $this->dofusdbId !== null && $this->dofusdbId !== '';
    }
}
