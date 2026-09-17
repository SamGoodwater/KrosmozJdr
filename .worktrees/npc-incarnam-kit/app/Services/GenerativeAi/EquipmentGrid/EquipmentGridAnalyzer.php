<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\EquipmentGrid;

use App\Enums\EntityState;

/**
 * Classe les équipements dans les cases niveau × slot × voie et choisit un représentant.
 *
 * Ne réécrit pas les fiches Dofus. Un item sans voie (pas de Force / Int / Chance / Agi ni dégâts élémentaires)
 * n’occupe pas de case.
 *
 * @example
 * $report = $analyzer->analyze($definition, $items);
 */
final class EquipmentGridAnalyzer
{
    /** @var array<string, int> */
    private const STATE_RANK = [
        EntityState::Playable->value => 0,
        EntityState::Draft->value => 1,
        EntityState::Auto->value => 2,
        EntityState::Raw->value => 3,
    ];

    /**
     * @param  list<EquipmentGridItem>  $items
     */
    public function analyze(EquipmentGridDefinition $definition, array $items): EquipmentGridReport
    {
        $classifier = new EquipmentVoieClassifier($definition);
        /** @var array<string, list<EquipmentGridItem>> $buckets */
        $buckets = [];
        $outsideGrid = 0;
        $unclassified = 0;

        foreach ($items as $item) {
            if ($item->state === EntityState::Archived->value) {
                continue;
            }
            $slotKey = $definition->slotKeyForDofusTypeId($item->dofusdbTypeId);
            if ($slotKey === null) {
                $outsideGrid++;

                continue;
            }
            $voieKey = $classifier->classify($item->bonuses);
            if ($voieKey === null) {
                $unclassified++;

                continue;
            }
            $level = $definition->clampLevel($item->level);
            $buckets[$this->bucketKey($slotKey, $voieKey, $level)][] = $item;
        }

        $cells = [];
        $filled = 0;
        $holes = 0;
        $duplicateCount = 0;
        foreach ($definition->slots() as $slot) {
            foreach ($definition->activeVoieKeys() as $voieKey) {
                $voie = $definition->voie($voieKey);
                if ($voie === null) {
                    continue;
                }
                for ($level = $definition->minLevel; $level <= $definition->maxLevel; $level++) {
                    $candidates = $buckets[$this->bucketKey($slot['key'], $voieKey, $level)] ?? [];
                    if ($candidates === []) {
                        $holes++;
                        $cells[] = new EquipmentGridCell(
                            slotKey: $slot['key'],
                            slotLabel: $slot['label'],
                            voieKey: $voieKey,
                            voieLabel: $voie['label'],
                            level: $level,
                            isHole: true,
                            representative: null,
                            duplicates: [],
                        );

                        continue;
                    }
                    $ordered = $this->rankCandidates($candidates, $classifier, $voieKey, $definition->officialIdPrefix);
                    $winner = $ordered[0];
                    $dupes = array_slice($ordered, 1);
                    $filled++;
                    $duplicateCount += count($dupes);
                    $cells[] = new EquipmentGridCell(
                        slotKey: $slot['key'],
                        slotLabel: $slot['label'],
                        voieKey: $voieKey,
                        voieLabel: $voie['label'],
                        level: $level,
                        isHole: false,
                        representative: $this->summarize($winner),
                        duplicates: array_map($this->summarize(...), $dupes),
                    );
                }
            }
        }

        return new EquipmentGridReport(
            definition: $definition,
            cells: $cells,
            filled: $filled,
            holes: $holes,
            duplicateCount: $duplicateCount,
            outsideGrid: $outsideGrid,
            unclassified: $unclassified,
            scanned: count($items),
        );
    }

    /**
     * @param  list<EquipmentGridItem>  $candidates
     * @return list<EquipmentGridItem>
     */
    private function rankCandidates(
        array $candidates,
        EquipmentVoieClassifier $classifier,
        string $voieKey,
        string $officialPrefix,
    ): array {
        usort($candidates, function (EquipmentGridItem $a, EquipmentGridItem $b) use ($classifier, $voieKey, $officialPrefix): int {
            $dofus = ((int) $b->hasDofusSource()) <=> ((int) $a->hasDofusSource());
            if ($dofus !== 0) {
                return $dofus;
            }
            $state = ($this->stateRank($a->state) <=> $this->stateRank($b->state));
            if ($state !== 0) {
                return $state;
            }
            $generated = ((int) $a->isGridGenerated($officialPrefix)) <=> ((int) $b->isGridGenerated($officialPrefix));
            if ($generated !== 0) {
                return $generated;
            }
            $score = $classifier->score($b->bonuses, $voieKey) <=> $classifier->score($a->bonuses, $voieKey);
            if ($score !== 0) {
                return $score;
            }

            return $a->id <=> $b->id;
        });

        return $candidates;
    }

    private function stateRank(string $state): int
    {
        return self::STATE_RANK[$state] ?? 9;
    }

    /**
     * @return array{id: int, name: string, state: string, dofusdb_id: string|null, official_id: string|null}
     */
    private function summarize(EquipmentGridItem $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'state' => $item->state,
            'dofusdb_id' => $item->dofusdbId,
            'official_id' => $item->officialId,
        ];
    }

    private function bucketKey(string $slotKey, string $voieKey, int $level): string
    {
        return $slotKey.'|'.$voieKey.'|'.$level;
    }
}
