<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Specialization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * SpecializationTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les spécialisations.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class SpecializationTableController extends Controller
{
    use InterpretsEntityTableSort;
    use PaginatesEntityTable;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Specialization::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

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

        $query = Specialization::query()
            ->visibleToUser($request->user())
            ->with([
                'createdBy',
                'capabilities' => fn ($q) => $q->orderBy('name'),
                'spells' => fn ($q) => $q->orderBy('name'),
            ])
            ->withCount(['capabilities', 'spells']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('description')) {
            $needle = (string) $request->get('description');
            $query->where(function ($q) use ($needle) {
                $q->where('short_description', 'like', "%{$needle}%")
                    ->orWhere('description', 'like', "%{$needle}%");
            });
        }

        if ($request->filled('capabilities')) {
            $needle = (string) $request->get('capabilities');
            $query->whereHas('capabilities', function ($q) use ($needle) {
                $q->where('name', 'like', "%{$needle}%");
            });
        }

        if ($request->filled('spells')) {
            $needle = (string) $request->get('spells');
            $query->whereHas('spells', function ($q) use ($needle) {
                $q->where('name', 'like', "%{$needle}%");
            });
        }

        $allowedSort = ['id', 'name', 'capabilities_count', 'spells_count', 'created_at', 'updated_at'];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $limit = $pageResult['limit'];
        $page = $pageResult['page'];
        $pagination = $pageResult['pagination'];

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Specialization::class),
            'createAny' => Gate::allows('createAny', Specialization::class),
            'updateAny' => Gate::allows('updateAny', Specialization::class),
            'deleteAny' => Gate::allows('deleteAny', Specialization::class),
            'manageAny' => Gate::allows('manageAny', Specialization::class),
        ];

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Specialization $s) {
                $createdBy = $s->createdBy;

                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'description' => $s->description,
                    'short_description' => $s->short_description,
                    'state' => (string) ($s->state ?? 'draft'),
                    'read_level' => (int) ($s->read_level ?? 0),
                    'write_level' => (int) ($s->write_level ?? 0),
                    'image' => $s->image,
                    'capabilities_count' => $s->capabilities_count ?? 0,
                    'spells_count' => $s->spells_count ?? 0,
                    'capabilities' => $s->capabilities->map(fn ($it) => ['id' => $it->id, 'name' => $it->name])->values()->all(),
                    'spells' => $s->spells->map(fn ($it) => ['id' => $it->id, 'name' => $it->name])->values()->all(),
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
                    'entityType' => 'specializations',
                    'query' => [
                        'search' => $search,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $limit,
                        'page' => $page,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => [],
                    'pagination' => $pagination,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Specialization $s) {
            $showHref = route('entities.specializations.show', $s->id);
            $createdBy = $s->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $s->created_at ? $s->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $s->created_at ? $s->created_at->getTimestamp() : 0;
            $updatedAtLabel = $s->updated_at ? $s->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $s->updated_at ? $s->updated_at->getTimestamp() : 0;

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
                    'description' => [
                        'type' => 'text',
                        'value' => (string) ($s->short_description ?? $s->description ?? '-'),
                        'params' => [
                            'searchValue' => trim((string) (($s->short_description ?? '').' '.($s->description ?? ''))),
                        ],
                    ],
                    'spells_count' => [
                        'type' => 'text',
                        'value' => (string) ((int) ($s->spells_count ?? 0)),
                        'params' => [
                            'sortValue' => (int) ($s->spells_count ?? 0),
                        ],
                    ],
                    'capabilities_count' => [
                        'type' => 'text',
                        'value' => (string) ((int) ($s->capabilities_count ?? 0)),
                        'params' => [
                            'sortValue' => (int) ($s->capabilities_count ?? 0),
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
                        'short_description' => $s->short_description,
                        'state' => (string) ($s->state ?? 'draft'),
                        'capabilities' => $s->capabilities->map(fn ($it) => ['id' => $it->id, 'name' => $it->name])->values()->all(),
                        'spells' => $s->spells->map(fn ($it) => ['id' => $it->id, 'name' => $it->name])->values()->all(),
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
                'entityType' => 'specializations',
                'query' => [
                    'search' => $search,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                    'page' => $page,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => [],
                'pagination' => $pagination,
            ],
            'rows' => $tableRows,
        ]);
    }
}
