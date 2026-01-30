<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Resource;
use App\Models\Type\ResourceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ResourceTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les Ressources.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 *
 * Hybride client-first:
 * - cet endpoint peut servir à charger un dataset "base"
 * - ou à charger un sous-ensemble via une URL paramétrée (Option A)
 *
 * @example
 * GET /api/tables/resources?limit=5000
 * GET /api/tables/resources?filters[rarity]=2&filters[resource_type_id]=12
 */
class ResourceTableController extends Controller
{
    private const STATE_COLORS = [
        'raw' => 'neutral',
        'draft' => 'warning',
        'playable' => 'success',
        'archived' => 'error',
    ];

    private const LEVEL_OPTIONS = [
        ['value' => '0', 'label' => 'Invité'],
        ['value' => '1', 'label' => 'Utilisateur'],
        ['value' => '2', 'label' => 'Joueur'],
        ['value' => '3', 'label' => 'Maître de jeu'],
        ['value' => '4', 'label' => 'Admin'],
        ['value' => '5', 'label' => 'Super admin'],
    ];

    private function stateColor(?string $state): string
    {
        $s = (string) ($state ?? '');
        return self::STATE_COLORS[$s] ?? 'base';
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Resource::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);

        // Compat: accepter des filtres "flat" (rarity=2) en plus de filters[rarity]=2
        foreach (['level', 'resource_type_id', 'rarity', 'auto_update', 'state', 'read_level', 'write_level'] as $k) {
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

        $query = Resource::query()->with(['createdBy', 'resourceType']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtres (select)
        if (array_key_exists('level', $filters) && $filters['level'] !== '' && $filters['level'] !== null) {
            $query->where('level', (string) $filters['level']);
        }
        if (array_key_exists('resource_type_id', $filters) && $filters['resource_type_id'] !== '' && $filters['resource_type_id'] !== null) {
            $query->where('resource_type_id', (int) $filters['resource_type_id']);
        }
        foreach (['rarity', 'auto_update'] as $k) {
            if (array_key_exists($k, $filters) && $filters[$k] !== '' && $filters[$k] !== null) {
                $query->where($k, (int) $filters[$k]);
            }
        }
        if (array_key_exists('state', $filters) && $filters['state'] !== '' && $filters['state'] !== null) {
            $query->where('state', (string) $filters['state']);
        }
        if (array_key_exists('read_level', $filters) && $filters['read_level'] !== '' && $filters['read_level'] !== null) {
            $query->where('read_level', (int) $filters['read_level']);
        }
        if (array_key_exists('write_level', $filters) && $filters['write_level'] !== '' && $filters['write_level'] !== null) {
            $query->where('write_level', (int) $filters['write_level']);
        }

        // Tri (liste blanche)
        $allowedSort = ['id', 'name', 'level', 'rarity', 'price', 'weight', 'state', 'read_level', 'write_level', 'auto_update', 'dofusdb_id', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Resource::class),
            'createAny' => Gate::allows('createAny', Resource::class),
            'updateAny' => Gate::allows('updateAny', Resource::class),
            'deleteAny' => Gate::allows('deleteAny', Resource::class),
            'manageAny' => Gate::allows('manageAny', Resource::class),
        ];

        $resourceTypes = ResourceType::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->limit(5000)
            ->get()
            ->map(fn ($t) => ['value' => (string) $t->id, 'label' => (string) $t->name])
            ->values()
            ->all();

        $filterOptions = [
            'resource_type_id' => $resourceTypes,
            'rarity' => collect(Resource::RARITY)
                ->map(fn ($label, $value) => ['value' => (string) $value, 'label' => (string) $label])
                ->values()
                ->all(),
            'auto_update' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'state' => [
                ['value' => 'raw', 'label' => 'Brut'],
                ['value' => 'draft', 'label' => 'Brouillon'],
                ['value' => 'playable', 'label' => 'Jouable'],
                ['value' => 'archived', 'label' => 'Archivé'],
            ],
            'read_level' => self::LEVEL_OPTIONS,
            'write_level' => self::LEVEL_OPTIONS,
            'level' => [
                ['value' => '1', 'label' => '1'],
                ['value' => '50', 'label' => '50'],
                ['value' => '100', 'label' => '100'],
                ['value' => '150', 'label' => '150'],
                ['value' => '200', 'label' => '200'],
            ],
        ];

        // Option B: renvoyer des entités brutes (le front génère `cells`).
        if ($format === 'entities') {
            $entities = $rows->map(function (Resource $r) {
                $createdBy = $r->createdBy;
                $resourceType = $r->resourceType;

                return [
                    'id' => $r->id,
                    'dofusdb_id' => $r->dofusdb_id,
                    'official_id' => $r->official_id,
                    'name' => $r->name,
                    'description' => $r->description,
                    'level' => $r->level,
                    'price' => $r->price,
                    'weight' => $r->weight,
                    'rarity' => $r->rarity,
                    'dofus_version' => $r->dofus_version,
                    'state' => (string) ($r->state ?? 'draft'),
                    'read_level' => (int) ($r->read_level ?? 0),
                    'write_level' => (int) ($r->write_level ?? 0),
                    'image' => $r->image,
                    'auto_update' => (bool) $r->auto_update,
                    'resource_type_id' => $r->resource_type_id,
                    'resourceType' => $resourceType ? [
                        'id' => $resourceType->id,
                        'name' => $resourceType->name,
                    ] : null,
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $r->created_at?->toISOString(),
                    'updated_at' => $r->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'resources',
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

        $rarityColor = fn (int $r) => match ($r) {
            0 => 'success',
            1 => 'info',
            2 => 'primary',
            3 => 'warning',
            4 => 'error',
            5 => 'neutral',
            default => 'primary',
        };

        $tableRows = $rows->map(function (Resource $r) use ($rarityColor) {
            $showHref = route('entities.resources.show', $r->id);
            $rarityLabel = Resource::RARITY[$r->rarity] ?? (string) $r->rarity;

            $resourceTypeId = $r->resource_type_id;
            $resourceTypeName = $r->resourceType?->name ?? '-';

            $dofusDbHref = $r->dofusdb_id ? "https://www.dofus.com/fr/mmorpg/encyclopedie/ressources/{$r->dofusdb_id}" : null;

            $createdBy = $r->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $r->created_at ? $r->created_at->getTimestamp() : 0;
            $updatedAtLabel = $r->updated_at ? $r->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $r->updated_at ? $r->updated_at->getTimestamp() : 0;
            $state = (string) ($r->state ?? 'draft');

            return [
                'id' => $r->id,
                'cells' => [
                    'image' => [
                        'type' => 'image',
                        'value' => $r->image,
                        'params' => [
                            'alt' => $r->name,
                            'searchValue' => $r->name,
                        ],
                    ],
                    'name' => [
                        'type' => 'route',
                        'value' => $r->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => $r->name,
                            'sortValue' => $r->name,
                        ],
                    ],
                    'level' => [
                        'type' => 'text',
                        'value' => $r->level ?: '-',
                        'params' => [
                            'filterValue' => $r->level ?: '',
                            'sortValue' => is_numeric((string) $r->level) ? (int) $r->level : (string) ($r->level ?? ''),
                            'searchValue' => (string) ($r->level ?? ''),
                        ],
                    ],
                    'resource_type' => [
                        'type' => 'text',
                        'value' => $resourceTypeName,
                        'params' => [
                            'filterValue' => $resourceTypeId ? (string) $resourceTypeId : '',
                            'sortValue' => $resourceTypeName,
                            'searchValue' => $resourceTypeName,
                        ],
                    ],
                    'rarity' => [
                        'type' => 'badge',
                        'value' => $rarityLabel,
                        'params' => [
                            'color' => $rarityColor((int) $r->rarity),
                            'filterValue' => (string) ((int) $r->rarity),
                            'sortValue' => (int) $r->rarity,
                        ],
                    ],
                    'price' => [
                        'type' => 'text',
                        'value' => $r->price ?: '-',
                        'params' => [
                            'sortValue' => is_numeric((string) $r->price) ? (float) $r->price : (string) ($r->price ?? ''),
                        ],
                    ],
                    'weight' => [
                        'type' => 'text',
                        'value' => $r->weight ?: '-',
                        'params' => [
                            'sortValue' => is_numeric((string) $r->weight) ? (float) $r->weight : (string) ($r->weight ?? ''),
                        ],
                    ],
                    'state' => [
                        'type' => 'badge',
                        'value' => $state,
                        'params' => [
                            'color' => $this->stateColor($state),
                            'filterValue' => $state,
                            'sortValue' => $state,
                        ],
                    ],
                    'auto_update' => [
                        'type' => 'badge',
                        'value' => $r->auto_update ? 'Oui' : 'Non',
                        'params' => [
                            'color' => $r->auto_update ? 'success' : 'error',
                            'filterValue' => $r->auto_update ? '1' : '0',
                            'sortValue' => $r->auto_update ? 1 : 0,
                        ],
                    ],
                    'dofusdb_id' => [
                        'type' => 'route',
                        'value' => $r->dofusdb_id ? (string) $r->dofusdb_id : '-',
                        'params' => [
                            'href' => $dofusDbHref,
                            'target' => '_blank',
                            'sortValue' => $r->dofusdb_id ?? 0,
                            'filterValue' => (string) ($r->dofusdb_id ?? ''),
                        ],
                    ],
                    'created_by' => [
                        'type' => 'text',
                        'value' => $createdByLabel,
                        'params' => [
                            'sortValue' => $createdByLabel,
                            'searchValue' => $createdByLabel,
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
                // Données brutes minimales (utile pour actions/bulk panel future)
                'rowParams' => [
                    'entity' => [
                        'id' => $r->id,
                        'name' => $r->name,
                        'description' => $r->description,
                        'level' => $r->level,
                        'price' => $r->price,
                        'weight' => $r->weight,
                        'rarity' => $r->rarity,
                        'dofus_version' => $r->dofus_version,
                        'state' => (string) ($r->state ?? 'draft'),
                        'read_level' => (int) ($r->read_level ?? 0),
                        'write_level' => (int) ($r->write_level ?? 0),
                        'image' => $r->image,
                        'auto_update' => (bool) $r->auto_update,
                        'resource_type_id' => $r->resource_type_id,
                        'resourceType' => $r->resourceType ? [
                            'id' => $r->resourceType->id,
                            'name' => $r->resourceType->name,
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

        return response()->json([
            'meta' => [
                'entityType' => 'resources',
                'query' => [
                    'search' => $search,
                    'filters' => $filters,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
                'format' => 'cells',
            ],
            'rows' => $tableRows,
        ]);
    }
}


