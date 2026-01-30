<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Capability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * CapabilityTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les capacités.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class CapabilityTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Capability::class);

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

        $query = Capability::query()->with(['createdBy']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('effect', 'like', "%{$search}%");
            });
        }

        $allowedSort = ['id', 'name', 'level', 'pa', 'po', 'element', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Capability::class),
            'createAny' => Gate::allows('createAny', Capability::class),
            'updateAny' => Gate::allows('updateAny', Capability::class),
            'deleteAny' => Gate::allows('deleteAny', Capability::class),
            'manageAny' => Gate::allows('manageAny', Capability::class),
        ];

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Capability $c) {
                $createdBy = $c->createdBy;
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'description' => $c->description,
                    'effect' => $c->effect,
                    'level' => $c->level,
                    'pa' => $c->pa,
                    'po' => $c->po,
                    'po_editable' => (bool) $c->po_editable,
                    'time_before_use_again' => $c->time_before_use_again,
                    'casting_time' => $c->casting_time,
                    'duration' => $c->duration,
                    'element' => $c->element,
                    'is_magic' => (bool) $c->is_magic,
                    'ritual_available' => (bool) $c->ritual_available,
                    'powerful' => $c->powerful,
                    'state' => (string) ($c->state ?? 'draft'),
                    'read_level' => (int) ($c->read_level ?? 0),
                    'write_level' => (int) ($c->write_level ?? 0),
                    'image' => $c->image,
                    'created_by' => $c->created_by,
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
                    'entityType' => 'capabilities',
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

        $tableRows = $rows->map(function (Capability $c) {
            $showHref = route('entities.capabilities.show', $c->id);
            $createdBy = $c->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $c->created_at ? $c->created_at->getTimestamp() : 0;
            $updatedAtLabel = $c->updated_at ? $c->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $c->updated_at ? $c->updated_at->getTimestamp() : 0;

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
                            'sortValue' => is_numeric((string) $c->level) ? (int) $c->level : (string) ($c->level ?? ''),
                            'searchValue' => (string) ($c->level ?? ''),
                        ],
                    ],
                    'pa' => [
                        'type' => 'text',
                        'value' => $c->pa ?: '-',
                        'params' => [
                            'sortValue' => is_numeric((string) $c->pa) ? (int) $c->pa : (string) ($c->pa ?? ''),
                        ],
                    ],
                    'po' => [
                        'type' => 'text',
                        'value' => $c->po ?: '-',
                        'params' => [
                            'sortValue' => (string) ($c->po ?? ''),
                        ],
                    ],
                    'element' => [
                        'type' => 'text',
                        'value' => $c->element ?: '-',
                        'params' => [
                            'sortValue' => (string) ($c->element ?? ''),
                            'searchValue' => (string) ($c->element ?? ''),
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
                        'id' => $c->id,
                        'name' => $c->name,
                        'description' => $c->description,
                        'effect' => $c->effect,
                        'level' => $c->level,
                        'pa' => $c->pa,
                        'po' => $c->po,
                        'po_editable' => (bool) $c->po_editable,
                        'time_before_use_again' => $c->time_before_use_again,
                        'casting_time' => $c->casting_time,
                        'duration' => $c->duration,
                        'element' => $c->element,
                        'is_magic' => (bool) $c->is_magic,
                        'ritual_available' => (bool) $c->ritual_available,
                        'powerful' => $c->powerful,
                        'state' => (string) ($c->state ?? 'draft'),
                        'read_level' => (int) ($c->read_level ?? 0),
                        'write_level' => (int) ($c->write_level ?? 0),
                        'image' => $c->image,
                        'created_by' => $c->created_by,
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
                'entityType' => 'capabilities',
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


