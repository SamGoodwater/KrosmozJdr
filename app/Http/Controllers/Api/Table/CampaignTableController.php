<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Campaign;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * CampaignTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les campagnes.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class CampaignTableController extends Controller
{
    private const STATE_LABELS = [
        Campaign::STATE_RAW => 'Brut',
        Campaign::STATE_DRAFT => 'Brouillon',
        Campaign::STATE_PLAYABLE => 'Jouable',
        Campaign::STATE_ARCHIVED => 'Archivé',
    ];

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Campaign::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['state', 'progress_state', 'is_public', 'read_level', 'write_level'] as $k) {
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

        $query = Campaign::query()->with(['createdBy']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('state', $filters) && $filters['state'] !== '' && $filters['state'] !== null) {
            $query->where('state', (string) $filters['state']);
        }
        if (array_key_exists('progress_state', $filters) && $filters['progress_state'] !== '' && $filters['progress_state'] !== null) {
            $query->where('progress_state', (int) $filters['progress_state']);
        }
        if (array_key_exists('is_public', $filters) && $filters['is_public'] !== '' && $filters['is_public'] !== null) {
            $query->where('is_public', (int) $filters['is_public']);
        }
        if (array_key_exists('read_level', $filters) && $filters['read_level'] !== '' && $filters['read_level'] !== null) {
            $query->where('read_level', (int) $filters['read_level']);
        }
        if (array_key_exists('write_level', $filters) && $filters['write_level'] !== '' && $filters['write_level'] !== null) {
            $query->where('write_level', (int) $filters['write_level']);
        }

        $allowedSort = ['id', 'name', 'slug', 'progress_state', 'state', 'read_level', 'write_level', 'is_public', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Campaign::class),
            'createAny' => Gate::allows('createAny', Campaign::class),
            'updateAny' => Gate::allows('updateAny', Campaign::class),
            'deleteAny' => Gate::allows('deleteAny', Campaign::class),
            'manageAny' => Gate::allows('manageAny', Campaign::class),
        ];

        $filterOptions = [
            'state' => collect(self::STATE_LABELS)
                ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
                ->values()
                ->all(),
            'progress_state' => collect(Campaign::PROGRESS_STATES)
                ->map(fn (string $label, int $value) => ['value' => (string) $value, 'label' => $label])
                ->values()
                ->all(),
            'read_level' => collect(User::ROLES)
                ->map(fn (string $label, int $value) => ['value' => (string) $value, 'label' => $label])
                ->values()
                ->all(),
            'write_level' => collect(User::ROLES)
                ->map(fn (string $label, int $value) => ['value' => (string) $value, 'label' => $label])
                ->values()
                ->all(),
            'is_public' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
        ];

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Campaign $c) {
                $createdBy = $c->createdBy;
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'description' => $c->description,
                    'slug' => $c->slug,
                    'keyword' => $c->keyword,
                    'is_public' => (int) ($c->is_public ?? 0),
                    'progress_state' => (int) ($c->progress_state ?? 0),
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
                    'entityType' => 'campaigns',
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

        $tableRows = $rows->map(function (Campaign $c) {
            $showHref = route('entities.campaigns.show', $c->id);

            $createdBy = $c->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $c->created_at ? $c->created_at->getTimestamp() : 0;
            $updatedAtLabel = $c->updated_at ? $c->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $c->updated_at ? $c->updated_at->getTimestamp() : 0;

            $stateLabel = self::STATE_LABELS[(string) ($c->state ?? '')] ?? (string) ($c->state ?? '');
            $isPublicLabel = ((int) ($c->is_public ?? 0)) === 1 ? 'Oui' : 'Non';

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
                    'slug' => [
                        'type' => 'text',
                        'value' => $c->slug ?: '-',
                        'params' => [
                            'searchValue' => (string) ($c->slug ?? ''),
                            'sortValue' => (string) ($c->slug ?? ''),
                        ],
                    ],
                    'state' => [
                        'type' => 'badge',
                        'value' => $stateLabel,
                        'params' => [
                            'color' => 'primary',
                            'filterValue' => (string) ($c->state ?? ''),
                            'sortValue' => (string) ($c->state ?? ''),
                        ],
                    ],
                    'is_public' => [
                        'type' => 'badge',
                        'value' => $isPublicLabel,
                        'params' => [
                            'color' => ((int) ($c->is_public ?? 0)) === 1 ? 'success' : 'base',
                            'filterValue' => (string) ((int) ($c->is_public ?? 0)),
                            'sortValue' => (int) ($c->is_public ?? 0),
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
                        'slug' => $c->slug,
                        'keyword' => $c->keyword,
                        'is_public' => (int) ($c->is_public ?? 0),
                        'progress_state' => (int) ($c->progress_state ?? 0),
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
                'entityType' => 'campaigns',
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


