<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Campaign;
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
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Campaign::class);

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['state', 'is_public'] as $k) {
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
            $query->where('state', (int) $filters['state']);
        }
        if (array_key_exists('is_public', $filters) && $filters['is_public'] !== '' && $filters['is_public'] !== null) {
            $query->where('is_public', (int) $filters['is_public']);
        }

        $allowedSort = ['id', 'name', 'slug', 'state', 'is_public', 'created_at', 'updated_at'];
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
            'state' => collect(Campaign::STATE)->map(fn ($label, $value) => ['value' => (string) $value, 'label' => (string) $label])->values()->all(),
            'is_public' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
        ];

        $tableRows = $rows->map(function (Campaign $c) {
            $showHref = route('entities.campaigns.show', $c->id);

            $createdBy = $c->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $c->created_at ? $c->created_at->getTimestamp() : 0;
            $updatedAtLabel = $c->updated_at ? $c->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $c->updated_at ? $c->updated_at->getTimestamp() : 0;

            $stateLabel = Campaign::STATE[$c->state] ?? (string) $c->state;
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
                            'sortValue' => (int) ($c->state ?? 0),
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
                        'state' => $c->state,
                        'usable' => (int) ($c->usable ?? 0),
                        'is_visible' => $c->is_visible,
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


