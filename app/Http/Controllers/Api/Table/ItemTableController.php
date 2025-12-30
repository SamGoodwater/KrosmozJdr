<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Item;
use App\Models\Type\ItemType;
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
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Item::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['level', 'rarity', 'item_type_id'] as $k) {
            if (!array_key_exists($k, $filters) && $request->has($k)) {
                $filters[$k] = $request->get($k);
            }
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Item::query()->with(['createdBy', 'itemType']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('level', $filters) && $filters['level'] !== '' && $filters['level'] !== null) {
            $query->where('level', $filters['level']);
        }
        if (array_key_exists('item_type_id', $filters) && $filters['item_type_id'] !== '' && $filters['item_type_id'] !== null) {
            $query->where('item_type_id', (int) $filters['item_type_id']);
        }
        if (array_key_exists('rarity', $filters) && $filters['rarity'] !== '' && $filters['rarity'] !== null) {
            // Rareté peut être string (common/uncommon/...) ou int selon DB.
            $query->where('rarity', $filters['rarity']);
        }

        $allowedSort = ['id', 'name', 'level', 'rarity', 'dofusdb_id', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Item::class),
            'createAny' => Gate::allows('createAny', Item::class),
            'updateAny' => Gate::allows('updateAny', Item::class),
            'deleteAny' => Gate::allows('deleteAny', Item::class),
            'manageAny' => Gate::allows('manageAny', Item::class),
        ];

        // Options filtres
        $itemTypes = ItemType::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->limit(5000)
            ->get()
            ->map(fn ($t) => ['value' => (string) $t->id, 'label' => (string) $t->name])
            ->values()
            ->all();

        $rarityMap = [
            '0' => ['label' => 'Commun', 'color' => 'success', 'sort' => 0],
            '1' => ['label' => 'Peu commun', 'color' => 'info', 'sort' => 1],
            '2' => ['label' => 'Rare', 'color' => 'primary', 'sort' => 2],
            '3' => ['label' => 'Épique', 'color' => 'warning', 'sort' => 3],
            '4' => ['label' => 'Légendaire', 'color' => 'error', 'sort' => 4],
        ];

        $filterOptions = [
            'item_type_id' => $itemTypes,
            'rarity' => collect($rarityMap)
                ->map(fn ($meta, $value) => ['value' => (string) $value, 'label' => (string) $meta['label']])
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
            $entities = $rows->map(function (Item $it) {
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
                    'price' => $it->price,
                    'rarity' => $it->rarity,
                    'dofus_version' => $it->dofus_version,
                    'usable' => (int) ($it->usable ?? 0),
                    'image' => $it->image,
                    'auto_update' => (bool) $it->auto_update,
                    'item_type_id' => $it->item_type_id,
                    'itemType' => $itemType ? [
                        'id' => $itemType->id,
                        'name' => $itemType->name,
                    ] : null,
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
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => $filterOptions,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Item $it) use ($rarityMap) {
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
            $rarityKey = is_string($rarityRaw) ? $rarityRaw : (string) $rarityRaw;
            $rarityMeta = $rarityMap[$rarityKey] ?? null;

            $rarityLabel = $rarityMeta ? $rarityMeta['label'] : ($rarityKey ?: '-');
            $rarityColor = $rarityMeta ? $rarityMeta['color'] : 'primary';
            $raritySort = $rarityMeta ? (int) $rarityMeta['sort'] : $rarityKey;

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
                    'rarity' => [
                        'type' => 'badge',
                        'value' => $rarityLabel,
                        'params' => [
                            'color' => $rarityColor,
                            'filterValue' => $rarityKey,
                            'sortValue' => $raritySort,
                        ],
                    ],
                    'item_type' => [
                        'type' => 'text',
                        'value' => $itemTypeName,
                        'params' => [
                            'filterValue' => $itemTypeId ? (string) $itemTypeId : '',
                            'sortValue' => $itemTypeName,
                            'searchValue' => $itemTypeName,
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
                        'price' => $it->price,
                        'rarity' => $it->rarity,
                        'dofus_version' => $it->dofus_version,
                        'usable' => (int) ($it->usable ?? 0),
                        'is_visible' => $it->is_visible,
                        'image' => $it->image,
                        'auto_update' => (bool) $it->auto_update,
                        'item_type_id' => $it->item_type_id,
                        'itemType' => $it->itemType ? [
                            'id' => $it->itemType->id,
                            'name' => $it->itemType->name,
                        ] : null,
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
                ],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
            ],
            'rows' => $tableRows,
        ]);
    }
}


