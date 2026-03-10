<?php

namespace App\Services\Scrapping\Core\Search;

use App\Models\Entity\Breed;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Services\Scrapping\Catalog\DofusDbMonsterRacesCatalogService;
use App\Services\Scrapping\Registry\TypeRegistryBatchTouchService;
use App\Models\Type\ResourceType;

/**
 * Enrichit les résultats de recherche scrapping (exists, typeName, raceName, etc.).
 *
 * Utilisé par ScrappingSearchController après CollectService::fetchManyResult.
 */
final class SearchResultEnricher
{
    public function __construct(
        private TypeRegistryBatchTouchService $typeRegistryBatchTouch,
        private DofusDbMonsterRacesCatalogService $monsterRacesCatalog,
    ) {}

    /**
     * Enrichit les items : exists/existing, libellés de type, libellé de race (monstres).
     *
     * @param string $entity Type d'entité (class, monster, item, spell, panoply, resource, consumable, equipment)
     * @param array<int, array<string, mixed>> $items Items bruts (liste indexée)
     * @return array<int, array<string, mixed>>
     */
    public function enrich(string $entity, array $items): array
    {
        $items = $this->stripUnwantedFields($entity, $items);
        $items = $this->withExistsFlag($entity, $items);
        $items = $this->withTypeLabelsAndRegistry($entity, $items);
        $items = $this->withMonsterRaceLabel($entity, $items);

        return $items;
    }

    /**
     * Supprime les champs inutiles en recherche (allège la réponse API).
     * Pour les ressources : recipesThatUse (recettes qui utilisent la ressource) — on obtient
     * les recettes à l'inverse via équipements/consommables → leurs ingrédients.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    private function stripUnwantedFields(string $entity, array $items): array
    {
        if (strtolower($entity) !== 'resource') {
            return $items;
        }

        foreach ($items as $i => $it) {
            if (!is_array($it)) {
                continue;
            }
            unset($items[$i]['recipesThatUse']);
        }

        return $items;
    }

    /**
     * Ajoute `exists` + `existing` (id interne) aux items.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    public function withExistsFlag(string $entity, array $items): array
    {
        $modelClass = match ($entity) {
            'class' => Breed::class,
            'monster' => Monster::class,
            'item' => Item::class,
            'spell' => Spell::class,
            'panoply' => Panoply::class,
            'resource' => Resource::class,
            'consumable' => Consumable::class,
            'equipment' => Item::class,
            default => null,
        };

        if ($modelClass === null) {
            return $items;
        }

        $dofusIds = [];
        foreach ($items as $it) {
            if (! is_array($it)) {
                continue;
            }
            if (isset($it['id']) && (is_int($it['id']) || (is_string($it['id']) && ctype_digit($it['id'])))) {
                $dofusIds[] = (string) (int) $it['id'];
            }
        }
        $dofusIds = array_values(array_unique($dofusIds));
        if ($dofusIds === []) {
            return $items;
        }

        try {
            /** @var array<string, int> $existingMap */
            $existingMap = $modelClass::query()
                ->whereIn('dofusdb_id', $dofusIds)
                ->pluck('id', 'dofusdb_id')
                ->all();
        } catch (\Throwable) {
            $existingMap = [];
        }

        foreach ($items as $i => $it) {
            if (! is_array($it)) {
                continue;
            }
            $idStr = isset($it['id']) ? (string) (int) $it['id'] : null;
            $existingId = ($idStr !== null && isset($existingMap[$idStr])) ? (int) $existingMap[$idStr] : null;
            $items[$i]['exists'] = $existingId !== null;
            $items[$i]['existing'] = $existingId !== null ? ['id' => $existingId] : null;
        }

        return $items;
    }

    /**
     * Ajoute typeName, typeDecision, typeKnown (et enregistre les typeId en base si absent).
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    public function withTypeLabelsAndRegistry(string $entity, array $items): array
    {
        $entity = strtolower($entity);
        $registry = match ($entity) {
            'resource' => ResourceType::class,
            'consumable' => ConsumableType::class,
            'item', 'equipment' => ItemType::class,
            default => null,
        };

        if ($registry === null) {
            return $items;
        }

        $typeIds = [];
        foreach ($items as $it) {
            if (! is_array($it)) {
                continue;
            }
            $typeId = $it['typeId'] ?? null;
            if (is_int($typeId) || (is_string($typeId) && ctype_digit($typeId))) {
                $n = (int) $typeId;
                if ($n > 0) {
                    $typeIds[] = $n;
                }
            }
        }
        $typeIds = array_values(array_unique($typeIds));
        if ($typeIds === []) {
            return $items;
        }

        $byTypeId = [];
        try {
            /** @var class-string $registry */
            $rows = $registry::query()->whereIn('dofusdb_type_id', $typeIds)->get();
            foreach ($rows as $row) {
                $id = is_numeric($row->dofusdb_type_id ?? null) ? (int) $row->dofusdb_type_id : 0;
                if ($id > 0) {
                    $byTypeId[$id] = $row;
                }
            }
        } catch (\Throwable) {
            $byTypeId = [];
        }

        $knownIds = array_map('intval', array_keys($byTypeId));
        $missing = array_values(array_diff($typeIds, $knownIds));
        if ($missing !== []) {
            try {
                $this->typeRegistryBatchTouch->touchMany($registry, $missing);
                /** @var class-string $registry */
                $touched = $registry::query()->whereIn('dofusdb_type_id', $missing)->get();
                foreach ($touched as $row) {
                    $id = is_numeric($row->dofusdb_type_id ?? null) ? (int) $row->dofusdb_type_id : 0;
                    if ($id > 0) {
                        $byTypeId[$id] = $row;
                    }
                }
            } catch (\Throwable) {
                // best-effort
            }
        }

        foreach ($items as $i => $it) {
            if (! is_array($it)) {
                continue;
            }
            $typeId = $it['typeId'] ?? null;
            if (! (is_int($typeId) || (is_string($typeId) && ctype_digit($typeId)))) {
                continue;
            }
            $typeId = (int) $typeId;
            if ($typeId <= 0) {
                continue;
            }
            try {
                $typeModel = $byTypeId[$typeId] ?? null;
                if ($typeModel === null) {
                    continue;
                }
                $items[$i]['typeName'] = is_string($typeModel->name ?? null) ? (string) $typeModel->name : null;
                $items[$i]['typeDecision'] = is_string($typeModel->decision ?? null) ? (string) $typeModel->decision : null;
                $items[$i]['typeKnown'] = ($items[$i]['typeDecision'] ?? null) === 'allowed';
            } catch (\Throwable) {
                $items[$i]['typeName'] = null;
                $items[$i]['typeDecision'] = null;
                $items[$i]['typeKnown'] = null;
            }
        }

        return $items;
    }

    /**
     * Enrichit les monstres avec raceId + raceName.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    public function withMonsterRaceLabel(string $entity, array $items): array
    {
        if (strtolower($entity) !== 'monster') {
            return $items;
        }

        $lang = (string) config('scrapping.data_collect.default_language', 'fr');

        foreach ($items as $i => $it) {
            if (! is_array($it)) {
                continue;
            }
            $race = $it['race'] ?? ($it['raceId'] ?? null);
            if (! (is_int($race) || (is_string($race) && preg_match('/^-?\d+$/', $race)))) {
                continue;
            }
            $raceId = (int) $race;
            $items[$i]['raceId'] = $raceId;
            try {
                $name = $this->monsterRacesCatalog->fetchName($raceId, $lang, false);
                $items[$i]['raceName'] = $name ?: null;
                \App\Models\Type\MonsterRace::touchDofusdbRace($raceId, $name ?: null);
            } catch (\Throwable) {
                $items[$i]['raceName'] = null;
            }
        }

        return $items;
    }
}
