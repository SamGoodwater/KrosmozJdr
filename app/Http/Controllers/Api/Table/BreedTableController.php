<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Breed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Endpoint "Table v2" (TanStack Table) pour les breeds (affichées « Classes »).
 */
class BreedTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Breed::class);

        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';
        $search = $request->filled('search') ? (string) $request->get('search') : '';
        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));
        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Breed::query()->with(['createdBy']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('specificity', 'like', "%{$search}%");
            });
        }

        $allowedSort = ['id', 'name', 'life', 'life_dice', 'dofusdb_id', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Breed::class),
            'createAny' => Gate::allows('createAny', Breed::class),
            'updateAny' => Gate::allows('updateAny', Breed::class),
            'deleteAny' => Gate::allows('deleteAny', Breed::class),
            'manageAny' => Gate::allows('manageAny', Breed::class),
        ];

        if ($format === 'entities') {
            $entities = $rows->map(function (Breed $c) {
                $createdBy = $c->createdBy;
                return [
                    'id' => $c->id,
                    'official_id' => $c->official_id,
                    'dofusdb_id' => $c->dofusdb_id,
                    'name' => $c->name,
                    'description_fast' => $c->description_fast,
                    'description' => $c->description,
                    'life' => $c->life,
                    'life_dice' => $c->life_dice,
                    'specificity' => $c->specificity,
                    'dofus_version' => $c->dofus_version,
                    'state' => (string) ($c->state ?? 'draft'),
                    'read_level' => (int) ($c->read_level ?? 0),
                    'write_level' => (int) ($c->write_level ?? 0),
                    'image' => $c->image,
                    'icon' => $c->icon,
                    'auto_update' => (bool) $c->auto_update,
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
                    'entityType' => 'breeds',
                    'query' => [
                        'search' => $search,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $limit,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => [],
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Breed $c) {
            $showHref = route('entities.breeds.show', $c->id);
            $createdBy = $c->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');
            $createdAtLabel = $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $c->created_at ? $c->created_at->getTimestamp() : 0;
            $updatedAtLabel = $c->updated_at ? $c->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $c->updated_at ? $c->updated_at->getTimestamp() : 0;

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
                    'life' => [
                        'type' => 'text',
                        'value' => $c->life ?: '-',
                        'params' => [
                            'sortValue' => is_numeric((string) $c->life) ? (int) $c->life : (string) ($c->life ?? ''),
                        ],
                    ],
                    'life_dice' => [
                        'type' => 'text',
                        'value' => $c->life_dice ?: '-',
                        'params' => [
                            'sortValue' => (string) ($c->life_dice ?? ''),
                        ],
                    ],
                    'specificity' => [
                        'type' => 'text',
                        'value' => (string) ($c->specificity ?? '-'),
                        'params' => [
                            'searchValue' => (string) ($c->specificity ?? ''),
                            'sortValue' => (string) ($c->specificity ?? ''),
                        ],
                    ],
                    'dofusdb_id' => [
                        'type' => 'text',
                        'value' => $c->dofusdb_id ?: '-',
                        'params' => [
                            'sortValue' => $c->dofusdb_id ?? 0,
                            'searchValue' => (string) ($c->dofusdb_id ?? ''),
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
                        'official_id' => $c->official_id,
                        'dofusdb_id' => $c->dofusdb_id,
                        'name' => $c->name,
                        'description_fast' => $c->description_fast,
                        'description' => $c->description,
                        'life' => $c->life,
                        'life_dice' => $c->life_dice,
                        'specificity' => $c->specificity,
                        'dofus_version' => $c->dofus_version,
                        'state' => (string) ($c->state ?? 'draft'),
                        'read_level' => (int) ($c->read_level ?? 0),
                        'write_level' => (int) ($c->write_level ?? 0),
                        'image' => $c->image,
                        'icon' => $c->icon,
                        'auto_update' => (bool) $c->auto_update,
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
                'entityType' => 'breeds',
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
