<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Endpoint Table v2 pour le référentiel des langues (Bibliothèque).
 */
class LanguageTableController extends Controller
{
    use InterpretsEntityTableFilters;
    use InterpretsEntityTableSort;
    use PaginatesEntityTable;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Language::class);

        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';
        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        $search = $request->filled('search') ? (string) $request->get('search') : '';
        $sort = (string) $request->get('sort', 'name');
        $order = (string) $request->get('order', 'asc');
        $sortsPayload = $request->input('sorts');
        if (is_array($sortsPayload) && isset($sortsPayload[0]) && is_array($sortsPayload[0])) {
            $sort = (string) ($sortsPayload[0]['field'] ?? $sortsPayload[0]['column'] ?? $sort);
            $order = strtolower((string) ($sortsPayload[0]['dir'] ?? $sortsPayload[0]['order'] ?? $order));
        }
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'asc';
        }

        $query = Language::query();
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $allowedSort = ['id', 'name', 'color', 'created_at', 'updated_at'];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'name', 'asc');

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Language::class),
            'createAny' => Gate::allows('create', Language::class),
            'updateAny' => Gate::allows('create', Language::class),
            'deleteAny' => Gate::allows('create', Language::class),
            'manageAny' => Gate::allows('create', Language::class),
        ];
        $filterOptions = [];

        if ($format === 'entities') {
            $entities = $rows->map(fn (Language $language) => $this->entityPayload($language))->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'languages',
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

        $tableRows = $rows->map(function (Language $language) {
            return [
                'id' => $language->id,
                'cells' => [
                    'name' => [
                        'type' => 'text',
                        'value' => (string) $language->name,
                        'params' => [
                            'searchValue' => (string) $language->name,
                            'sortValue' => (string) $language->name,
                        ],
                    ],
                    'description' => [
                        'type' => 'text',
                        'value' => (string) ($language->description ?? '—'),
                        'params' => [
                            'searchValue' => (string) ($language->description ?? ''),
                            'sortValue' => (string) ($language->description ?? ''),
                        ],
                    ],
                    'color' => [
                        'type' => 'badge',
                        'value' => (string) ($language->color ?? '#64748b'),
                        'params' => [
                            'color' => 'neutral',
                            'filterValue' => (string) ($language->color ?? ''),
                            'sortValue' => (string) ($language->color ?? ''),
                            'style' => [
                                'backgroundColor' => (string) ($language->color ?? '#64748b'),
                                'color' => '#fff',
                            ],
                        ],
                    ],
                ],
                'rowParams' => [
                    'entity' => $this->entityPayload($language),
                ],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'languages',
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
    private function entityPayload(Language $language): array
    {
        return [
            'id' => $language->id,
            'name' => $language->name,
            'description' => $language->description,
            'color' => $language->color,
            'created_at' => $language->created_at?->toISOString(),
            'updated_at' => $language->updated_at?->toISOString(),
        ];
    }
}
