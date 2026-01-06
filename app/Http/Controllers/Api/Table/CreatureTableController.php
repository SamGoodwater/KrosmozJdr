<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * CreatureTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les créatures.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class CreatureTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Creature::class);

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

        $query = Creature::query()->with(['createdBy']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $allowedSort = ['id', 'name', 'level', 'hostility', 'life', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Creature::class),
            'createAny' => Gate::allows('createAny', Creature::class),
            'updateAny' => Gate::allows('updateAny', Creature::class),
            'deleteAny' => Gate::allows('deleteAny', Creature::class),
            'manageAny' => Gate::allows('manageAny', Creature::class),
        ];

        $hostilityColor = fn (int $h) => match ($h) {
            0 => 'success', // Amical
            1 => 'info',    // Curieux
            2 => 'base',    // Neutre
            3 => 'warning', // Hostile
            4 => 'error',   // Aggressif
            default => 'primary',
        };

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Creature $c) {
                $createdBy = $c->createdBy;
                return $c->toArray() + [
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
                    'entityType' => 'creatures',
                    'query' => [
                        'search' => $search,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $limit,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => [
                        'hostility' => collect(Monster::HOSTILITY)->map(fn ($label, $value) => [
                            'value' => (string) $value,
                            'label' => (string) $label,
                        ])->values()->all(),
                    ],
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Creature $c) use ($hostilityColor) {
            $showHref = route('entities.creatures.show', $c->id);
            $createdBy = $c->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');
            $hostilityRaw = $c->hostility;
            $hostilityInt = is_numeric((string) $hostilityRaw) ? (int) $hostilityRaw : null;

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
                            'filterValue' => $c->level ?: '',
                            'sortValue' => is_numeric((string) $c->level) ? (int) $c->level : (string) ($c->level ?? ''),
                        ],
                    ],
                    'hostility' => [
                        'type' => 'badge',
                        'value' => $hostilityInt === null
                            ? '-'
                            : (Monster::HOSTILITY[$hostilityInt] ?? (string) $hostilityInt),
                        'params' => [
                            'color' => $hostilityInt === null ? 'base' : $hostilityColor($hostilityInt),
                            'filterValue' => $hostilityInt === null ? '' : (string) $hostilityInt,
                            'sortValue' => $hostilityInt === null ? -1 : $hostilityInt,
                        ],
                    ],
                    'life' => [
                        'type' => 'text',
                        'value' => $c->life ?: '-',
                        'params' => [
                            'sortValue' => is_numeric((string) $c->life) ? (int) $c->life : (string) ($c->life ?? ''),
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
                'entityType' => 'creatures',
                'query' => [
                    'search' => $search,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => [
                    'hostility' => collect(Monster::HOSTILITY)->map(fn ($label, $value) => [
                        'value' => (string) $value,
                        'label' => (string) $label,
                    ])->values()->all(),
                ],
            ],
            'rows' => $tableRows,
        ]);
    }
}


