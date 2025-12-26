<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Specialization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * SpecializationTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les spécialisations.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class SpecializationTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Specialization::class);

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Specialization::query()->with(['createdBy'])->withCount('capabilities');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $allowedSort = ['id', 'name', 'capabilities_count', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Specialization::class),
            'createAny' => Gate::allows('createAny', Specialization::class),
            'updateAny' => Gate::allows('updateAny', Specialization::class),
            'deleteAny' => Gate::allows('deleteAny', Specialization::class),
            'manageAny' => Gate::allows('manageAny', Specialization::class),
        ];

        $tableRows = $rows->map(function (Specialization $s) {
            $showHref = route('entities.specializations.show', $s->id);
            $createdBy = $s->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $s->created_at ? $s->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $s->created_at ? $s->created_at->getTimestamp() : 0;
            $updatedAtLabel = $s->updated_at ? $s->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $s->updated_at ? $s->updated_at->getTimestamp() : 0;

            return [
                'id' => $s->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $s->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $s->name,
                            'sortValue' => (string) $s->name,
                        ],
                    ],
                    'description' => [
                        'type' => 'text',
                        'value' => (string) ($s->description ?? '-'),
                        'params' => [
                            'searchValue' => (string) ($s->description ?? ''),
                        ],
                    ],
                    'capabilities_count' => [
                        'type' => 'text',
                        'value' => (string) ((int) ($s->capabilities_count ?? 0)),
                        'params' => [
                            'sortValue' => (int) ($s->capabilities_count ?? 0),
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
                        'id' => $s->id,
                        'name' => $s->name,
                        'description' => $s->description,
                        'usable' => (int) ($s->usable ?? 0),
                        'is_visible' => $s->is_visible,
                        'image' => $s->image,
                        'created_by' => $s->created_by,
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
                'entityType' => 'specializations',
                'query' => [
                    'search' => $search,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => [],
            ],
            'rows' => $tableRows,
        ]);
    }
}


