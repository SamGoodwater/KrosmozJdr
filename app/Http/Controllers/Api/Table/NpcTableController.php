<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Npc;
use App\Models\Entity\Specialization;
use App\Services\Characteristic\CharacteristicMetaByDbColumnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * NpcTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les PNJ.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class NpcTableController extends Controller
{
    public function __construct(
        private readonly CharacteristicMetaByDbColumnService $characteristicMeta
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Npc::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['breed_id', 'specialization_id', 'creature_level', 'creature_state'] as $k) {
            if (! array_key_exists($k, $filters) && $request->has($k)) {
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

        $query = Npc::query()->with(['creature', 'breed', 'specialization']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('creature', fn ($qq) => $qq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('breed', fn ($qq) => $qq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('specialization', fn ($qq) => $qq->where('name', 'like', "%{$search}%"));
            });
        }

        if (array_key_exists('breed_id', $filters) && $filters['breed_id'] !== '' && $filters['breed_id'] !== null) {
            $val = $filters['breed_id'];
            $ids = is_array($val) ? array_map('intval', $val) : [(int) $val];
            $ids = array_filter($ids, fn ($id) => $id > 0);
            if ($ids !== []) {
                $query->whereIn('breed_id', $ids);
            }
        }
        if (array_key_exists('specialization_id', $filters) && $filters['specialization_id'] !== '' && $filters['specialization_id'] !== null) {
            $val = $filters['specialization_id'];
            $ids = is_array($val) ? array_map('intval', $val) : [(int) $val];
            $ids = array_filter($ids, fn ($id) => $id > 0);
            if ($ids !== []) {
                $query->whereIn('specialization_id', $ids);
            }
        }
        if (array_key_exists('creature_level', $filters) && $filters['creature_level'] !== '' && $filters['creature_level'] !== null) {
            $val = $filters['creature_level'];
            $levels = is_array($val) ? $val : [$val];
            $levels = array_filter($levels, fn ($v) => $v !== '' && $v !== null);
            if ($levels !== []) {
                $query->whereHas('creature', fn ($q) => $q->whereIn('level', $levels));
            }
        }
        if (array_key_exists('creature_state', $filters) && $filters['creature_state'] !== '' && $filters['creature_state'] !== null) {
            $val = $filters['creature_state'];
            $states = is_array($val) ? $val : [$val];
            $states = array_filter($states, fn ($v) => $v !== '' && $v !== null);
            if ($states !== []) {
                $query->whereHas('creature', fn ($q) => $q->whereIn('state', $states));
            }
        }

        $allowedSort = ['id', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Npc::class),
            'createAny' => Gate::allows('createAny', Npc::class),
            'updateAny' => Gate::allows('updateAny', Npc::class),
            'deleteAny' => Gate::allows('deleteAny', Npc::class),
            'manageAny' => Gate::allows('manageAny', Npc::class),
        ];

        $breedOptions = Breed::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(fn ($b) => ['value' => (string) $b->id, 'label' => (string) $b->name])
            ->values()
            ->all();
        $specializationOptions = Specialization::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(fn ($s) => ['value' => (string) $s->id, 'label' => (string) $s->name])
            ->values()
            ->all();
        $toDistinctOptions = function ($values, $sort = true) {
            $collected = collect($values)->filter(fn ($v) => $v !== null && $v !== '')->map(fn ($v) => (string) $v)->unique()->values();
            if ($sort) {
                $collected = $collected->sort(SORT_NATURAL)->values();
            }

            return $collected->map(fn ($v) => ['value' => $v, 'label' => $v])->all();
        };
        $creatureStateOptions = [
            ['value' => Creature::STATE_RAW, 'label' => 'Brouillon (raw)'],
            ['value' => Creature::STATE_DRAFT, 'label' => 'Brouillon'],
            ['value' => Creature::STATE_PLAYABLE, 'label' => 'Jouable'],
            ['value' => Creature::STATE_ARCHIVED, 'label' => 'Archivé'],
        ];
        $filterOptions = [
            'breed_id' => $breedOptions,
            'specialization_id' => $specializationOptions,
            'creature_level' => $toDistinctOptions($rows->pluck('creature.level')),
            'creature_state' => $creatureStateOptions,
        ];

        // Mode "entities" : retourner les entités brutes (créature complète pour colonnes résumé comme Monster)
        if ($format === 'entities') {
            $entities = $rows->map(function (Npc $n) {
                $creature = null;
                if ($n->creature) {
                    $c = $n->creature;
                    $creature = [
                        'id' => $c->id,
                        'name' => $c->name,
                        'description' => $c->description,
                        'level' => $c->level,
                        'life' => $c->life,
                        'pa' => $c->pa,
                        'pm' => $c->pm,
                        'po' => $c->po,
                        'ini' => $c->ini,
                        'ca' => $c->ca,
                        'touch' => $c->touch,
                        'invocation' => $c->invocation,
                        'dodge_pa' => $c->dodge_pa,
                        'dodge_pm' => $c->dodge_pm,
                        'fuite' => $c->fuite,
                        'tacle' => $c->tacle,
                        'vitality' => $c->vitality,
                        'sagesse' => $c->sagesse,
                        'strong' => $c->strong,
                        'intel' => $c->intel,
                        'agi' => $c->agi,
                        'chance' => $c->chance,
                        'hostility' => $c->hostility,
                        'location' => $c->location,
                        'image' => $c->image,
                        'state' => $c->state,
                        'other_info' => $c->other_info,
                        'kamas' => $c->kamas,
                        'drop_' => $c->drop_,
                        'other_item' => $c->other_item,
                        'other_consumable' => $c->other_consumable,
                        'other_resource' => $c->other_resource,
                        'other_spell' => $c->other_spell,
                        'do_fixe_neutre' => $c->do_fixe_neutre,
                        'do_fixe_terre' => $c->do_fixe_terre,
                        'do_fixe_feu' => $c->do_fixe_feu,
                        'do_fixe_air' => $c->do_fixe_air,
                        'do_fixe_eau' => $c->do_fixe_eau,
                        'res_fixe_neutre' => $c->res_fixe_neutre,
                        'res_fixe_terre' => $c->res_fixe_terre,
                        'res_fixe_feu' => $c->res_fixe_feu,
                        'res_fixe_air' => $c->res_fixe_air,
                        'res_fixe_eau' => $c->res_fixe_eau,
                        'res_neutre' => $c->res_neutre,
                        'res_terre' => $c->res_terre,
                        'res_feu' => $c->res_feu,
                        'res_air' => $c->res_air,
                        'res_eau' => $c->res_eau,
                    ];
                }

                return [
                    'id' => $n->id,
                    'creature_id' => $n->creature_id,
                    'story' => $n->story,
                    'historical' => $n->historical,
                    'age' => $n->age,
                    'size' => $n->size,
                    'breed_id' => $n->breed_id,
                    'specialization_id' => $n->specialization_id,
                    'creature_level' => $n->creature?->level,
                    'creature_state' => $n->creature?->state,
                    'creature' => $creature,
                    'breed' => $n->breed ? [
                        'id' => $n->breed->id,
                        'name' => $n->breed->name,
                    ] : null,
                    'specialization' => $n->specialization ? [
                        'id' => $n->specialization->id,
                        'name' => $n->specialization->name,
                    ] : null,
                    'created_at' => $n->created_at?->toISOString(),
                    'updated_at' => $n->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'npcs',
                    'query' => [
                        'search' => $search,
                        'filters' => $filters,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $limit,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => $filterOptions,
                    'characteristics' => [
                        'creature' => [
                            'byDbColumn' => $this->characteristicMeta->buildCreatureByDbColumn(),
                        ],
                    ],
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Npc $n) {
            $showHref = route('entities.npcs.show', $n->id);
            $creatureName = $n->creature?->name ?? '-';
            $breedName = $n->breed?->name ?? '-';
            $specName = $n->specialization?->name ?? '-';

            $createdAtLabel = $n->created_at ? $n->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $n->created_at ? $n->created_at->getTimestamp() : 0;
            $updatedAtLabel = $n->updated_at ? $n->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $n->updated_at ? $n->updated_at->getTimestamp() : 0;

            return [
                'id' => $n->id,
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
                    'breed' => [
                        'type' => 'text',
                        'value' => $breedName,
                        'params' => [
                            'searchValue' => $breedName,
                            'sortValue' => $breedName,
                        ],
                    ],
                    'specialization' => [
                        'type' => 'text',
                        'value' => $specName,
                        'params' => [
                            'searchValue' => $specName,
                            'sortValue' => $specName,
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
                        'id' => $n->id,
                        'creature_id' => $n->creature_id,
                        'story' => $n->story,
                        'historical' => $n->historical,
                        'age' => $n->age,
                        'size' => $n->size,
                        'breed_id' => $n->breed_id,
                        'specialization_id' => $n->specialization_id,
                        'creature' => $n->creature ? [
                            'id' => $n->creature->id,
                            'name' => $n->creature->name,
                        ] : null,
                        'breed' => $n->breed ? [
                            'id' => $n->breed->id,
                            'name' => $n->breed->name,
                        ] : null,
                        'specialization' => $n->specialization ? [
                            'id' => $n->specialization->id,
                            'name' => $n->specialization->name,
                        ] : null,
                    ],
                ],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'npcs',
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


