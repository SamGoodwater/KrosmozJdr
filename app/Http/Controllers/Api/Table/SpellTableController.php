<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Spell;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * SpellTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les sorts.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class SpellTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Spell::class);

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['level', 'pa'] as $k) {
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

        $query = Spell::query()->with(['createdBy', 'spellTypes']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('level', $filters) && $filters['level'] !== '' && $filters['level'] !== null) {
            $query->where('level', (string) $filters['level']);
        }
        if (array_key_exists('pa', $filters) && $filters['pa'] !== '' && $filters['pa'] !== null) {
            $query->where('pa', (string) $filters['pa']);
        }

        $allowedSort = ['id', 'name', 'level', 'pa', 'po', 'area', 'dofusdb_id', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Spell::class),
            'createAny' => Gate::allows('createAny', Spell::class),
            'updateAny' => Gate::allows('updateAny', Spell::class),
            'deleteAny' => Gate::allows('deleteAny', Spell::class),
            'manageAny' => Gate::allows('manageAny', Spell::class),
        ];

        $filterOptions = [
            'level' => [
                ['value' => '1', 'label' => '1'],
                ['value' => '50', 'label' => '50'],
                ['value' => '100', 'label' => '100'],
                ['value' => '150', 'label' => '150'],
                ['value' => '200', 'label' => '200'],
            ],
            'pa' => [
                ['value' => '1', 'label' => '1'],
                ['value' => '2', 'label' => '2'],
                ['value' => '3', 'label' => '3'],
                ['value' => '4', 'label' => '4'],
                ['value' => '5', 'label' => '5'],
                ['value' => '6', 'label' => '6+'],
            ],
        ];

        $tableRows = $rows->map(function (Spell $sp) {
            $showHref = route('entities.spells.show', $sp->id);
            $dofusDbHref = $sp->dofusdb_id ? "https://www.dofus.com/fr/mmorpg/encyclopedie/sorts/{$sp->dofusdb_id}" : null;

            $createdBy = $sp->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $sp->created_at ? $sp->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $sp->created_at ? $sp->created_at->getTimestamp() : 0;
            $updatedAtLabel = $sp->updated_at ? $sp->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $sp->updated_at ? $sp->updated_at->getTimestamp() : 0;

            $types = $sp->spellTypes?->pluck('name')->filter()->values()->all() ?? [];
            $typesLabel = count($types) ? implode(', ', $types) : '-';

            return [
                'id' => $sp->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $sp->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $sp->name,
                            'sortValue' => (string) $sp->name,
                        ],
                    ],
                    'level' => [
                        'type' => 'text',
                        'value' => $sp->level ?: '-',
                        'params' => [
                            'filterValue' => (string) ($sp->level ?? ''),
                            'sortValue' => is_numeric((string) $sp->level) ? (int) $sp->level : (string) ($sp->level ?? ''),
                            'searchValue' => (string) ($sp->level ?? ''),
                        ],
                    ],
                    'pa' => [
                        'type' => 'text',
                        'value' => $sp->pa ?: '-',
                        'params' => [
                            'filterValue' => (string) ($sp->pa ?? ''),
                            'sortValue' => is_numeric((string) $sp->pa) ? (int) $sp->pa : (string) ($sp->pa ?? ''),
                        ],
                    ],
                    'po' => [
                        'type' => 'text',
                        'value' => $sp->po ?: '-',
                        'params' => [
                            'sortValue' => (string) ($sp->po ?? ''),
                        ],
                    ],
                    'area' => [
                        'type' => 'text',
                        'value' => $sp->area ?? '-',
                        'params' => [
                            'sortValue' => (int) ($sp->area ?? 0),
                        ],
                    ],
                    'spell_types' => [
                        'type' => 'text',
                        'value' => $typesLabel,
                        'params' => [
                            'searchValue' => $typesLabel,
                            'sortValue' => $typesLabel,
                        ],
                    ],
                    'dofusdb_id' => [
                        'type' => 'route',
                        'value' => $sp->dofusdb_id ? (string) $sp->dofusdb_id : '-',
                        'params' => [
                            'href' => $dofusDbHref,
                            'target' => '_blank',
                            'sortValue' => $sp->dofusdb_id ?? 0,
                            'filterValue' => (string) ($sp->dofusdb_id ?? ''),
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
                'rowParams' => [
                    'entity' => [
                        'id' => $sp->id,
                        'official_id' => $sp->official_id,
                        'dofusdb_id' => $sp->dofusdb_id,
                        'name' => $sp->name,
                        'description' => $sp->description,
                        'effect' => $sp->effect,
                        'area' => $sp->area,
                        'level' => $sp->level,
                        'po' => $sp->po,
                        'po_editable' => (bool) $sp->po_editable,
                        'pa' => $sp->pa,
                        'cast_per_turn' => $sp->cast_per_turn,
                        'cast_per_target' => $sp->cast_per_target,
                        'sight_line' => (bool) $sp->sight_line,
                        'number_between_two_cast' => $sp->number_between_two_cast,
                        'number_between_two_cast_editable' => (bool) $sp->number_between_two_cast_editable,
                        'element' => $sp->element,
                        'category' => $sp->category,
                        'is_magic' => (bool) $sp->is_magic,
                        'powerful' => $sp->powerful,
                        'usable' => (int) ($sp->usable ?? 0),
                        'is_visible' => $sp->is_visible,
                        'image' => $sp->image,
                        'auto_update' => (bool) $sp->auto_update,
                        'spellTypes' => $sp->spellTypes?->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])->values()->all() ?? [],
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
                'entityType' => 'spells',
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


