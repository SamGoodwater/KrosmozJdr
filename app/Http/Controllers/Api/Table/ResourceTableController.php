<?php

namespace App\Http\Controllers\Api\Table;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
use App\Models\Entity\Resource;
use App\Models\Type\ResourceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ResourceTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les Ressources.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 *
 * Hybride client-first:
 * - cet endpoint peut servir à charger un dataset "base"
 * - ou à charger un sous-ensemble via une URL paramétrée (Option A)
 *
 * @example
 * GET /api/tables/resources?limit=5000
 * GET /api/tables/resources?filters[rarity]=2&filters[resource_type_id]=12
 */
class ResourceTableController extends Controller
{
    use InterpretsEntityTableFilters;
    use InterpretsEntityTableSort;
    use PaginatesEntityTable;

    private const STATE_COLORS = [
        'raw' => 'neutral',
        'draft' => 'warning',
        'playable' => 'success',
        'archived' => 'error',
    ];

    private function stateColor(?string $state): string
    {
        $s = (string) ($state ?? '');

        return self::STATE_COLORS[$s] ?? 'base';
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Resource::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);

        // Compat: accepter des filtres "flat" (rarity=2) en plus de filters[rarity]=2
        foreach (['level', 'resource_type_id', 'rarity', 'auto_update', 'state', 'read_level', 'write_level'] as $k) {
            if (! array_key_exists($k, $filters) && $request->has($k)) {
                $filters[$k] = $request->get($k);
            }
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $sortsPayload = $request->input('sorts');
        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (is_array($sortsPayload) && isset($sortsPayload[0]) && is_array($sortsPayload[0])) {
            $sort = (string) ($sortsPayload[0]['field'] ?? $sortsPayload[0]['column'] ?? $sort);
            $order = strtolower((string) ($sortsPayload[0]['dir'] ?? $sortsPayload[0]['order'] ?? $order));
        }
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Resource::query()
            ->visibleToUser($request->user())
            ->with(['createdBy', 'resourceType', 'recipeIngredients'])
            ->withCount(['recipeIngredients']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('effect', 'like', "%{$search}%");
            });
        }

        // Filtres (select / multi)
        if ($this->hasFilterValue($filters, 'level')) {
            $this->applyIntegerRangeFilter($query, 'level', $filters['level']);
        }
        if ($this->hasFilterValue($filters, 'resource_type_id')) {
            $this->applyEqualityFilter($query, 'resource_type_id', $filters['resource_type_id'], 'int');
        }
        foreach (['rarity', 'auto_update'] as $k) {
            if ($this->hasFilterValue($filters, $k)) {
                $this->applyEqualityFilter($query, $k, $filters[$k], 'int');
            }
        }
        if ($this->hasFilterValue($filters, 'state')) {
            $this->applyEqualityFilter($query, 'state', $filters['state']);
        }
        if ($this->hasFilterValue($filters, 'read_level')) {
            $this->applyEqualityFilter($query, 'read_level', $filters['read_level'], 'int');
        }
        if ($this->hasFilterValue($filters, 'write_level')) {
            $this->applyEqualityFilter($query, 'write_level', $filters['write_level'], 'int');
        }
        if ($this->hasFilterValue($filters, 'id')) {
            $this->applyEqualityFilter($query, 'id', $filters['id'], 'int');
        }

        $this->applyEntityTableIdList($query, $request);

        // Tri (liste blanche)
        $allowedSort = ['id', 'name', 'level', 'rarity', 'price', 'weight', 'state', 'read_level', 'write_level', 'auto_update', 'resource_type_id', 'dofusdb_id', 'created_at', 'updated_at'];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $limit = $pageResult['limit'];
        $page = $pageResult['page'];
        $pagination = $pageResult['pagination'];

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Resource::class),
            'createAny' => Gate::allows('createAny', Resource::class),
            'updateAny' => Gate::allows('updateAny', Resource::class),
            'deleteAny' => Gate::allows('deleteAny', Resource::class),
            'manageAny' => Gate::allows('manageAny', Resource::class),
        ];

        $resourceTypes = ResourceType::query()
            ->select(['id', 'name', 'dofusdb_type_id', 'show_in_catalog'])
            ->orderBy('name')
            ->limit(5000)
            ->get()
            ->map(fn ($t) => [
                'value' => (string) $t->id,
                'label' => (string) $t->name,
                'dofusdb_type_id' => $t->dofusdb_type_id !== null ? (int) $t->dofusdb_type_id : null,
                'show_in_catalog' => (bool) $t->show_in_catalog,
            ])
            ->values()
            ->all();

        $filterOptions = [
            'resource_type_id' => $resourceTypes,
            'rarity' => collect(Resource::RARITY)
                ->map(fn ($label, $value) => ['value' => (string) $value, 'label' => (string) $label])
                ->values()
                ->all(),
            'state' => EntityState::options(),
            'level' => $this->integerColumnBounds(
                Resource::query()->visibleToUser($request->user()),
                'level',
                1,
                200
            ),
        ];

        // Option B: renvoyer des entités brutes (le front génère `cells`).
        if ($format === 'entities') {
            $entities = $rows->map(function (Resource $r) {
                $createdBy = $r->createdBy;
                $resourceType = $r->resourceType;

                return [
                    'id' => $r->id,
                    'dofusdb_id' => $r->dofusdb_id,
                    'official_id' => $r->official_id,
                    'name' => $r->name,
                    'description' => $r->description,
                    'effect' => $r->effect,
                    'level' => $r->level,
                    'price' => $r->price,
                    'weight' => $r->weight,
                    'rarity' => $r->rarity,
                    'dofus_version' => $r->dofus_version,
                    'state' => (string) ($r->state ?? 'draft'),
                    'read_level' => (int) ($r->read_level ?? 0),
                    'write_level' => (int) ($r->write_level ?? 0),
                    'image' => $r->image,
                    'auto_update' => (bool) $r->auto_update,
                    'resource_type_id' => $r->resource_type_id,
                    'resourceType' => $resourceType ? [
                        'id' => $resourceType->id,
                        'name' => $resourceType->name,
                    ] : null,
                    'recipe_ingredients' => $r->relationLoaded('recipeIngredients')
                        ? $r->recipeIngredients->map(fn ($ing) => [
                            'id' => $ing->id,
                            'name' => $ing->name,
                            'image' => $ing->image ?? null,
                            'pivot' => ['quantity' => $ing->pivot?->quantity ?? 1],
                        ])->values()->all()
                        : [],
                    'recipe_ingredients_count' => (int) ($r->recipe_ingredients_count ?? 0),
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $r->created_at?->toISOString(),
                    'updated_at' => $r->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'resources',
                    'query' => [
                        'search' => $search,
                        'filters' => $filters,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $limit,
                        'page' => $page,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => $filterOptions,
                    'pagination' => $pagination,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $rarityColor = fn (int $r) => match ($r) {
            0 => 'success',
            1 => 'info',
            2 => 'primary',
            3 => 'warning',
            4 => 'error',
            5 => 'neutral',
            default => 'primary',
        };

        $tableRows = $rows->map(function (Resource $r) use ($rarityColor) {
            $showHref = route('entities.resources.show', $r->id);
            $rarityLabel = Resource::RARITY[$r->rarity] ?? (string) $r->rarity;

            $resourceTypeId = $r->resource_type_id;
            $resourceTypeName = $r->resourceType?->name ?? '-';

            $dofusDbHref = $r->dofusdb_id ? "https://www.dofus.com/fr/mmorpg/encyclopedie/ressources/{$r->dofusdb_id}" : null;

            $createdBy = $r->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $r->created_at ? $r->created_at->getTimestamp() : 0;
            $updatedAtLabel = $r->updated_at ? $r->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $r->updated_at ? $r->updated_at->getTimestamp() : 0;
            $state = (string) ($r->state ?? 'draft');

            return [
                'id' => $r->id,
                'cells' => [
                    'image' => [
                        'type' => 'image',
                        'value' => $r->image,
                        'params' => [
                            'alt' => $r->name,
                            'searchValue' => $r->name,
                        ],
                    ],
                    'name' => [
                        'type' => 'route',
                        'value' => $r->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => $r->name,
                            'sortValue' => $r->name,
                        ],
                    ],
                    'level' => [
                        'type' => 'text',
                        'value' => $r->level ?: '-',
                        'params' => [
                            'filterValue' => $r->level ?: '',
                            'sortValue' => is_numeric((string) $r->level) ? (int) $r->level : (string) ($r->level ?? ''),
                            'searchValue' => (string) ($r->level ?? ''),
                        ],
                    ],
                    'effect' => [
                        'type' => 'text',
                        'value' => $r->effect ?: '-',
                        'params' => [
                            'sortValue' => (string) ($r->effect ?? ''),
                            'searchValue' => (string) ($r->effect ?? ''),
                            'filterValue' => (string) ($r->effect ?? ''),
                        ],
                    ],
                    'resource_type' => [
                        'type' => 'text',
                        'value' => $resourceTypeName,
                        'params' => [
                            'filterValue' => $resourceTypeId ? (string) $resourceTypeId : '',
                            'sortValue' => $resourceTypeName,
                            'searchValue' => $resourceTypeName,
                            'tooltip' => $resourceTypeName !== '' && $resourceTypeName !== '-'
                                ? 'Catégorie métier de la ressource (bois, minerai, plante…).'
                                : '',
                        ],
                    ],
                    'rarity' => [
                        'type' => 'badge',
                        'value' => $rarityLabel,
                        'params' => [
                            'color' => $rarityColor((int) $r->rarity),
                            'filterValue' => (string) ((int) $r->rarity),
                            'sortValue' => (int) $r->rarity,
                            'tooltip' => Resource::RARITY_HELPER,
                        ],
                    ],
                    'price' => [
                        'type' => 'text',
                        'value' => $r->price ?: '-',
                        'params' => [
                            'sortValue' => is_numeric((string) $r->price) ? (float) $r->price : (string) ($r->price ?? ''),
                        ],
                    ],
                    'weight' => [
                        'type' => 'text',
                        'value' => $r->weight ?: '-',
                        'params' => [
                            'sortValue' => is_numeric((string) $r->weight) ? (float) $r->weight : (string) ($r->weight ?? ''),
                        ],
                    ],
                    'state' => [
                        'type' => 'badge',
                        'value' => $state,
                        'params' => [
                            'color' => $this->stateColor($state),
                            'filterValue' => $state,
                            'sortValue' => $state,
                        ],
                    ],
                    'auto_update' => [
                        'type' => 'badge',
                        'value' => $r->auto_update ? 'Oui' : 'Non',
                        'params' => [
                            'color' => $r->auto_update ? 'success' : 'error',
                            'filterValue' => $r->auto_update ? '1' : '0',
                            'sortValue' => $r->auto_update ? 1 : 0,
                        ],
                    ],
                    'dofusdb_id' => [
                        'type' => 'route',
                        'value' => $r->dofusdb_id ? (string) $r->dofusdb_id : '-',
                        'params' => [
                            'href' => $dofusDbHref,
                            'target' => '_blank',
                            'sortValue' => $r->dofusdb_id ?? 0,
                            'filterValue' => (string) ($r->dofusdb_id ?? ''),
                        ],
                    ],
                    'created_by' => [
                        'type' => 'text',
                        'value' => $createdByLabel,
                        'params' => [
                            'sortValue' => $createdByLabel,
                            'searchValue' => $createdByLabel,
                        ],
                    ],
                    'created_at' => [
                        'type' => 'text',
                        'value' => $createdAtLabel,
                        'params' => [
                            'sortValue' => $createdAtSort,
                            'searchValue' => $createdAtLabel,
                        ],
                    ],
                    'updated_at' => [
                        'type' => 'text',
                        'value' => $updatedAtLabel,
                        'params' => [
                            'sortValue' => $updatedAtSort,
                            'searchValue' => $updatedAtLabel,
                        ],
                    ],
                ],
                // Données brutes minimales (utile pour actions/bulk panel future)
                'rowParams' => [
                    'entity' => [
                        'id' => $r->id,
                        'name' => $r->name,
                        'description' => $r->description,
                        'effect' => $r->effect,
                        'level' => $r->level,
                        'price' => $r->price,
                        'weight' => $r->weight,
                        'rarity' => $r->rarity,
                        'dofus_version' => $r->dofus_version,
                        'state' => (string) ($r->state ?? 'draft'),
                        'read_level' => (int) ($r->read_level ?? 0),
                        'write_level' => (int) ($r->write_level ?? 0),
                        'image' => $r->image,
                        'auto_update' => (bool) $r->auto_update,
                        'resource_type_id' => $r->resource_type_id,
                        'resourceType' => $r->resourceType ? [
                            'id' => $r->resourceType->id,
                            'name' => $r->resourceType->name,
                        ] : null,
                        'recipe_ingredients_count' => (int) ($r->recipe_ingredients_count ?? 0),
                        'createdBy' => $createdBy ? [
                            'id' => $createdBy->id,
                            'name' => $createdBy->name,
                            'email' => $createdBy->email,
                        ] : null,
                    ],
                ],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'resources',
                'query' => [
                    'search' => $search,
                    'filters' => $filters,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
                'pagination' => $pagination,
                'format' => 'cells',
            ],
            'rows' => $tableRows,
        ]);
    }
}
