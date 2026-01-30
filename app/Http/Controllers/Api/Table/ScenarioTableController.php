<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Scenario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ScenarioTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les scénarios.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class ScenarioTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Scenario::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['state', 'is_public'] as $k) {
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

        $query = Scenario::query()->with(['createdBy']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('state', $filters) && $filters['state'] !== '' && $filters['state'] !== null) {
            $query->where('state', (int) $filters['state']);
        }
        if (array_key_exists('is_public', $filters) && $filters['is_public'] !== '' && $filters['is_public'] !== null) {
            $query->where('is_public', (int) $filters['is_public']);
        }

        $allowedSort = ['id', 'name', 'slug', 'state', 'is_public', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Scenario::class),
            'createAny' => Gate::allows('createAny', Scenario::class),
            'updateAny' => Gate::allows('updateAny', Scenario::class),
            'deleteAny' => Gate::allows('deleteAny', Scenario::class),
            'manageAny' => Gate::allows('manageAny', Scenario::class),
        ];

        // filterOptions (utilisés côté client par TanStackTableFilters).
        $stateOptions = Scenario::query()
            ->whereNotNull('state')
            ->select('state')
            ->distinct()
            ->orderBy('state')
            ->limit(50)
            ->pluck('state')
            ->map(fn ($v) => [
                'value' => (string) $v,
                'label' => is_numeric((string) $v) ? "État {$v}" : (string) $v,
            ])
            ->values()
            ->all();

        $filterOptions = [
            'state' => $stateOptions,
            'is_public' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
        ];

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Scenario $s) {
                $createdBy = $s->createdBy;
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'description' => $s->description,
                    'slug' => $s->slug,
                    'keyword' => $s->keyword,
                    'is_public' => (bool) ($s->is_public ?? false),
                    'progress_state' => (int) ($s->progress_state ?? 0),
                    'state' => (string) ($s->state ?? 'draft'),
                    'read_level' => (int) ($s->read_level ?? 0),
                    'write_level' => (int) ($s->write_level ?? 0),
                    'image' => $s->image,
                    'created_by' => $s->created_by,
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $s->created_at?->toISOString(),
                    'updated_at' => $s->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'scenarios',
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

        $tableRows = $rows->map(function (Scenario $s) {
            $showHref = route('entities.scenarios.show', $s->id);

            $createdBy = $s->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $s->created_at ? $s->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $s->created_at ? $s->created_at->getTimestamp() : 0;
            $updatedAtLabel = $s->updated_at ? $s->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $s->updated_at ? $s->updated_at->getTimestamp() : 0;

            $stateLabel = is_numeric((string) $s->state) ? (string) $s->state : ((string) ($s->state ?? '-'));
            $isPublicLabel = ((int) ($s->is_public ?? 0)) === 1 ? 'Oui' : 'Non';

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
                    'slug' => [
                        'type' => 'text',
                        'value' => $s->slug ?: '-',
                        'params' => [
                            'searchValue' => (string) ($s->slug ?? ''),
                            'sortValue' => (string) ($s->slug ?? ''),
                        ],
                    ],
                    'state' => [
                        'type' => 'badge',
                        'value' => $stateLabel,
                        'params' => [
                            'color' => 'primary',
                            'filterValue' => (string) ($s->state ?? ''),
                            'sortValue' => (int) ($s->state ?? 0),
                        ],
                    ],
                    'is_public' => [
                        'type' => 'badge',
                        'value' => $isPublicLabel,
                        'params' => [
                            'color' => ((int) ($s->is_public ?? 0)) === 1 ? 'success' : 'base',
                            'filterValue' => (string) ((int) ($s->is_public ?? 0)),
                            'sortValue' => (int) ($s->is_public ?? 0),
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
                    'entity' => [
                        'id' => $s->id,
                        'name' => $s->name,
                        'description' => $s->description,
                        'slug' => $s->slug,
                        'keyword' => $s->keyword,
                        'is_public' => (int) ($s->is_public ?? 0),
                        'progress_state' => (int) ($s->progress_state ?? 0),
                        'state' => (string) ($s->state ?? 'draft'),
                        'read_level' => (int) ($s->read_level ?? 0),
                        'write_level' => (int) ($s->write_level ?? 0),
                        'image' => $s->image,
                        'created_by' => $s->created_by,
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
                'entityType' => 'scenarios',
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


