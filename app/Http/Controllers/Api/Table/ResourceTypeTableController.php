<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Type\ResourceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ResourceTypeTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les types de ressources.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class ResourceTypeTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ResourceType::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        if (!array_key_exists('decision', $filters) && $request->has('decision')) {
            $filters['decision'] = $request->get('decision');
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = ResourceType::query()->withCount('resources');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('dofusdb_type_id', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('decision', $filters) && $filters['decision'] !== '' && $filters['decision'] !== null) {
            $decision = (string) $filters['decision'];
            if (in_array($decision, [ResourceType::DECISION_PENDING, ResourceType::DECISION_ALLOWED, ResourceType::DECISION_BLOCKED], true)) {
                $query->where('decision', $decision);
            }
        }

        $allowedSort = ['id', 'name', 'dofusdb_type_id', 'decision', 'seen_count', 'last_seen_at', 'resources_count', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', ResourceType::class),
            'createAny' => Gate::allows('createAny', ResourceType::class),
            'updateAny' => Gate::allows('updateAny', ResourceType::class),
            'deleteAny' => Gate::allows('deleteAny', ResourceType::class),
            'manageAny' => Gate::allows('manageAny', ResourceType::class),
        ];

        $decisionLabel = fn (string $decision) => $decision === ResourceType::DECISION_ALLOWED
            ? 'Utilisé'
            : ($decision === ResourceType::DECISION_BLOCKED ? 'Non utilisé' : 'En attente');

        $decisionColor = fn (string $decision) => $decision === ResourceType::DECISION_ALLOWED
            ? 'green-700'
            : ($decision === ResourceType::DECISION_BLOCKED ? 'red-700' : 'gray-700');

        $filterOptions = [
            'decision' => [
                ['value' => ResourceType::DECISION_PENDING, 'label' => 'En attente'],
                ['value' => ResourceType::DECISION_ALLOWED, 'label' => 'Utilisé'],
                ['value' => ResourceType::DECISION_BLOCKED, 'label' => 'Non utilisé'],
            ],
        ];

        // Option B: renvoyer des entités brutes (le front génère `cells`).
        if ($format === 'entities') {
            $entities = $rows->map(function (ResourceType $rt) {
                $decision = (string) ($rt->decision ?? ResourceType::DECISION_PENDING);
                $lastSeen = $rt->last_seen_at;

                return [
                    'id' => $rt->id,
                    'name' => (string) $rt->name,
                    'dofusdb_type_id' => $rt->dofusdb_type_id,
                    'decision' => $decision,
                    'seen_count' => (int) ($rt->seen_count ?? 0),
                    'last_seen_at' => $lastSeen?->toISOString(),
                    'resources_count' => (int) ($rt->resources_count ?? 0),
                    'created_at' => $rt->created_at?->toISOString(),
                    'updated_at' => $rt->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'resource-types',
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

        $tableRows = $rows->map(function (ResourceType $rt) use ($decisionLabel, $decisionColor) {
            $showHref = route('entities.resource-types.show', $rt->id);

            $lastSeen = $rt->last_seen_at;
            $lastSeenValue = $lastSeen ? $lastSeen->toDateTimeString() : '-';
            $lastSeenSort = $lastSeen ? $lastSeen->timestamp : 0;

            $decision = (string) ($rt->decision ?? ResourceType::DECISION_PENDING);

            $createdAtLabel = $rt->created_at ? $rt->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $rt->created_at ? $rt->created_at->getTimestamp() : 0;
            $updatedAtLabel = $rt->updated_at ? $rt->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $rt->updated_at ? $rt->updated_at->getTimestamp() : 0;

            return [
                'id' => $rt->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $rt->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $rt->name,
                            'sortValue' => (string) $rt->name,
                        ],
                    ],
                    'dofusdb_type_id' => [
                        'type' => 'text',
                        'value' => $rt->dofusdb_type_id ?? '-',
                        'params' => [
                            'searchValue' => (string) ($rt->dofusdb_type_id ?? ''),
                            'sortValue' => (int) ($rt->dofusdb_type_id ?? 0),
                        ],
                    ],
                    'decision' => [
                        'type' => 'badge',
                        'value' => $decisionLabel($decision),
                        'params' => [
                            'color' => $decisionColor($decision),
                            'filterValue' => $decision,
                            'sortValue' => $decision,
                        ],
                    ],
                    'seen_count' => [
                        'type' => 'text',
                        'value' => (string) ((int) ($rt->seen_count ?? 0)),
                        'params' => [
                            'sortValue' => (int) ($rt->seen_count ?? 0),
                        ],
                    ],
                    'last_seen_at' => [
                        'type' => 'text',
                        'value' => $lastSeenValue,
                        'params' => [
                            'sortValue' => $lastSeenSort,
                        ],
                    ],
                    'resources_count' => [
                        'type' => 'text',
                        'value' => (string) ((int) ($rt->resources_count ?? 0)),
                        'params' => [
                            'sortValue' => (int) ($rt->resources_count ?? 0),
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
                        'id' => $rt->id,
                        'name' => $rt->name,
                        'dofusdb_type_id' => $rt->dofusdb_type_id,
                        'decision' => $rt->decision,
                        'usable' => (int) ($rt->usable ?? 0),
                        'is_visible' => (string) ($rt->is_visible ?? 'guest'),
                        'seen_count' => (int) ($rt->seen_count ?? 0),
                        'last_seen_at' => $rt->last_seen_at?->toISOString(),
                        'resources_count' => (int) ($rt->resources_count ?? 0),
                    ],
                ],
            ];
        })->values()->all();

        $filterOptions = [
            'decision' => [
                ['value' => 'pending', 'label' => 'En attente'],
                ['value' => 'allowed', 'label' => 'Utilisé'],
                ['value' => 'blocked', 'label' => 'Non utilisé'],
            ],
        ];

        return response()->json([
            'meta' => [
                'entityType' => 'resource-types',
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


