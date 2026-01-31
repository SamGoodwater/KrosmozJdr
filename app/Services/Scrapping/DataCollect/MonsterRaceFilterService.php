<?php

namespace App\Services\Scrapping\DataCollect;

use App\Models\Type\MonsterRace;

/**
 * Dérive des filtres `raceId` / `raceIds` pour les monstres.
 *
 * @description
 * Objectif: reproduire le même pattern que `type_mode` :
 * - all: aucun filtre race (toutes les races)
 * - allowed: uniquement les races validées (state=playable)
 * - selected: uniquement les races fournies par l'UI
 */
class MonsterRaceFilterService
{
    public const RACE_MODE_ALL = 'all';
    public const RACE_MODE_ALLOWED = 'allowed';
    public const RACE_MODE_SELECTED = 'selected';

    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function applyDefaults(array $filters, string $raceMode = self::RACE_MODE_ALLOWED): array
    {
        $raceMode = $this->normalizeRaceMode($raceMode);

        $hasExplicit = array_key_exists('raceId', $filters) || array_key_exists('raceIds', $filters);
        if ($hasExplicit) {
            return $filters;
        }

        if ($raceMode === self::RACE_MODE_SELECTED) {
            return $filters;
        }

        if ($raceMode === self::RACE_MODE_ALL) {
            return $filters;
        }

        // allowed: races validées uniquement (IDs DofusDB)
        try {
            $ids = MonsterRace::query()
                ->where('state', MonsterRace::STATE_PLAYABLE)
                ->whereNotNull('dofusdb_race_id')
                ->pluck('dofusdb_race_id')
                ->map(fn ($v) => (int) $v)
                ->filter(fn ($v) => $v !== 0)
                ->unique()
                ->values()
                ->all();

            if (!empty($ids)) {
                $filters['raceIds'] = $ids;
            }
        } catch (\Throwable) {
            // best effort
        }

        return $filters;
    }

    private function normalizeRaceMode(string $mode): string
    {
        $mode = strtolower(trim((string) $mode));
        return match ($mode) {
            self::RACE_MODE_ALL => self::RACE_MODE_ALL,
            self::RACE_MODE_SELECTED => self::RACE_MODE_SELECTED,
            default => self::RACE_MODE_ALLOWED,
        };
    }
}

