<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Type\ConsumableType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ConsumableTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les consommables.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class ConsumableTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Consumable::class);

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

        $query = Consumable::query()->with(['createdBy', 'consumableType']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $allowedSort = ['id', 'name', 'level', 'rarity', 'dofusdb_id', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Consumable::class),
            'createAny' => Gate::allows('createAny', Consumable::class),
            'updateAny' => Gate::allows('updateAny', Consumable::class),
            'deleteAny' => Gate::allows('deleteAny', Consumable::class),
            'manageAny' => Gate::allows('manageAny', Consumable::class),
        ];

        $rarityColor = fn (int $r) => match ($r) {
            0 => 'success',
            1 => 'info',
            2 => 'primary',
            3 => 'warning',
            4 => 'error',
            5 => 'neutral',
            default => 'primary',
        };

        $consumableTypeOptions = ConsumableType::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(fn (ConsumableType $t) => [
                'value' => (string) $t->id,
                'label' => (string) $t->name,
            ])
            ->values()
            ->all();

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Consumable $c) {
                $createdBy = $c->createdBy;
                return $c->toArray() + [
                    'consumableType' => $c->consumableType ? [
                        'id' => $c->consumableType->id,
                        'name' => $c->consumableType->name,
                    ] : null,
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
                    'entityType' => 'consumables',
                    'query' => [
                        'search' => $search,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $limit,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => [
                        'rarity' => collect(Resource::RARITY)->map(fn ($label, $value) => [
                            'value' => (string) $value,
                            'label' => (string) $label,
                        ])->values()->all(),
                        'consumable_type_id' => $consumableTypeOptions,
                    ],
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Consumable $c) use ($rarityColor) {
            $showHref = route('entities.consumables.show', $c->id);
            $createdBy = $c->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $c->created_at ? $c->created_at->getTimestamp() : 0;
            $updatedAtLabel = $c->updated_at ? $c->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $c->updated_at ? $c->updated_at->getTimestamp() : 0;

            $typeName = $c->consumableType?->name ?? '-';
            $typeId = $c->consumable_type_id;
            $rarityRaw = $c->rarity;
            $rarityInt = is_numeric((string) $rarityRaw) ? (int) $rarityRaw : null;
            $rarityLabel = $rarityInt === null ? '-' : (Resource::RARITY[$rarityInt] ?? (string) $rarityInt);

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
                            'searchValue' => (string) ($c->level ?? ''),
                        ],
                    ],
                    'rarity' => [
                        'type' => 'badge',
                        'value' => $rarityLabel,
                        'params' => [
                            'color' => $rarityInt === null ? 'base' : $rarityColor($rarityInt),
                            'filterValue' => $rarityInt === null ? '' : (string) $rarityInt,
                            'sortValue' => $rarityInt === null ? -1 : $rarityInt,
                        ],
                    ],
                    'consumable_type' => [
                        'type' => 'text',
                        'value' => $typeName,
                        'params' => [
                            'filterValue' => $typeId ? (string) $typeId : '',
                            'sortValue' => $typeName,
                            'searchValue' => $typeName,
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
                        'consumableType' => $c->consumableType ? [
                            'id' => $c->consumableType->id,
                            'name' => $c->consumableType->name,
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

        $consumableTypeOptions = ConsumableType::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(fn (ConsumableType $t) => [
                'value' => (string) $t->id,
                'label' => (string) $t->name,
            ])
            ->values()
            ->all();

        return response()->json([
            'meta' => [
                'entityType' => 'consumables',
                'query' => [
                    'search' => $search,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => [
                    'rarity' => collect(Resource::RARITY)->map(fn ($label, $value) => [
                        'value' => (string) $value,
                        'label' => (string) $label,
                    ])->values()->all(),
                    'consumable_type_id' => $consumableTypeOptions,
                ],
            ],
            'rows' => $tableRows,
        ]);
    }
}


