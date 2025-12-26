<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Monster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * MonsterTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les monstres.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class MonsterTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Monster::class);

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['size', 'is_boss'] as $k) {
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

        $query = Monster::query()->with(['creature', 'monsterRace']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('dofusdb_id', 'like', "%{$search}%")
                    ->orWhereHas('creature', fn ($qq) => $qq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('monsterRace', fn ($qq) => $qq->where('name', 'like', "%{$search}%"));
            });
        }

        if (array_key_exists('size', $filters) && $filters['size'] !== '' && $filters['size'] !== null) {
            $query->where('size', (int) $filters['size']);
        }
        if (array_key_exists('is_boss', $filters) && $filters['is_boss'] !== '' && $filters['is_boss'] !== null) {
            $query->where('is_boss', (int) $filters['is_boss']);
        }

        $allowedSort = ['id', 'size', 'is_boss', 'dofusdb_id', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Monster::class),
            'createAny' => Gate::allows('createAny', Monster::class),
            'updateAny' => Gate::allows('updateAny', Monster::class),
            'deleteAny' => Gate::allows('deleteAny', Monster::class),
            'manageAny' => Gate::allows('manageAny', Monster::class),
        ];

        $filterOptions = [
            'size' => collect(Monster::SIZE)->map(fn ($label, $value) => ['value' => (string) $value, 'label' => (string) $label])->values()->all(),
            'is_boss' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
        ];

        $tableRows = $rows->map(function (Monster $m) {
            $showHref = route('entities.monsters.show', $m->id);
            $creatureName = $m->creature?->name ?? '-';
            $raceName = $m->monsterRace?->name ?? '-';

            $createdAtLabel = $m->created_at ? $m->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $m->created_at ? $m->created_at->getTimestamp() : 0;
            $updatedAtLabel = $m->updated_at ? $m->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $m->updated_at ? $m->updated_at->getTimestamp() : 0;

            $sizeLabel = Monster::SIZE[$m->size] ?? (string) $m->size;
            $bossLabel = ((int) ($m->is_boss ?? 0)) === 1 ? 'Boss' : 'Non';

            return [
                'id' => $m->id,
                'cells' => [
                    'creature_name' => [
                        'type' => 'route',
                        'value' => $creatureName,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => $creatureName,
                            'sortValue' => $creatureName,
                        ],
                    ],
                    'monster_race' => [
                        'type' => 'text',
                        'value' => $raceName,
                        'params' => [
                            'searchValue' => $raceName,
                            'sortValue' => $raceName,
                        ],
                    ],
                    'size' => [
                        'type' => 'text',
                        'value' => $sizeLabel,
                        'params' => [
                            'filterValue' => (string) ($m->size ?? ''),
                            'sortValue' => (int) ($m->size ?? 0),
                        ],
                    ],
                    'is_boss' => [
                        'type' => 'badge',
                        'value' => $bossLabel,
                        'params' => [
                            'color' => ((int) ($m->is_boss ?? 0)) === 1 ? 'error' : 'base',
                            'filterValue' => (string) ((int) ($m->is_boss ?? 0)),
                            'sortValue' => (int) ($m->is_boss ?? 0),
                        ],
                    ],
                    'dofusdb_id' => [
                        'type' => 'text',
                        'value' => $m->dofusdb_id ?: '-',
                        'params' => [
                            'sortValue' => $m->dofusdb_id ?? 0,
                            'searchValue' => (string) ($m->dofusdb_id ?? ''),
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
                        'id' => $m->id,
                        'creature_id' => $m->creature_id,
                        'official_id' => $m->official_id,
                        'dofusdb_id' => $m->dofusdb_id,
                        'dofus_version' => $m->dofus_version,
                        'auto_update' => (bool) $m->auto_update,
                        'size' => $m->size,
                        'is_boss' => (int) ($m->is_boss ?? 0),
                        'boss_pa' => $m->boss_pa,
                        'monster_race_id' => $m->monster_race_id,
                        'creature' => $m->creature ? [
                            'id' => $m->creature->id,
                            'name' => $m->creature->name,
                        ] : null,
                        'monsterRace' => $m->monsterRace ? [
                            'id' => $m->monsterRace->id,
                            'name' => $m->monsterRace->name,
                        ] : null,
                    ],
                ],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'monsters',
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


