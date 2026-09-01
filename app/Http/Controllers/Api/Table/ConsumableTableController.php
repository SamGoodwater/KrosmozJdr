<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Type\ConsumableType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ConsumableTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les consommables.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class ConsumableTableController extends Controller
{
    use InterpretsEntityTableFilters;
    use InterpretsEntityTableSort;
    use PaginatesEntityTable;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Consumable::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['level', 'rarity', 'consumable_type_id'] as $k) {
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

        $query = Consumable::query()
            ->visibleToUser($request->user())
            ->with(['createdBy', 'consumableType', 'resources'])
            ->withCount(['resources', 'creatures', 'campaigns', 'scenarios', 'shops']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('effect', 'like', "%{$search}%");
            });
        }
        if (array_key_exists('id', $filters) && $this->hasFilterValue($filters, 'id')) {
            $this->applyEqualityFilter($query, 'id', $filters['id'], 'int');
        }
        if ($this->normalizeRangeBounds($filters['level'] ?? null) !== null) {
            $this->applyIntegerRangeFilter($query, 'level', $filters['level']);
        } elseif ($this->hasFilterValue($filters, 'level')) {
            $this->applyEqualityFilter($query, 'level', $filters['level']);
        }
        if ($this->hasFilterValue($filters, 'rarity')) {
            $this->applyEqualityFilter($query, 'rarity', $filters['rarity'], 'int');
        }
        if ($this->hasFilterValue($filters, 'consumable_type_id')) {
            $this->applyEqualityFilter($query, 'consumable_type_id', $filters['consumable_type_id'], 'int');
        }

        $this->applyEntityTableIdList($query, $request);

        $allowedSort = ['id', 'name', 'level', 'rarity', 'consumable_type_id', 'dofusdb_id', 'created_at', 'updated_at'];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $limit = $pageResult['limit'];
        $page = $pageResult['page'];
        $pagination = $pageResult['pagination'];

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Consumable::class),
            'createAny' => Gate::allows('createAny', Consumable::class),
            'updateAny' => Gate::allows('updateAny', Consumable::class),
            'deleteAny' => Gate::allows('deleteAny', Consumable::class),
            'manageAny' => Gate::allows('manageAny', Consumable::class),
        ];

        $rarityColor = fn (int $r) => match ($r) {
            0 => 'success',
            1 => 'info',
            2 => 'primary',
            3 => 'warning',
            4 => 'error',
            5 => 'neutral',
            default => 'primary',
        };

        $consumableTypeOptions = ConsumableType::query()
            ->select(['id', 'name', 'dofusdb_type_id', 'show_in_catalog'])
            ->orderBy('name')
            ->get()
            ->map(fn (ConsumableType $t) => [
                'value' => (string) $t->id,
                'label' => (string) $t->name,
                'dofusdb_type_id' => $t->dofusdb_type_id !== null ? (int) $t->dofusdb_type_id : null,
                'show_in_catalog' => (bool) $t->show_in_catalog,
            ])
            ->values()
            ->all();

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Consumable $c) {
                $createdBy = $c->createdBy;
                $resources = $c->relationLoaded('resources')
                    ? $c->resources->map(fn ($res) => [
                        'id' => $res->id,
                        'name' => $res->name,
                        'image' => $res->image ?? null,
                        'pivot' => ['quantity' => $res->pivot?->quantity ?? 1],
                    ])->values()->all()
                    : [];

                return $c->toArray() + [
                    'resources' => $resources,
                    'consumableType' => $c->consumableType ? [
                        'id' => $c->consumableType->id,
                        'name' => $c->consumableType->name,
                    ] : null,
                    'resources_count' => (int) ($c->resources_count ?? 0),
                    'creatures_count' => (int) ($c->creatures_count ?? 0),
                    'campaigns_count' => (int) ($c->campaigns_count ?? 0),
                    'scenarios_count' => (int) ($c->scenarios_count ?? 0),
                    'shops_count' => (int) ($c->shops_count ?? 0),
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $c->created_at?->toISOString(),
                    'updated_at' => $c->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'consumables',
                    'query' => [
                        'search' => $search,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $limit,
                        'page' => $page,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => [
                        'rarity' => collect(Resource::RARITY)->map(fn ($label, $value) => [
                            'value' => (string) $value,
                            'label' => (string) $label,
                        ])->values()->all(),
                        'consumable_type_id' => $consumableTypeOptions,
                        'level' => $this->integerColumnBounds(Consumable::query()->visibleToUser($request->user()), 'level', 1, 200),
                    ],
                    'pagination' => $pagination,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Consumable $c) use ($rarityColor) {
            $showHref = route('entities.consumables.show', $c->id);
            $createdBy = $c->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $c->created_at ? $c->created_at->getTimestamp() : 0;
            $updatedAtLabel = $c->updated_at ? $c->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $c->updated_at ? $c->updated_at->getTimestamp() : 0;

            $typeName = $c->consumableType?->name ?? '-';
            $typeId = $c->consumable_type_id;
            $rarityRaw = $c->rarity;
            $rarityInt = is_numeric((string) $rarityRaw) ? (int) $rarityRaw : null;
            $rarityLabel = $rarityInt === null ? '-' : (Resource::RARITY[$rarityInt] ?? (string) $rarityInt);

            return [
                'id' => $c->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $c->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $c->name,
                            'sortValue' => (string) $c->name,
                        ],
                    ],
                    'level' => [
                        'type' => 'text',
                        'value' => $c->level ?: '-',
                        'params' => [
                            'filterValue' => $c->level ?: '',
                            'sortValue' => is_numeric((string) $c->level) ? (int) $c->level : (string) ($c->level ?? ''),
                            'searchValue' => (string) ($c->level ?? ''),
                        ],
                    ],
                    'effect' => [
                        'type' => 'text',
                        'value' => $c->effect ?: '-',
                        'params' => [
                            'sortValue' => (string) ($c->effect ?? ''),
                            'searchValue' => (string) ($c->effect ?? ''),
                            'filterValue' => (string) ($c->effect ?? ''),
                        ],
                    ],
                    'rarity' => [
                        'type' => 'badge',
                        'value' => $rarityLabel,
                        'params' => [
                            'color' => $rarityInt === null ? 'base' : $rarityColor($rarityInt),
                            'filterValue' => $rarityInt === null ? '' : (string) $rarityInt,
                            'sortValue' => $rarityInt === null ? -1 : $rarityInt,
                            'tooltip' => Resource::RARITY_HELPER,
                        ],
                    ],
                    'consumable_type' => [
                        'type' => 'text',
                        'value' => $typeName,
                        'params' => [
                            'filterValue' => $typeId ? (string) $typeId : '',
                            'sortValue' => $typeName,
                            'searchValue' => $typeName,
                            'tooltip' => $typeName !== '' && $typeName !== '-'
                                ? 'Catégorie du consommable (potion, nourriture, parchemin…).'
                                : '',
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
                    'entity' => $c->toArray() + [
                        'consumableType' => $c->consumableType ? [
                            'id' => $c->consumableType->id,
                            'name' => $c->consumableType->name,
                        ] : null,
                        'resources_count' => (int) ($c->resources_count ?? 0),
                        'creatures_count' => (int) ($c->creatures_count ?? 0),
                        'campaigns_count' => (int) ($c->campaigns_count ?? 0),
                        'scenarios_count' => (int) ($c->scenarios_count ?? 0),
                        'shops_count' => (int) ($c->shops_count ?? 0),
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
                'entityType' => 'consumables',
                'query' => [
                    'search' => $search,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                    'page' => $page,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => [
                    'rarity' => collect(Resource::RARITY)->map(fn ($label, $value) => [
                        'value' => (string) $value,
                        'label' => (string) $label,
                    ])->values()->all(),
                    'consumable_type_id' => $consumableTypeOptions,
                ],
                'pagination' => $pagination,
            ],
            'rows' => $tableRows,
        ]);
    }
}
