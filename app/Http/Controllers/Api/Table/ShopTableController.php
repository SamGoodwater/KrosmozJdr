<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ShopTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les boutiques.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class ShopTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Shop::class);

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Shop::query()
            ->with(['createdBy', 'npc.creature'])
            ->withCount('items');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('npc.creature', fn ($qq) => $qq->where('name', 'like', "%{$search}%"));
            });
        }

        $allowedSort = ['id', 'name', 'items_count', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Shop::class),
            'createAny' => Gate::allows('createAny', Shop::class),
            'updateAny' => Gate::allows('updateAny', Shop::class),
            'deleteAny' => Gate::allows('deleteAny', Shop::class),
            'manageAny' => Gate::allows('manageAny', Shop::class),
        ];

        $tableRows = $rows->map(function (Shop $s) {
            $showHref = route('entities.shops.show', $s->id);
            $createdBy = $s->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $s->created_at ? $s->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $s->created_at ? $s->created_at->getTimestamp() : 0;
            $updatedAtLabel = $s->updated_at ? $s->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $s->updated_at ? $s->updated_at->getTimestamp() : 0;

            $npcName = $s->npc?->creature?->name ?? '-';

            return [
                'id' => $s->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $s->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $s->name,
                            'sortValue' => (string) $s->name,
                        ],
                    ],
                    'location' => [
                        'type' => 'text',
                        'value' => (string) ($s->location ?? '-'),
                        'params' => [
                            'searchValue' => (string) ($s->location ?? ''),
                        ],
                    ],
                    'npc_name' => [
                        'type' => 'text',
                        'value' => $npcName,
                        'params' => [
                            'searchValue' => $npcName,
                            'sortValue' => $npcName,
                        ],
                    ],
                    'items_count' => [
                        'type' => 'text',
                        'value' => (string) ((int) ($s->items_count ?? 0)),
                        'params' => [
                            'sortValue' => (int) ($s->items_count ?? 0),
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
                    'entity' => $s->toArray() + [
                        'npc' => $s->npc ? ($s->npc->toArray() + [
                            'creature' => $s->npc->creature ? [
                                'id' => $s->npc->creature->id,
                                'name' => $s->npc->creature->name,
                            ] : null,
                        ]) : null,
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
                'entityType' => 'shops',
                'query' => [
                    'search' => $search,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => [],
            ],
            'rows' => $tableRows,
        ]);
    }
}


