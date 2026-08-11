<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Condition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ConditionTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les états (référentiel Condition).
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class ConditionTableController extends Controller
{
    use InterpretsEntityTableSort;

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
        $this->authorize('viewAny', Condition::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['state', 'read_level', 'write_level', 'dissipable'] as $k) {
            if (! array_key_exists($k, $filters) && $request->has($k)) {
                $filters[$k] = $request->get($k);
            }
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

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

        $query = Condition::query()
            ->visibleToUser($request->user())->with(['createdBy']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
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
        if (array_key_exists('dissipable', $filters) && $filters['dissipable'] !== '' && $filters['dissipable'] !== null) {
            $rawD = $filters['dissipable'];
            $parts = is_array($rawD) ? $rawD : [$rawD];
            $bools = [];
            foreach ($parts as $p) {
                $b = $this->normalizeDissipableFilter($p);
                if ($b !== null) {
                    $bools[$b ? 't' : 'f'] = $b;
                }
            }
            if (count($bools) === 1) {
                $query->where('dissipable', (bool) reset($bools));
            }
        }

        $allowedSort = ['id', 'name', 'state', 'dissipable', 'read_level', 'write_level', 'created_at', 'updated_at'];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Condition::class),
            'createAny' => Gate::allows('createAny', Condition::class),
            'updateAny' => Gate::allows('updateAny', Condition::class),
            'deleteAny' => Gate::allows('deleteAny', Condition::class),
            'manageAny' => Gate::allows('manageAny', Condition::class),
        ];

        $filterOptions = [
            'state' => [
                ['value' => 'raw', 'label' => 'Brut'],
                ['value' => 'draft', 'label' => 'Brouillon'],
                ['value' => 'playable', 'label' => 'Jouable'],
                ['value' => 'archived', 'label' => 'Archivé'],
            ],
            'read_level' => self::LEVEL_OPTIONS,
            'write_level' => self::LEVEL_OPTIONS,
            'dissipable' => [
                ['value' => '1', 'label' => 'Dissipable'],
                ['value' => '0', 'label' => 'Non dissipable'],
            ],
        ];

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Condition $a) {
                $createdBy = $a->createdBy;

                return [
                    'id' => $a->id,
                    'name' => $a->name,
                    'description' => $a->description,
                    'state' => (string) ($a->state ?? 'draft'),
                    'read_level' => (int) ($a->read_level ?? 0),
                    'write_level' => (int) ($a->write_level ?? 0),
                    'dissipable' => (bool) ($a->dissipable ?? true),
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
                    'entityType' => 'conditions',
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

        $tableRows = $rows->map(function (Condition $a) {
            $showHref = route('entities.conditions.show', $a->id);
            $createdBy = $a->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $state = (string) ($a->state ?? 'draft');
            $dissipable = (bool) ($a->dissipable ?? true);
            $dissipableLabel = $dissipable ? 'Dissipable' : 'Non dissipable';
            $dissipableIcon = $dissipable ? 'icons/caracteristics/unenchantable.webp' : 'icons/caracteristics/notUnenchantable.webp';

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
                    'dissipable' => [
                        'type' => 'image',
                        'value' => $dissipableIcon,
                        'params' => [
                            'alt' => $dissipableLabel,
                            'filterValue' => $dissipable ? '1' : '0',
                            'sortValue' => $dissipable ? 1 : 0,
                            'searchValue' => $dissipableLabel,
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
                    'read_level' => [
                        'type' => 'badge',
                        'value' => (string) ((int) ($a->read_level ?? 0)),
                        'params' => [
                            'color' => 'info',
                            'filterValue' => (string) ((int) ($a->read_level ?? 0)),
                            'sortValue' => (int) ($a->read_level ?? 0),
                        ],
                    ],
                    'write_level' => [
                        'type' => 'badge',
                        'value' => (string) ((int) ($a->write_level ?? 0)),
                        'params' => [
                            'color' => 'info',
                            'filterValue' => (string) ((int) ($a->write_level ?? 0)),
                            'sortValue' => (int) ($a->write_level ?? 0),
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
                        'state' => (string) ($a->state ?? 'draft'),
                        'read_level' => (int) ($a->read_level ?? 0),
                        'write_level' => (int) ($a->write_level ?? 0),
                        'dissipable' => $dissipable,
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
                'entityType' => 'conditions',
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

    private function normalizeDissipableFilter(mixed $v): ?bool
    {
        if ($v === '' || $v === null) {
            return null;
        }
        if (is_bool($v)) {
            return $v;
        }
        $s = strtolower(trim((string) $v));
        if (in_array($s, ['1', 'true', 'yes', 'oui'], true)) {
            return true;
        }
        if (in_array($s, ['0', 'false', 'no', 'non'], true)) {
            return false;
        }

        return null;
    }
}
