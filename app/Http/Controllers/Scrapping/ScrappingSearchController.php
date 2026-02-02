<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Models\Entity\Classe;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Services\Scrapping\Catalog\DofusDbMonsterRacesCatalogService;
use App\Models\Type\ResourceType;
use App\Services\Scrapping\Constants\EntityLimits;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\DataCollect\ItemEntityTypeFilterService;
use App\Services\Scrapping\DataCollect\MonsterRaceFilterService;
use App\Services\Scrapping\Registry\TypeRegistryBatchTouchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint de recherche (collect-only) basé sur config JSON.
 *
 * @description
 * Permet de récupérer des listes d'entités depuis DofusDB via pagination/filters,
 * sans lancer l'import (collecte uniquement). Utile pour alimenter le tableau UI
 * et démarrer les tests.
 */
class ScrappingSearchController extends Controller
{
    public function __construct(
        private ConfigLoader $configLoader,
        private CollectService $collectService,
        private ItemEntityTypeFilterService $itemEntityTypeFilters,
        private MonsterRaceFilterService $monsterRaceFilters,
        private TypeRegistryBatchTouchService $typeRegistryBatchTouch,
        private DofusDbMonsterRacesCatalogService $monsterRacesCatalog,
    ) {}

    public function search(Request $request, string $entity): JsonResponse
    {
        $source = 'dofusdb';

        // Liste des entités depuis Core (config/) + alias « class » pour breed
        $available = $this->configLoader->listEntities($source);
        if (in_array('breed', $available, true)) {
            $available[] = 'class';
            sort($available);
        }
        if (!in_array($entity, $available, true)) {
            return response()->json([
                'success' => false,
                'message' => "Entité '{$entity}' non supportée.",
                'timestamp' => now()->toISOString(),
            ], 404);
        }

        $filters = $this->extractFilters($request);
        $typeMode = $this->extractTypeMode($request);
        $filters = $this->applyEntityDefaultsWithMode($entity, $filters, $typeMode);

        // Monstres: race_mode (all/allowed/selected) -> injecter raceIds par défaut si besoin.
        if (strtolower($entity) === 'monster') {
            $raceMode = $this->extractRaceMode($request);
            $filters = $this->monsterRaceFilters->applyDefaults($filters, $raceMode);
        }

        $options = $this->extractOptions($request, $entity);

        $result = $this->collectService->fetchManyResult('dofusdb', $entity, $filters, $options);
        $items = $this->withExistsFlag($entity, $result['items']);
        $items = $this->withTypeLabelsAndRegistry($entity, $items);
        $items = $this->withMonsterRaceLabel($entity, $items);

        // Enrichir meta avec pagination "page/per_page" si utilisée
        $meta = $result['meta'];
        $pagination = $this->extractPagePagination($request);
        if ($pagination !== null) {
            $meta['page'] = $pagination['page'];
            $meta['per_page'] = $pagination['per_page'];
            if (isset($meta['total']) && is_int($meta['total']) && $meta['total'] > 0) {
                $meta['total_pages'] = (int) ceil($meta['total'] / max(1, $pagination['per_page']));
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'meta' => $meta,
                'query' => [
                    'filters' => $filters,
                    'options' => $options,
                ],
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * @return array<string,mixed>
     */
    private function extractFilters(Request $request): array
    {
        $filters = [];

        foreach (['id', 'idMin', 'idMax', 'name', 'raceId', 'levelMin', 'levelMax', 'breedId', 'typeId'] as $key) {
            if ($request->has($key)) {
                $filters[$key] = $request->query($key);
            }
        }

        if ($request->has('typeIds')) {
            $typeIds = $request->query('typeIds');
            if (is_string($typeIds)) {
                $parts = array_filter(array_map('trim', explode(',', $typeIds)));
                $filters['typeIds'] = $parts;
            } elseif (is_array($typeIds)) {
                $filters['typeIds'] = $typeIds;
            }
        }

        if ($request->has('typeIdsNot')) {
            $typeIdsNot = $request->query('typeIdsNot');
            if (is_string($typeIdsNot)) {
                $parts = array_filter(array_map('trim', explode(',', $typeIdsNot)));
                $filters['typeIdsNot'] = $parts;
            } elseif (is_array($typeIdsNot)) {
                $filters['typeIdsNot'] = $typeIdsNot;
            }
        }

        // raceIds peut être "1,2,3" ou ["1","2"]
        if ($request->has('raceIds')) {
            $raceIds = $request->query('raceIds');
            if (is_string($raceIds)) {
                $parts = array_filter(array_map('trim', explode(',', $raceIds)));
                $filters['raceIds'] = $parts;
            } elseif (is_array($raceIds)) {
                $filters['raceIds'] = $raceIds;
            }
        }

        // ids peut être "1,2,3" ou ["1","2"]
        if ($request->has('ids')) {
            $ids = $request->query('ids');
            if (is_string($ids)) {
                $parts = array_filter(array_map('trim', explode(',', $ids)));
                $filters['ids'] = $parts;
            } elseif (is_array($ids)) {
                $filters['ids'] = $ids;
            }
        }

        return $filters;
    }

    private function extractRaceMode(Request $request): string
    {
        $m = $request->query('race_mode');
        if (!is_string($m) || $m === '') {
            return MonsterRaceFilterService::RACE_MODE_ALLOWED;
        }
        $m = strtolower(trim($m));
        return in_array($m, [
            MonsterRaceFilterService::RACE_MODE_ALL,
            MonsterRaceFilterService::RACE_MODE_ALLOWED,
            MonsterRaceFilterService::RACE_MODE_SELECTED,
        ], true)
            ? $m
            : MonsterRaceFilterService::RACE_MODE_ALLOWED;
    }

    /**
     * Ajoute `exists` + `existing` (id interne) aux items de DofusDB.
     *
     * @param string $entity
     * @param array<int, array<string,mixed>> $items
     * @return array<int, array<string,mixed>>
     */
    private function withExistsFlag(string $entity, array $items): array
    {
        $modelClass = match ($entity) {
            'class' => Classe::class,
            'monster' => Monster::class,
            'item' => Item::class,
            'spell' => Spell::class,
            'panoply' => Panoply::class,
            'resource' => Resource::class,
            'consumable' => Consumable::class,
            'equipment' => Item::class,
            default => null,
        };

        if (!$modelClass) {
            return $items;
        }

        $dofusIds = [];
        foreach ($items as $it) {
            if (!is_array($it)) continue;
            if (isset($it['id']) && (is_int($it['id']) || (is_string($it['id']) && ctype_digit($it['id'])))) {
                $dofusIds[] = (string) (int) $it['id'];
            }
        }
        $dofusIds = array_values(array_unique($dofusIds));
        if (empty($dofusIds)) {
            return $items;
        }

        /** @var array<string,int> $existingMap */
        try {
            $existingMap = $modelClass::query()
                ->whereIn('dofusdb_id', $dofusIds)
                ->pluck('id', 'dofusdb_id')
                ->all();
        } catch (\Throwable) {
            // Best-effort: en environnement sans DB/migrations (tests), on n'échoue pas la recherche.
            $existingMap = [];
        }

        foreach ($items as $i => $it) {
            if (!is_array($it)) continue;
            $idStr = isset($it['id']) ? (string) (int) $it['id'] : null;
            $existingId = ($idStr !== null && isset($existingMap[$idStr])) ? (int) $existingMap[$idStr] : null;
            $items[$i]['exists'] = $existingId !== null;
            $items[$i]['existing'] = $existingId ? ['id' => $existingId] : null;
        }

        return $items;
    }

    /**
     * Ajoute des informations de type (nom) + enregistre le typeId en base si absent.
     *
     * @param string $entity
     * @param array<int, array<string,mixed>> $items
     * @return array<int, array<string,mixed>>
     */
    private function withTypeLabelsAndRegistry(string $entity, array $items): array
    {
        $entity = strtolower($entity);
        $registry = match ($entity) {
            'resource' => ResourceType::class,
            'consumable' => ConsumableType::class,
            'item', 'equipment' => ItemType::class,
            default => null,
        };

        if (!$registry) {
            return $items;
        }

        // 1) Extraire les typeId uniques
        $typeIds = [];
        foreach ($items as $it) {
            if (!is_array($it)) continue;
            $typeId = $it['typeId'] ?? null;
            if (is_int($typeId) || (is_string($typeId) && ctype_digit($typeId))) {
                $n = (int) $typeId;
                if ($n > 0) {
                    $typeIds[] = $n;
                }
            }
        }
        $typeIds = array_values(array_unique($typeIds));
        if (empty($typeIds)) {
            return $items;
        }

        // 2) Charger en bulk les types connus
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

        // 3) Auto-touch des types absents (batch)
        $knownIds = array_map('intval', array_keys($byTypeId));
        $missing = array_values(array_diff($typeIds, $knownIds));
        if (!empty($missing)) {
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
                // ignore, best-effort
            }
        }

        // 4) Enrichir items
        foreach ($items as $i => $it) {
            if (!is_array($it)) continue;

            $typeId = $it['typeId'] ?? null;
            if (!(is_int($typeId) || (is_string($typeId) && ctype_digit($typeId)))) {
                continue;
            }
            $typeId = (int) $typeId;
            if ($typeId <= 0) continue;

            try {
                $typeModel = $byTypeId[$typeId] ?? null;
                if (!$typeModel) continue;

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
     * Enrichit les monstres avec `raceId` + `raceName`.
     *
     * DofusDB renvoie `race` (int). L'UI Krosmoz préfère `raceId` et surtout le nom.
     *
     * @param string $entity
     * @param array<int, array<string,mixed>> $items
     * @return array<int, array<string,mixed>>
     */
    private function withMonsterRaceLabel(string $entity, array $items): array
    {
        if (strtolower($entity) !== 'monster') {
            return $items;
        }

        $lang = (string) config('scrapping.data_collect.default_language', 'fr');

        foreach ($items as $i => $it) {
            if (!is_array($it)) continue;
            $race = $it['race'] ?? ($it['raceId'] ?? null);
            if (!(is_int($race) || (is_string($race) && preg_match('/^-?\d+$/', $race)))) {
                continue;
            }
            $raceId = (int) $race;
            $items[$i]['raceId'] = $raceId;

            try {
                $name = $this->monsterRacesCatalog->fetchName($raceId, $lang, false);
                $items[$i]['raceName'] = $name ?: null;
                // registry local (validation via `state`)
                \App\Models\Type\MonsterRace::touchDofusdbRace($raceId, $name ?: null);
            } catch (\Throwable) {
                $items[$i]['raceName'] = null;
            }
        }

        return $items;
    }

    /**
     * @return array{skip_cache?:bool, limit?:int, max_pages?:int, max_items?:int, start_skip?:int}
     */
    private function extractOptions(Request $request, string $entity): array
    {
        $options = [];

        if ($request->has('skip_cache')) {
            $options['skip_cache'] = filter_var($request->query('skip_cache'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('limit')) {
            $limit = $request->integer('limit');
            $options['limit'] = max(1, min(200, $limit));
        }

        if ($request->has('start_skip')) {
            $startSkip = $request->integer('start_skip');
            $options['start_skip'] = max(0, $startSkip);
        }

        // Pagination "page/per_page" (prioritaire sur l'usage manuel limit/offset si fourni)
        $pagePagination = $this->extractPagePagination($request);
        if ($pagePagination !== null) {
            $perPage = $pagePagination['per_page'];
            $page = $pagePagination['page'];

            if (!$request->has('start_skip')) {
                $options['start_skip'] = max(0, ($page - 1) * $perPage);
            }
            if (!$request->has('limit')) {
                $options['limit'] = $perPage;
            }
            // Important: un "bloc" = per_page items, même si DofusDB cappe à 50.
            if (!$request->has('max_items')) {
                $options['max_items'] = $perPage;
            }
        }

        $entityKey = strtolower($entity);
        $cap = EntityLimits::capFor($entityKey, 20000);

        $hasMaxItems = $request->has('max_items');
        $derivedMaxItems = false;
        if ($hasMaxItems) {
            $maxItems = $request->integer('max_items');
            // 0 = "tout" (borné par cap entité)
            if ($maxItems <= 0) {
                $options['max_items'] = $cap;
            } else {
                $options['max_items'] = max(1, min($cap, $maxItems));
            }
        }

        // Si max_items n'est pas fourni, on veut quand même pouvoir dépasser le cap DofusDB=50
        // pour atteindre le "limit" demandé (ex: limit=100 => 2 pages).
        if (!$hasMaxItems) {
            $want = isset($options['limit']) ? (int) $options['limit'] : 50;
            $want = max(1, min($cap, $want));
            $options['max_items'] = $want;
            $hasMaxItems = true;
            $derivedMaxItems = true;
        }

        // max_pages:
        // - si fourni: 0 = illimité (mais on borne via un calcul safe si max_items est connu)
        // - si non fourni mais max_items fourni: calcul automatique (pagination 50 par 50 en pratique)
        //
        // Compat UI:
        // - l'ancienne UI envoyait souvent max_pages=1 "par défaut", ce qui bloquait la pagination.
        // - si max_items a été dérivé automatiquement ET max_pages=1, on ignore ce max_pages.
        if ($request->has('max_pages')) {
            $maxPages = $request->integer('max_pages');
            if ($derivedMaxItems && $maxPages === 1) {
                // Ignorer "1 page" implicite et calculer plus bas.
            } else {
            if ($maxPages <= 0) {
                if ($hasMaxItems && isset($options['max_items'])) {
                    $assumedPageSize = 50;
                    $calc = (int) ceil(((int) $options['max_items']) / $assumedPageSize) + 2;
                    $options['max_pages'] = max(1, min(2000, $calc));
                } else {
                    // illimité (mais le collector garde de toute façon un max_items par défaut)
                    $options['max_pages'] = 0;
                }
            } else {
                $options['max_pages'] = max(1, min(2000, $maxPages));
            }
                return $options;
            }
        }

        if ($hasMaxItems && isset($options['max_items'])) {
            $assumedPageSize = 50;
            $calc = (int) ceil(((int) $options['max_items']) / $assumedPageSize) + 2;
            $options['max_pages'] = max(1, min(2000, $calc));
        }

        return $options;
    }

    /**
     * Pagination UX : page/per_page.
     *
     * @return array{page:int,per_page:int}|null
     */
    private function extractPagePagination(Request $request): ?array
    {
        $has = $request->has('page') || $request->has('per_page');
        if (!$has) return null;

        $page = max(1, $request->integer('page', 1));
        // On garde 100 comme valeur par défaut (UX) et on borne à 200 (cap historique côté API).
        $perPage = $request->integer('per_page', 100);
        $perPage = max(1, min(200, $perPage));

        return [
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * Injecte des filtres implicites pour certaines entités "alias" de /items.
     *
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    private function applyEntityDefaults(string $entity, array $filters): array
    {
        return $this->itemEntityTypeFilters->applyDefaults($entity, $filters, ItemEntityTypeFilterService::TYPE_MODE_ALLOWED);
    }

    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    private function applyEntityDefaultsWithMode(string $entity, array $filters, string $typeMode): array
    {
        return $this->itemEntityTypeFilters->applyDefaults($entity, $filters, $typeMode);
    }

    private function extractTypeMode(Request $request): string
    {
        $v = $request->query('type_mode', ItemEntityTypeFilterService::TYPE_MODE_ALLOWED);
        return is_string($v) ? $v : ItemEntityTypeFilterService::TYPE_MODE_ALLOWED;
    }
}

