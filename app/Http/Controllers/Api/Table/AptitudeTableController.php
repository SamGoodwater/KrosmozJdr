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
        foreach (['state', 'read_level', 'write_level', 'reuse', 'duration', 'range'] as $k) {
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
                    ->orWhere('effect', 'like', "%{$search}%")
                    ->orWhere('reuse', 'like', "%{$search}%")
                    ->orWhere('duration', 'like', "%{$search}%")
                    ->orWhere('range', 'like', "%{$search}%");
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
        foreach (['reuse', 'duration', 'range'] as $textField) {
            if ($this->hasFilterValue($filters, $textField)) {
                $needle = trim((string) collect($this->normalizeFilterList($filters[$textField]))->first());
                if ($needle !== '') {
                    $query->where($textField, 'like', "%{$needle}%");
                }
            }
        }

        $allowedSort = [
            'id', 'name', 'reuse', 'duration', 'range',
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
                    'reuse' => [
                        'type' => 'text',
                        'value' => (string) ($a->reuse ?: '—'),
                        'params' => [
                            'searchValue' => (string) ($a->reuse ?? ''),
                            'sortValue' => (string) ($a->reuse ?? ''),
                        ],
                    ],
                    'duration' => [
                        'type' => 'text',
                        'value' => (string) ($a->duration ?: '—'),
                        'params' => [
                            'searchValue' => (string) ($a->duration ?? ''),
                            'sortValue' => (string) ($a->duration ?? ''),
                        ],
                    ],
                    'range' => [
                        'type' => 'text',
                        'value' => (string) ($a->range ?: '—'),
                        'params' => [
                            'searchValue' => (string) ($a->range ?? ''),
                            'sortValue' => (string) ($a->range ?? ''),
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
            'reuse' => $a->reuse,
            'duration' => $a->duration,
            'range' => $a->range,
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
