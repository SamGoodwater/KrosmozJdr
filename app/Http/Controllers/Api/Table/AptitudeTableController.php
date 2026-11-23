<?php

namespace App\Http\Controllers\Api\Table;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
use App\Models\Entity\Aptitude;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AptitudeTableController extends Controller
{
    use InterpretsEntityTableFilters;
    use InterpretsEntityTableSort;
    use PaginatesEntityTable;

    private const STATE_COLORS = [
        'raw' => 'neutral',
        'draft' => 'warning',
        'auto' => 'info',
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
        return self::STATE_COLORS[(string) ($state ?? '')] ?? 'base';
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Aptitude::class);

        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';
        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['state', 'read_level', 'write_level', 'reuse_type', 'duration_type', 'range_type'] as $k) {
            if (! array_key_exists($k, $filters) && $request->has($k)) {
                $filters[$k] = $request->get($k);
            }
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';
        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        $sortsPayload = $request->input('sorts');
        if (is_array($sortsPayload) && isset($sortsPayload[0]) && is_array($sortsPayload[0])) {
            $sort = (string) ($sortsPayload[0]['field'] ?? $sortsPayload[0]['column'] ?? $sort);
            $order = strtolower((string) ($sortsPayload[0]['dir'] ?? $sortsPayload[0]['order'] ?? $order));
        }
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Aptitude::query()->visibleToUser($request->user())->with(['createdBy']);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('effect', 'like', "%{$search}%");
            });
        }
        if ($this->hasFilterValue($filters, 'state')) {
            $this->applyEqualityFilter($query, 'state', $filters['state']);
        }
        if ($this->hasFilterValue($filters, 'read_level')) {
            $this->applyEqualityFilter($query, 'read_level', $filters['read_level'], 'int');
        }
        if ($this->hasFilterValue($filters, 'write_level')) {
            $this->applyEqualityFilter($query, 'write_level', $filters['write_level'], 'int');
        }
        if ($this->hasFilterValue($filters, 'reuse_type')) {
            $this->applyEqualityFilter($query, 'reuse_type', $filters['reuse_type']);
        }
        if ($this->hasFilterValue($filters, 'duration_type')) {
            $this->applyEqualityFilter($query, 'duration_type', $filters['duration_type']);
        }
        if ($this->hasFilterValue($filters, 'range_type')) {
            $this->applyEqualityFilter($query, 'range_type', $filters['range_type']);
        }

        $allowedSort = [
            'id', 'name', 'reuse_type', 'duration_type', 'range_type',
            'state', 'read_level', 'write_level', 'created_at', 'updated_at',
        ];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Aptitude::class),
            'createAny' => Gate::allows('createAny', Aptitude::class),
            'updateAny' => Gate::allows('updateAny', Aptitude::class),
            'deleteAny' => Gate::allows('deleteAny', Aptitude::class),
            'manageAny' => Gate::allows('manageAny', Aptitude::class),
        ];
        $filterOptions = [
            'state' => EntityState::options(),
            'read_level' => self::LEVEL_OPTIONS,
            'write_level' => self::LEVEL_OPTIONS,
            'reuse_type' => [
                ['value' => Aptitude::REUSE_UNLIMITED, 'label' => 'Illimité'],
                ['value' => Aptitude::REUSE_SHORT_REST, 'label' => 'Repos court'],
                ['value' => Aptitude::REUSE_LONG_REST, 'label' => 'Repos long'],
                ['value' => Aptitude::REUSE_TIMED, 'label' => 'Temporisé'],
            ],
            'duration_type' => [
                ['value' => Aptitude::DURATION_INSTANT, 'label' => 'Instantané'],
                ['value' => Aptitude::DURATION_TIMED, 'label' => 'Temporisé'],
                ['value' => Aptitude::DURATION_PERMANENT, 'label' => 'Permanent'],
                ['value' => Aptitude::DURATION_WHILE_CONDITION, 'label' => 'Tant que…'],
            ],
            'range_type' => [
                ['value' => Aptitude::RANGE_SELF, 'label' => 'Soi'],
                ['value' => Aptitude::RANGE_DISTANCE, 'label' => 'Distance'],
                ['value' => Aptitude::RANGE_SENSE, 'label' => 'Sens'],
            ],
        ];

        if ($format === 'entities') {
            $entities = $rows->map(fn (Aptitude $a) => $this->entityPayload($a))->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'aptitudes',
                    'query' => [
                        'search' => $search,
                        'filters' => $filters,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $pageResult['limit'],
                        'page' => $pageResult['page'],
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => $filterOptions,
                    'pagination' => $pageResult['pagination'],
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Aptitude $a) {
            $showHref = route('entities.aptitudes.show', $a->id);
            $createdBy = $a->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');
            $state = (string) ($a->state ?? 'draft');

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
                    'reuse_type' => [
                        'type' => 'badge',
                        'value' => (string) $a->reuse_type,
                        'params' => [
                            'color' => 'neutral',
                            'filterValue' => (string) $a->reuse_type,
                            'sortValue' => (string) $a->reuse_type,
                        ],
                    ],
                    'duration_type' => [
                        'type' => 'badge',
                        'value' => (string) $a->duration_type,
                        'params' => [
                            'color' => 'info',
                            'filterValue' => (string) $a->duration_type,
                            'sortValue' => (string) $a->duration_type,
                        ],
                    ],
                    'range_type' => [
                        'type' => 'badge',
                        'value' => (string) $a->range_type,
                        'params' => [
                            'color' => 'info',
                            'filterValue' => (string) $a->range_type,
                            'sortValue' => (string) $a->range_type,
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
                    'created_by' => [
                        'type' => 'text',
                        'value' => $createdByLabel,
                        'params' => [
                            'searchValue' => $createdByLabel,
                            'sortValue' => $createdByLabel,
                        ],
                    ],
                ],
                'rowParams' => [
                    'entity' => $this->entityPayload($a),
                ],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'aptitudes',
                'query' => [
                    'search' => $search,
                    'filters' => $filters,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $pageResult['limit'],
                ],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
                'pagination' => $pageResult['pagination'],
            ],
            'rows' => $tableRows,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function entityPayload(Aptitude $a): array
    {
        $createdBy = $a->createdBy;

        return [
            'id' => $a->id,
            'name' => $a->name,
            'description' => $a->description,
            'effect' => $a->effect,
            'reuse_type' => $a->reuse_type,
            'reuse_amount' => $a->reuse_amount,
            'reuse_unit' => $a->reuse_unit,
            'duration_type' => $a->duration_type,
            'duration_amount' => $a->duration_amount,
            'duration_unit' => $a->duration_unit,
            'duration_note' => $a->duration_note,
            'range_type' => $a->range_type,
            'range_amount' => $a->range_amount,
            'range_unit' => $a->range_unit,
            'range_sense' => $a->range_sense,
            'range_note' => $a->range_note,
            'state' => (string) ($a->state ?? 'draft'),
            'read_level' => (int) ($a->read_level ?? 0),
            'write_level' => (int) ($a->write_level ?? 0),
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
    }
}
