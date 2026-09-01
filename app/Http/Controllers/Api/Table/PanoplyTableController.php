<?php

namespace App\Http\Controllers\Api\Table;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
use App\Models\Entity\Panoply;
use App\Models\Type\ItemType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * PanoplyTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les panoplies.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class PanoplyTableController extends Controller
{
    use InterpretsEntityTableFilters;
    use InterpretsEntityTableSort;
    use PaginatesEntityTable;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Panoply::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['state', 'items_count', 'item_type_id'] as $k) {
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

        $viewer = $request->user();

        $query = Panoply::query()
            ->visibleToUser($viewer)
            ->with([
                'createdBy',
                'items' => fn ($q) => $q
                    ->visibleToUser($viewer)
                    ->select(['items.id', 'items.name', 'items.level', 'items.image', 'items.item_type_id']),
            ])
            ->withCount([
                'items' => fn ($q) => $q->visibleToUser($viewer),
                'npcs',
                'campaigns',
                'scenarios',
                'shops',
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('bonus', 'like', "%{$search}%");
            });
        }

        $this->applyPanoplyTableFilters($query, $filters, $viewer);

        $this->applyEntityTableIdList($query, $request);

        $allowedSort = ['id', 'name', 'items_count', 'state', 'dofusdb_id', 'created_at', 'updated_at'];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $limit = $pageResult['limit'];
        $page = $pageResult['page'];
        $pagination = $pageResult['pagination'];

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Panoply::class),
            'createAny' => Gate::allows('createAny', Panoply::class),
            'updateAny' => Gate::allows('updateAny', Panoply::class),
            'deleteAny' => Gate::allows('deleteAny', Panoply::class),
            'manageAny' => Gate::allows('manageAny', Panoply::class),
        ];

        $filterOptions = $this->buildPanoplyFilterOptions($viewer);

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Panoply $p) {
                $createdBy = $p->createdBy;

                return $p->toArray() + [
                    'items' => $p->relationLoaded('items')
                        ? $p->items->map(static fn ($item) => [
                            'id' => $item->id,
                            'name' => $item->name,
                            'level' => $item->level,
                            'image' => $item->image,
                        ])->values()->all()
                        : [],
                    'items_count' => $p->items_count ?? 0,
                    'npcs_count' => $p->npcs_count ?? 0,
                    'campaigns_count' => $p->campaigns_count ?? 0,
                    'scenarios_count' => $p->scenarios_count ?? 0,
                    'shops_count' => $p->shops_count ?? 0,
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $p->created_at?->toISOString(),
                    'updated_at' => $p->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'panoplies',
                    'query' => [
                        'search' => $search,
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

        $tableRows = $rows->map(function (Panoply $p) {
            $showHref = route('entities.panoplies.show', $p->id);
            $createdBy = $p->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $p->created_at ? $p->created_at->getTimestamp() : 0;
            $updatedAtLabel = $p->updated_at ? $p->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $p->updated_at ? $p->updated_at->getTimestamp() : 0;

            return [
                'id' => $p->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $p->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $p->name,
                            'sortValue' => (string) $p->name,
                        ],
                    ],
                    'bonus' => [
                        'type' => 'text',
                        'value' => (string) ($p->bonus ?? '-'),
                        'params' => [
                            'searchValue' => (string) ($p->bonus ?? ''),
                        ],
                    ],
                    'items_count' => [
                        'type' => 'text',
                        'value' => (string) ((int) ($p->items_count ?? 0)),
                        'params' => [
                            'sortValue' => (int) ($p->items_count ?? 0),
                        ],
                    ],
                    'dofusdb_id' => [
                        'type' => 'text',
                        'value' => $p->dofusdb_id ? (string) $p->dofusdb_id : '-',
                        'params' => [
                            'sortValue' => $p->dofusdb_id ?? 0,
                            'searchValue' => (string) ($p->dofusdb_id ?? ''),
                        ],
                    ],
                    'created_by' => [
                        'type' => 'text',
                        'value' => $createdByLabel,
                        'params' => [
                            'searchValue' => $createdByLabel,
                            'sortValue' => $createdByLabel,
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
                'rowParams' => [
                    'entity' => $p->toArray() + [
                        'items_count' => $p->items_count ?? 0,
                        'npcs_count' => $p->npcs_count ?? 0,
                        'campaigns_count' => $p->campaigns_count ?? 0,
                        'scenarios_count' => $p->scenarios_count ?? 0,
                        'shops_count' => $p->shops_count ?? 0,
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
                'entityType' => 'panoplies',
                'query' => [
                    'search' => $search,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                    'page' => $page,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
                'pagination' => $pagination,
            ],
            'rows' => $tableRows,
        ]);
    }

    /**
     * Filtres panoplie : état, nombre de pièces, types d’objets présents.
     *
     * @param  Builder<Panoply>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyPanoplyTableFilters(Builder $query, array $filters, mixed $viewer): void
    {
        if ($this->hasFilterValue($filters, 'state')) {
            $this->applyEqualityFilter($query, 'state', $filters['state']);
        }
        if ($this->hasFilterValue($filters, 'items_count')) {
            $this->applyHavingRangeFilter($query, 'items_count', $filters['items_count']);
        }
        if ($this->hasFilterValue($filters, 'item_type_id')) {
            $ids = $this->castFilterList($filters['item_type_id'], 'int');
            if ($ids !== []) {
                $query->whereHas('items', function (Builder $q) use ($ids, $viewer) {
                    $q->visibleToUser($viewer)->whereIn('item_type_id', $ids);
                });
            }
        }
    }

    /**
     * @return array{
     *     state: list<array{value: string, label: string}>,
     *     item_type_id: list<array{value: string, label: string, dofusdb_type_id: int|null, show_in_catalog: bool}>,
     *     items_count: array{min: int, max: int}
     * }
     */
    private function buildPanoplyFilterOptions(mixed $viewer): array
    {
        $itemTypes = ItemType::query()
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

        $countQuery = Panoply::query()
            ->visibleToUser($viewer)
            ->withCount(['items' => fn ($q) => $q->visibleToUser($viewer)]);

        return [
            'state' => EntityState::options(),
            'item_type_id' => $itemTypes,
            'items_count' => $this->withCountColumnBounds($countQuery, 'items_count', 0, 20),
        ];
    }
}
