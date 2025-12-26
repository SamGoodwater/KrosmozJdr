<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Npc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * NpcTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les PNJ.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class NpcTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Npc::class);

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Npc::query()->with(['creature', 'classe', 'specialization']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('creature', fn ($qq) => $qq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('classe', fn ($qq) => $qq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('specialization', fn ($qq) => $qq->where('name', 'like', "%{$search}%"));
            });
        }

        $allowedSort = ['id', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Npc::class),
            'createAny' => Gate::allows('createAny', Npc::class),
            'updateAny' => Gate::allows('updateAny', Npc::class),
            'deleteAny' => Gate::allows('deleteAny', Npc::class),
            'manageAny' => Gate::allows('manageAny', Npc::class),
        ];

        $tableRows = $rows->map(function (Npc $n) {
            $showHref = route('entities.npcs.show', $n->id);
            $creatureName = $n->creature?->name ?? '-';
            $classeName = $n->classe?->name ?? '-';
            $specName = $n->specialization?->name ?? '-';

            $createdAtLabel = $n->created_at ? $n->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $n->created_at ? $n->created_at->getTimestamp() : 0;
            $updatedAtLabel = $n->updated_at ? $n->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $n->updated_at ? $n->updated_at->getTimestamp() : 0;

            return [
                'id' => $n->id,
                'cells' => [
                    'creature_name' => [
                        'type' => 'route',
                        'value' => $creatureName,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => $creatureName,
                            'sortValue' => $creatureName,
                        ],
                    ],
                    'classe' => [
                        'type' => 'text',
                        'value' => $classeName,
                        'params' => [
                            'searchValue' => $classeName,
                            'sortValue' => $classeName,
                        ],
                    ],
                    'specialization' => [
                        'type' => 'text',
                        'value' => $specName,
                        'params' => [
                            'searchValue' => $specName,
                            'sortValue' => $specName,
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
                        'id' => $n->id,
                        'creature_id' => $n->creature_id,
                        'story' => $n->story,
                        'historical' => $n->historical,
                        'age' => $n->age,
                        'size' => $n->size,
                        'classe_id' => $n->classe_id,
                        'specialization_id' => $n->specialization_id,
                        'creature' => $n->creature ? [
                            'id' => $n->creature->id,
                            'name' => $n->creature->name,
                        ] : null,
                        'classe' => $n->classe ? [
                            'id' => $n->classe->id,
                            'name' => $n->classe->name,
                        ] : null,
                        'specialization' => $n->specialization ? [
                            'id' => $n->specialization->id,
                            'name' => $n->specialization->name,
                        ] : null,
                    ],
                ],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'npcs',
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


