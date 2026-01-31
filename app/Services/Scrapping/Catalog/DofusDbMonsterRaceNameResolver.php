<?php

namespace App\Services\Scrapping\Catalog;

/**
 * Résout un raceId DofusDB vers un nom (via cache catalogue).
 */
class DofusDbMonsterRaceNameResolver
{
    public function __construct(private DofusDbMonsterRacesCatalogService $catalog) {}

    public function fetchName(int $raceId, string $lang = 'fr', bool $skipCache = false): ?string
    {
        $raceId = (int) $raceId;
        if ($raceId === 0) return null;

        $map = $this->catalog->mapNames($lang, $skipCache);
        return $map[$raceId] ?? null;
    }
}

