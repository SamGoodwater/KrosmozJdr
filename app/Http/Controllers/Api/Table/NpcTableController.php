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

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Npc::query()->with(['creature', 'breed', 'specialization']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('creature', fn ($qq) => $qq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('breed', fn ($qq) => $qq->where('name', 'like', "%{$search}%"))
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

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Npc $n) {
                return [
                    'id' => $n->id,
                    'creature_id' => $n->creature_id,
                    'story' => $n->story,
                    'historical' => $n->historical,
                    'age' => $n->age,
                    'size' => $n->size,
                    'breed_id' => $n->breed_id,
                    'specialization_id' => $n->specialization_id,
                    'creature' => $n->creature ? [
                        'id' => $n->creature->id,
                        'name' => $n->creature->name,
                    ] : null,
                    'breed' => $n->breed ? [
                        'id' => $n->breed->id,
                        'name' => $n->breed->name,
                    ] : null,
                    'specialization' => $n->specialization ? [
                        'id' => $n->specialization->id,
                        'name' => $n->specialization->name,
                    ] : null,
                    'created_at' => $n->created_at?->toISOString(),
                    'updated_at' => $n->updated_at?->toISOString(),
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
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Npc $n) {
            $showHref = route('entities.npcs.show', $n->id);
            $creatureName = $n->creature?->name ?? '-';
            $breedName = $n->breed?->name ?? '-';
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
                    'breed' => [
                        'type' => 'text',
                        'value' => $breedName,
                        'params' => [
                            'searchValue' => $breedName,
                            'sortValue' => $breedName,
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
                        'breed_id' => $n->breed_id,
                        'specialization_id' => $n->specialization_id,
                        'creature' => $n->creature ? [
                            'id' => $n->creature->id,
                            'name' => $n->creature->name,
                        ] : null,
                        'breed' => $n->breed ? [
                            'id' => $n->breed->id,
                            'name' => $n->breed->name,
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


