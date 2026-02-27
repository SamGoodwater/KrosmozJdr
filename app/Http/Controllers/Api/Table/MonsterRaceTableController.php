<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Type\MonsterRace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * MonsterRaceTableController
 *
 * Endpoint "Table v2" pour les races de monstres.
 * Compatible moteur de recherche : format=entities, search, filters, sort, whitelist/blacklist.
 */
class MonsterRaceTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MonsterRace::class);

        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        if (! array_key_exists('state', $filters) && $request->has('state')) {
            $filters['state'] = $request->get('state');
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';
        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = MonsterRace::query()->withCount('monsters')->with('superRace');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('dofusdb_race_id', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('state', $filters) && $filters['state'] !== '' && $filters['state'] !== null) {
            $query->where('state', (string) $filters['state']);
        }
        if (array_key_exists('id', $filters) && $filters['id'] !== '' && $filters['id'] !== null) {
            $query->where('id', (int) $filters['id']);
        }
        if (array_key_exists('id_super_race', $filters) && $filters['id_super_race'] !== '' && $filters['id_super_race'] !== null) {
            $query->where('id_super_race', (int) $filters['id_super_race']);
        }

        $whitelist = $request->input('whitelist', $request->input('ids', []));
        $blacklist = $request->input('blacklist', $request->input('exclude', []));
        $whitelistIds = collect((array) $whitelist)->map(fn ($v) => (int) $v)->filter(fn ($v) => $v > 0)->values()->all();
        $blacklistIds = collect((array) $blacklist)->map(fn ($v) => (int) $v)->filter(fn ($v) => $v > 0)->values()->all();
        if (! empty($whitelistIds)) {
            $query->whereIn('id', $whitelistIds);
        }
        if (! empty($blacklistIds)) {
            $query->whereNotIn('id', $blacklistIds);
        }

        $allowedSort = ['id', 'name', 'state', 'dofusdb_race_id', 'monsters_count', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', MonsterRace::class),
            'createAny' => Gate::allows('createAny', MonsterRace::class),
            'updateAny' => Gate::allows('updateAny', MonsterRace::class),
            'deleteAny' => Gate::allows('deleteAny', MonsterRace::class),
            'manageAny' => Gate::allows('manageAny', MonsterRace::class),
        ];

        $filterOptions = [
            'state' => [
                ['value' => MonsterRace::STATE_RAW, 'label' => 'Brut'],
                ['value' => MonsterRace::STATE_DRAFT, 'label' => 'Brouillon'],
                ['value' => MonsterRace::STATE_PLAYABLE, 'label' => 'Jouable'],
                ['value' => MonsterRace::STATE_ARCHIVED, 'label' => 'Archivé'],
            ],
        ];

        if ($format === 'entities') {
            $entities = $rows->map(function (MonsterRace $r) {
                return [
                    'id' => $r->id,
                    'name' => (string) $r->name,
                    'state' => (string) ($r->state ?? MonsterRace::STATE_DRAFT),
                    'dofusdb_race_id' => $r->dofusdb_race_id,
                    'id_super_race' => $r->id_super_race,
                    'super_race' => $r->superRace ? ['id' => $r->superRace->id, 'name' => $r->superRace->name] : null,
                    'monsters_count' => (int) ($r->monsters_count ?? 0),
                    'created_at' => $r->created_at?->toISOString(),
                    'updated_at' => $r->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'monster-races',
                    'query' => ['search' => $search, 'filters' => $filters, 'sort' => $sort, 'order' => $order, 'limit' => $limit],
                    'capabilities' => $capabilities,
                    'filterOptions' => $filterOptions,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (MonsterRace $r) {
            $showHref = route('entities.monster-races.index');
            return [
                'id' => $r->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $r->name,
                        'params' => ['href' => $showHref, 'searchValue' => (string) $r->name, 'sortValue' => (string) $r->name],
                    ],
                    'state' => ['type' => 'text', 'value' => (string) ($r->state ?? '-'), 'params' => ['sortValue' => (string) ($r->state ?? '')]],
                    'monsters_count' => ['type' => 'text', 'value' => (string) ((int) ($r->monsters_count ?? 0)), 'params' => ['sortValue' => (int) ($r->monsters_count ?? 0)]],
                ],
                'rowParams' => ['entity' => ['id' => $r->id, 'name' => $r->name, 'state' => $r->state, 'monsters_count' => (int) ($r->monsters_count ?? 0)]],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'monster-races',
                'query' => ['search' => $search, 'filters' => $filters, 'sort' => $sort, 'order' => $order, 'limit' => $limit],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
            ],
            'rows' => $tableRows,
        ]);
    }
}
