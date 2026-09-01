<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Type\ItemType;
use App\Support\Entity\ItemPanoplyPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ItemTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les items.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class ItemTableController extends Controller
{
    use InterpretsEntityTableFilters;
    use InterpretsEntityTableSort;
    use PaginatesEntityTable;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Item::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['level', 'rarity', 'item_type_id', 'state'] as $k) {
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
        $query = Item::query()
            ->visibleToUser($viewer)
            ->with([
                'createdBy',
                'itemType',
                'resources' => fn ($q) => $q->visibleToUser($viewer),
                ...ItemPanoplyPayload::eagerLoad($viewer),
            ])
            ->withCount([
                'resources' => fn ($q) => $q->visibleToUser($viewer),
                'panoplies' => fn ($q) => $q->visibleToUser($viewer),
                'shops',
                'campaigns',
                'scenarios',
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('effect', 'like', "%{$search}%")
                    ->orWhere('bonus', 'like', "%{$search}%");
            });
        }

        if ($this->hasFilterValue($filters, 'level')) {
            $this->applyEqualityFilter($query, 'level', $filters['level']);
        }
        if ($this->hasFilterValue($filters, 'item_type_id')) {
            $this->applyEqualityFilter($query, 'item_type_id', $filters['item_type_id'], 'int');
        }
        if ($this->hasFilterValue($filters, 'rarity')) {
            $this->applyEqualityFilter($query, 'rarity', $filters['rarity'], 'int');
        }
        if ($this->hasFilterValue($filters, 'id')) {
            $this->applyEqualityFilter($query, 'id', $filters['id'], 'int');
        }
        if ($this->hasFilterValue($filters, 'state')) {
            $this->applyEqualityFilter($query, 'state', $filters['state']);
        }

        $this->applyEntityTableIdList($query, $request);

        $allowedSort = ['id', 'name', 'level', 'rarity', 'item_type_id', 'price_custom', 'dofusdb_id', 'state', 'created_at', 'updated_at'];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $limit = $pageResult['limit'];
        $page = $pageResult['page'];
        $pagination = $pageResult['pagination'];

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Item::class),
            'createAny' => Gate::allows('createAny', Item::class),
            'updateAny' => Gate::allows('updateAny', Item::class),
            'deleteAny' => Gate::allows('deleteAny', Item::class),
            'manageAny' => Gate::allows('manageAny', Item::class),
        ];

        // Options filtres
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

        $rarityColor = fn (int $r) => match ($r) {
            0 => 'success',
            1 => 'info',
            2 => 'primary',
            3 => 'warning',
            4 => 'error',
            5 => 'neutral',
            default => 'primary',
        };

        $filterOptions = [
            'item_type_id' => $itemTypes,
            'rarity' => collect(Resource::RARITY)
                ->map(fn ($label, $value) => ['value' => (string) $value, 'label' => (string) $label])
                ->values()
                ->all(),
            'level' => [
                ['value' => '1', 'label' => '1'],
                ['value' => '50', 'label' => '50'],
                ['value' => '100', 'label' => '100'],
                ['value' => '150', 'label' => '150'],
                ['value' => '200', 'label' => '200'],
            ],
        ];

        // Option B: renvoyer des entités brutes (le front génère `cells`).
        if ($format === 'entities') {
            $entities = $rows->map(function (Item $it) use ($request) {
                $createdBy = $it->createdBy;
                $itemType = $it->itemType;

                return [
                    'id' => $it->id,
                    'dofusdb_id' => $it->dofusdb_id,
                    'official_id' => $it->official_id,
                    'name' => $it->name,
                    'description' => $it->description,
                    'effect' => $it->effect,
                    'bonus' => $it->bonus,
                    'recipe' => $it->recipe,
                    'level' => $it->level,
                    'price' => $it->displayPriceKamas(),
                    'rarity' => $it->rarity,
                    'dofus_version' => $it->dofus_version,
                    'state' => (string) ($it->state ?? 'draft'),
                    'read_level' => (int) ($it->read_level ?? 0),
                    'write_level' => (int) ($it->write_level ?? 0),
                    'image' => $it->image,
                    'auto_update' => (bool) $it->auto_update,
                    'item_type_id' => $it->item_type_id,
                    'itemType' => $itemType ? [
                        'id' => $itemType->id,
                        'name' => $itemType->name,
                    ] : null,
                    'resources' => $it->resources->map(fn ($res) => [
                        'id' => $res->id,
                        'name' => $res->name,
                        'image' => $res->image ?? null,
                        'pivot' => ['quantity' => $res->pivot?->quantity ?? 1],
                    ])->values()->all(),
                    'resources_count' => (int) ($it->resources_count ?? 0),
                    'panoplies' => ItemPanoplyPayload::fromItem($it, $request->user()),
                    'panoplies_count' => (int) ($it->panoplies_count ?? 0),
                    'shops_count' => (int) ($it->shops_count ?? 0),
                    'campaigns_count' => (int) ($it->campaigns_count ?? 0),
                    'scenarios_count' => (int) ($it->scenarios_count ?? 0),
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $it->created_at?->toISOString(),
                    'updated_at' => $it->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'items',
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

        $tableRows = $rows->map(function (Item $it) use ($rarityColor, $request) {
            $showHref = route('entities.items.show', $it->id);
            $dofusDbHref = $it->dofusdb_id ? "https://www.dofus.com/fr/mmorpg/encyclopedie/equipements/{$it->dofusdb_id}" : null;

            $createdBy = $it->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $it->created_at ? $it->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $it->created_at ? $it->created_at->getTimestamp() : 0;
            $updatedAtLabel = $it->updated_at ? $it->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $it->updated_at ? $it->updated_at->getTimestamp() : 0;

            $itemTypeId = $it->item_type_id;
            $itemTypeName = $it->itemType?->name ?? '-';

            $rarityRaw = $it->rarity;
            $rarityInt = is_numeric((string) $rarityRaw) ? (int) $rarityRaw : null;
            $rarityKey = $rarityInt === null ? '' : (string) $rarityInt;
            $rarityLabel = $rarityInt === null ? '-' : (Resource::RARITY[$rarityInt] ?? (string) $rarityInt);
            $rarityColorToken = $rarityInt === null ? 'primary' : $rarityColor($rarityInt);
            $raritySort = $rarityInt === null ? -1 : $rarityInt;

            return [
                'id' => $it->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $it->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $it->name,
                            'sortValue' => (string) $it->name,
                        ],
                    ],
                    'level' => [
                        'type' => 'text',
                        'value' => $it->level ?? '-',
                        'params' => [
                            'filterValue' => (string) ($it->level ?? ''),
                            'sortValue' => is_numeric((string) $it->level) ? (int) $it->level : (string) ($it->level ?? ''),
                            'searchValue' => (string) ($it->level ?? ''),
                        ],
                    ],
                    'effect' => [
                        'type' => 'text',
                        'value' => $it->effect ?: ($it->bonus ?: '-'),
                        'params' => [
                            'sortValue' => (string) ($it->effect ?? $it->bonus ?? ''),
                            'searchValue' => trim((string) (($it->effect ?? '').' '.($it->bonus ?? ''))),
                            'filterValue' => trim((string) (($it->effect ?? '').' '.($it->bonus ?? ''))),
                        ],
                    ],
                    'rarity' => [
                        'type' => 'badge',
                        'value' => $rarityLabel,
                        'params' => [
                            'color' => $rarityColorToken,
                            'filterValue' => $rarityKey,
                            'sortValue' => $raritySort,
                            'tooltip' => Resource::RARITY_HELPER,
                        ],
                    ],
                    'item_type' => [
                        'type' => 'text',
                        'value' => $itemTypeName,
                        'params' => [
                            'filterValue' => $itemTypeId ? (string) $itemTypeId : '',
                            'sortValue' => $itemTypeName,
                            'searchValue' => $itemTypeName,
                            'tooltip' => $itemTypeName !== '' && $itemTypeName !== '-'
                                ? 'Emplacement (anneau, cape, arme…).'
                                : '',
                        ],
                    ],
                    'dofusdb_id' => [
                        'type' => 'route',
                        'value' => $it->dofusdb_id ? (string) $it->dofusdb_id : '-',
                        'params' => [
                            'href' => $dofusDbHref,
                            'target' => '_blank',
                            'sortValue' => $it->dofusdb_id ?? 0,
                            'filterValue' => (string) ($it->dofusdb_id ?? ''),
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
                'rowParams' => [
                    'entity' => [
                        'id' => $it->id,
                        'official_id' => $it->official_id,
                        'dofusdb_id' => $it->dofusdb_id,
                        'name' => $it->name,
                        'level' => $it->level,
                        'description' => $it->description,
                        'effect' => $it->effect,
                        'bonus' => $it->bonus,
                        'recipe' => $it->recipe,
                        'price' => $it->displayPriceKamas(),
                        'rarity' => $it->rarity,
                        'dofus_version' => $it->dofus_version,
                        'state' => (string) ($it->state ?? 'draft'),
                        'read_level' => (int) ($it->read_level ?? 0),
                        'write_level' => (int) ($it->write_level ?? 0),
                        'image' => $it->image,
                        'auto_update' => (bool) $it->auto_update,
                        'item_type_id' => $it->item_type_id,
                        'itemType' => $it->itemType ? [
                            'id' => $it->itemType->id,
                            'name' => $it->itemType->name,
                        ] : null,
                        'resources_count' => (int) ($it->resources_count ?? 0),
                        'panoplies' => ItemPanoplyPayload::fromItem($it, $request->user()),
                        'panoplies_count' => (int) ($it->panoplies_count ?? 0),
                        'shops_count' => (int) ($it->shops_count ?? 0),
                        'campaigns_count' => (int) ($it->campaigns_count ?? 0),
                        'scenarios_count' => (int) ($it->scenarios_count ?? 0),
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
                'entityType' => 'items',
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
            ],
            'rows' => $tableRows,
        ]);
    }
}
