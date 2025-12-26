<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Panoply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * PanoplyTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les panoplies.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class PanoplyTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Panoply::class);

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Panoply::query()->with(['createdBy'])->withCount('items');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('bonus', 'like', "%{$search}%");
            });
        }

        $allowedSort = ['id', 'name', 'items_count', 'dofusdb_id', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Panoply::class),
            'createAny' => Gate::allows('createAny', Panoply::class),
            'updateAny' => Gate::allows('updateAny', Panoply::class),
            'deleteAny' => Gate::allows('deleteAny', Panoply::class),
            'manageAny' => Gate::allows('manageAny', Panoply::class),
        ];

        $tableRows = $rows->map(function (Panoply $p) {
            $showHref = route('entities.panoplies.show', $p->id);
            $createdBy = $p->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $p->created_at ? $p->created_at->getTimestamp() : 0;
            $updatedAtLabel = $p->updated_at ? $p->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $p->updated_at ? $p->updated_at->getTimestamp() : 0;

            return [
                'id' => $p->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $p->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $p->name,
                            'sortValue' => (string) $p->name,
                        ],
                    ],
                    'bonus' => [
                        'type' => 'text',
                        'value' => (string) ($p->bonus ?? '-'),
                        'params' => [
                            'searchValue' => (string) ($p->bonus ?? ''),
                        ],
                    ],
                    'items_count' => [
                        'type' => 'text',
                        'value' => (string) ((int) ($p->items_count ?? 0)),
                        'params' => [
                            'sortValue' => (int) ($p->items_count ?? 0),
                        ],
                    ],
                    'dofusdb_id' => [
                        'type' => 'text',
                        'value' => $p->dofusdb_id ? (string) $p->dofusdb_id : '-',
                        'params' => [
                            'sortValue' => $p->dofusdb_id ?? 0,
                            'searchValue' => (string) ($p->dofusdb_id ?? ''),
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
                    'entity' => $p->toArray() + [
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
                'entityType' => 'panoplies',
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


