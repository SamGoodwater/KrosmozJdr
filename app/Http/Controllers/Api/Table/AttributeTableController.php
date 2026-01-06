<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * AttributeTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les attributs.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class AttributeTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Attribute::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['usable', 'is_visible'] as $k) {
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

        $query = Attribute::query()->with(['createdBy']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('usable', $filters) && $filters['usable'] !== '' && $filters['usable'] !== null) {
            $query->where('usable', (int) $filters['usable']);
        }
        if (array_key_exists('is_visible', $filters) && $filters['is_visible'] !== '' && $filters['is_visible'] !== null) {
            $query->where('is_visible', (string) $filters['is_visible']);
        }

        $allowedSort = ['id', 'name', 'usable', 'is_visible', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Attribute::class),
            'createAny' => Gate::allows('createAny', Attribute::class),
            'updateAny' => Gate::allows('updateAny', Attribute::class),
            'deleteAny' => Gate::allows('deleteAny', Attribute::class),
            'manageAny' => Gate::allows('manageAny', Attribute::class),
        ];

        $filterOptions = [
            'usable' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'is_visible' => [
                ['value' => 'guest', 'label' => 'Invité'],
                ['value' => 'user', 'label' => 'Utilisateur'],
                ['value' => 'player', 'label' => 'Joueur'],
                ['value' => 'game_master', 'label' => 'Maître de jeu'],
            ],
        ];

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Attribute $a) {
                $createdBy = $a->createdBy;
                return [
                    'id' => $a->id,
                    'name' => $a->name,
                    'description' => $a->description,
                    'usable' => (int) ($a->usable ?? 0),
                    'is_visible' => $a->is_visible,
                    'image' => $a->image,
                    'created_by' => $a->created_by,
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $a->created_at?->toISOString(),
                    'updated_at' => $a->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'attributes',
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

        $tableRows = $rows->map(function (Attribute $a) {
            $showHref = route('entities.attributes.show', $a->id);
            $createdBy = $a->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $usableLabel = ((int) ($a->usable ?? 0)) === 1 ? 'Oui' : 'Non';

            $createdAtLabel = $a->created_at ? $a->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $a->created_at ? $a->created_at->getTimestamp() : 0;
            $updatedAtLabel = $a->updated_at ? $a->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $a->updated_at ? $a->updated_at->getTimestamp() : 0;

            return [
                'id' => $a->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $a->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $a->name,
                            'sortValue' => (string) $a->name,
                        ],
                    ],
                    'description' => [
                        'type' => 'text',
                        'value' => (string) ($a->description ?? '-'),
                        'params' => [
                            'searchValue' => (string) ($a->description ?? ''),
                            'sortValue' => (string) ($a->description ?? ''),
                        ],
                    ],
                    'usable' => [
                        'type' => 'badge',
                        'value' => $usableLabel,
                        'params' => [
                            'color' => ((int) ($a->usable ?? 0)) === 1 ? 'success' : 'base',
                            'filterValue' => (string) ((int) ($a->usable ?? 0)),
                            'sortValue' => (int) ($a->usable ?? 0),
                        ],
                    ],
                    'is_visible' => [
                        'type' => 'badge',
                        'value' => (string) ($a->is_visible ?? '-'),
                        'params' => [
                            'color' => 'info',
                            'filterValue' => (string) ($a->is_visible ?? ''),
                            'sortValue' => (string) ($a->is_visible ?? ''),
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
                        'id' => $a->id,
                        'name' => $a->name,
                        'description' => $a->description,
                        'usable' => (int) ($a->usable ?? 0),
                        'is_visible' => $a->is_visible,
                        'image' => $a->image,
                        'created_by' => $a->created_by,
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
                'entityType' => 'attributes',
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


