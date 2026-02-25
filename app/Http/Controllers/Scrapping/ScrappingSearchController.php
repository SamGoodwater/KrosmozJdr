<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Config\EntityMetaService;
use App\Services\Scrapping\Core\Search\SearchResultEnricher;
use App\Services\Scrapping\DataCollect\ItemEntityTypeFilterService;
use App\Services\Scrapping\DataCollect\MonsterRaceFilterService;
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
        private CollectAliasResolver $aliasResolver,
        private EntityMetaService $entityMeta,
        private CollectService $collectService,
        private SearchResultEnricher $searchEnricher,
        private ItemEntityTypeFilterService $itemEntityTypeFilters,
        private MonsterRaceFilterService $monsterRaceFilters,
    ) {}

    public function search(Request $request, string $entity): JsonResponse
    {
        $source = 'dofusdb';

        // Liste des entités depuis Core (config/) + alias « class » pour breed + resource/consumable/equipment (item)
        $available = $this->configLoader->listEntities($source);
        if (in_array('breed', $available, true)) {
            $available[] = 'class';
        }
        if (in_array('item', $available, true)) {
            $available[] = 'resource';
            $available[] = 'consumable';
            $available[] = 'equipment';
        }
        sort($available);

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

        // resource / consumable / equipment : résolution alias → item + filtre superTypeGroup
        $collectEntity = $entity;
        $aliasCfg = $this->aliasResolver->resolve($entity);
        if ($aliasCfg !== null && isset($aliasCfg['entity'], $aliasCfg['defaultFilter']) && $aliasCfg['entity'] === 'item') {
            $collectEntity = 'item';
            $filters = array_merge($aliasCfg['defaultFilter'], $filters);
        }

        // Monstres: race_mode (all/allowed/selected) -> injecter raceIds par défaut si besoin.
        if (strtolower($entity) === 'monster') {
            $raceMode = $this->extractRaceMode($request);
            $filters = $this->monsterRaceFilters->applyDefaults($filters, $raceMode);
        }

        $options = $this->extractOptions($request, $entity);

        $result = $this->collectService->fetchManyResult('dofusdb', $collectEntity, $filters, $options);
        $items = $this->searchEnricher->enrich($entity, $result['items']);

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
        $cap = $this->entityMeta->getMaxIdForType($entityKey);

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

