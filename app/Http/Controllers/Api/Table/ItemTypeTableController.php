<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Type\ItemType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ItemTypeTableController
 *
 * Endpoint "Table v2" pour les types d'équipements (ItemType).
 * Compatible moteur de recherche : format=entities, search, filters, sort, whitelist/blacklist.
 */
class ItemTypeTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ItemType::class);

        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        if (! array_key_exists('decision', $filters) && $request->has('decision')) {
            $filters['decision'] = $request->get('decision');
        }
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

        $query = ItemType::query()->withCount('items');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('dofusdb_type_id', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('decision', $filters) && $filters['decision'] !== '' && $filters['decision'] !== null) {
            $decision = (string) $filters['decision'];
            if (in_array($decision, [ItemType::DECISION_PENDING, ItemType::DECISION_ALLOWED, ItemType::DECISION_BLOCKED], true)) {
                $query->where('decision', $decision);
            }
        }
        if (array_key_exists('state', $filters) && $filters['state'] !== '' && $filters['state'] !== null) {
            $query->where('state', (string) $filters['state']);
        }
        if (array_key_exists('id', $filters) && $filters['id'] !== '' && $filters['id'] !== null) {
            $query->where('id', (int) $filters['id']);
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

        $allowedSort = ['id', 'name', 'state', 'dofusdb_type_id', 'decision', 'items_count', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', ItemType::class),
            'createAny' => Gate::allows('createAny', ItemType::class),
            'updateAny' => Gate::allows('updateAny', ItemType::class),
            'deleteAny' => Gate::allows('deleteAny', ItemType::class),
            'manageAny' => Gate::allows('manageAny', ItemType::class),
        ];

        $filterOptions = [
            'decision' => [
                ['value' => ItemType::DECISION_PENDING, 'label' => 'En attente'],
                ['value' => ItemType::DECISION_ALLOWED, 'label' => 'Utilisé'],
                ['value' => ItemType::DECISION_BLOCKED, 'label' => 'Non utilisé'],
            ],
            'state' => [
                ['value' => ItemType::STATE_RAW, 'label' => 'Brut'],
                ['value' => ItemType::STATE_DRAFT, 'label' => 'Brouillon'],
                ['value' => ItemType::STATE_PLAYABLE, 'label' => 'Jouable'],
                ['value' => ItemType::STATE_ARCHIVED, 'label' => 'Archivé'],
            ],
        ];

        if ($format === 'entities') {
            $entities = $rows->map(function (ItemType $t) {
                return [
                    'id' => $t->id,
                    'name' => (string) $t->name,
                    'state' => (string) ($t->state ?? ItemType::STATE_DRAFT),
                    'dofusdb_type_id' => $t->dofusdb_type_id,
                    'decision' => (string) ($t->decision ?? ItemType::DECISION_PENDING),
                    'items_count' => (int) ($t->items_count ?? 0),
                    'created_at' => $t->created_at?->toISOString(),
                    'updated_at' => $t->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'item-types',
                    'query' => ['search' => $search, 'filters' => $filters, 'sort' => $sort, 'order' => $order, 'limit' => $limit],
                    'capabilities' => $capabilities,
                    'filterOptions' => $filterOptions,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (ItemType $t) {
            $showHref = route('entities.item-types.index');
            return [
                'id' => $t->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $t->name,
                        'params' => ['href' => $showHref, 'searchValue' => (string) $t->name, 'sortValue' => (string) $t->name],
                    ],
                    'state' => ['type' => 'text', 'value' => (string) ($t->state ?? '-'), 'params' => ['sortValue' => (string) ($t->state ?? '')]],
                    'items_count' => ['type' => 'text', 'value' => (string) ((int) ($t->items_count ?? 0)), 'params' => ['sortValue' => (int) ($t->items_count ?? 0)]],
                ],
                'rowParams' => ['entity' => ['id' => $t->id, 'name' => $t->name, 'state' => $t->state, 'items_count' => (int) ($t->items_count ?? 0)]],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'item-types',
                'query' => ['search' => $search, 'filters' => $filters, 'sort' => $sort, 'order' => $order, 'limit' => $limit],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
            ],
            'rows' => $tableRows,
        ]);
    }
}
