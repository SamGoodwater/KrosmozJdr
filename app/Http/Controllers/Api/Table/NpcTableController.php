<?php

namespace App\Http\Controllers\Api\Table;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
use App\Http\Resources\Entity\LanguageResource;
use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Npc;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Services\Effect\SpellNestedPreviewSerializer;
use App\Support\Creature\CreatureMasteryColumns;
use App\Support\Creature\CreatureSize;
use App\Support\Npc\NpcRole;
use Illuminate\Database\Eloquent\Builder;
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
    use InterpretsEntityTableFilters;
    use PaginatesEntityTable;

    public function __construct(
        private readonly SpellNestedPreviewSerializer $spellNestedPreviewSerializer,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Npc::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['breed_id', 'specialization_id', 'creature_level', 'creature_hostility', 'state', 'npc_role', 'size'] as $k) {
            if (! array_key_exists($k, $filters) && $request->has($k)) {
                $filters[$k] = $request->get($k);
            }
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $sortsPayload = $request->input('sorts');
        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (is_array($sortsPayload) && isset($sortsPayload[0]) && is_array($sortsPayload[0])) {
            $sort = (string) ($sortsPayload[0]['field'] ?? $sortsPayload[0]['column'] ?? $sort);
            $order = strtolower((string) ($sortsPayload[0]['dir'] ?? $sortsPayload[0]['order'] ?? $order));
        }
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Npc::query()
            ->visibleToUser($request->user())
            ->with([
                'languages',
                'breed',
                'specialization',
                'creature' => fn ($q) => $q->with([
                    'creatureTraits',
                    'spells' => fn ($sq) => $sq
                        ->visibleToUser($request->user())
                        ->orderBy('name')
                        ->with([
                            'spellTypes',
                            'effects.degrees.effectSubEffects.subEffect',
                        ]),
                    'items' => fn ($iq) => $iq
                        ->visibleToUser($request->user())
                        ->orderBy('name')
                        ->with(['itemType:id,name']),
                ]),
            ])
            ->withCount([
                'panoplies' => fn ($q) => $q->visibleToUser($request->user()),
                'campaigns' => fn ($q) => $q->visibleToUser($request->user()),
                'scenarios' => fn ($q) => $q->visibleToUser($request->user()),
            ])
            ->withExists(['shop' => fn ($q) => $q->visibleToUser($request->user())]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('creature', fn ($qq) => $qq
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%"))
                    ->orWhereHas('breed', fn ($qq) => $qq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('specialization', fn ($qq) => $qq->where('name', 'like', "%{$search}%"));
            });
        }

        $this->applyNpcTableFilters($query, $filters);

        $this->applyEntityTableIdList($query, $request);

        $this->applyNpcTableSort($query, $request, $sort, $order);

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $limit = $pageResult['limit'];
        $page = $pageResult['page'];
        $pagination = $pageResult['pagination'];

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
        $visibleNpcs = Npc::query()->visibleToUser($request->user());
        $creatureHostilityOptions = [
            ['value' => '0', 'label' => 'Amical'],
            ['value' => '1', 'label' => 'Curieux'],
            ['value' => '2', 'label' => 'Neutre'],
            ['value' => '3', 'label' => 'Hostile'],
            ['value' => '4', 'label' => 'Agressif'],
        ];
        $filterOptions = [
            'breed_id' => $breedOptions,
            'specialization_id' => $specializationOptions,
            'creature_level' => $this->relatedIntegerColumnBounds(
                $visibleNpcs,
                'creature_id',
                Creature::class,
                'level',
                1,
                200
            ),
            'creature_life' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'life', 0, 500),
            'creature_pa' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'pa', 0, 20),
            'creature_pm' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'pm', 0, 20),
            'creature_po' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'po', 0, 20),
            'creature_ini' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'ini', 0, 200),
            'creature_ca' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'ca', 0, 50),
            'creature_strong' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'strong', 0, 400),
            'creature_intel' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'intel', 0, 400),
            'creature_agi' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'agi', 0, 400),
            'creature_chance' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'chance', 0, 400),
            'creature_vitality' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'vitality', 0, 400),
            'creature_critical_hit' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'critical_hit', 0, 50),
            'creature_heal_bonus' => $this->relatedIntegerColumnBounds($visibleNpcs, 'creature_id', Creature::class, 'heal_bonus', 0, 50),
            'creature_hostility' => $creatureHostilityOptions,
            'state' => EntityState::options(),
            'size' => collect(CreatureSize::LABELS)->map(fn ($label, $value) => [
                'value' => (string) $value,
                'label' => (string) $label,
            ])->values()->all(),
            'npc_role' => collect(NpcRole::LABELS)->map(fn ($label, $value) => [
                'value' => (string) $value,
                'label' => (string) $label,
            ])->values()->all(),
        ];

        // Mode "entities" : retourner les entités brutes (créature complète pour colonnes résumé comme Monster)
        if ($format === 'entities') {
            $entities = $rows->map(function (Npc $n) use ($request) {
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
                        'do_fixe_multiple' => $c->do_fixe_multiple,
                        'do_sagesse' => $c->do_sagesse,
                        'do_vitalite' => $c->do_vitalite,
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
                        'res_sagesse' => $c->res_sagesse,
                        'res_vitalite' => $c->res_vitalite,
                        ...CreatureMasteryColumns::extractFrom($c),
                        'spells' => $c->relationLoaded('spells')
                            ? $c->spells
                                ->map(fn (Spell $s) => $this->spellNestedPreviewSerializer->serialize($s))
                                ->values()
                                ->all()
                            : [],
                        'items' => $c->relationLoaded('items')
                            ? $c->items->map(fn (Item $item) => [
                                'id' => $item->id,
                                'name' => $item->name,
                                'description' => $item->description,
                                'level' => $item->level,
                                'image' => $item->image,
                                'rarity' => $item->rarity,
                                'bonus' => $item->bonus,
                                'item_type_id' => $item->item_type_id,
                                'itemType' => $item->relationLoaded('itemType') && $item->itemType
                                    ? [
                                        'id' => $item->itemType->id,
                                        'name' => $item->itemType->name,
                                    ]
                                    : null,
                                'pivot' => [
                                    'quantity' => $item->pivot->quantity ?? 1,
                                ],
                            ])->values()->all()
                            : [],
                        'creatureTraits' => $c->relationLoaded('creatureTraits')
                            ? $c->creatureTraits->map(fn ($t) => [
                                'id' => $t->id,
                                'name' => $t->name,
                                'description' => $t->description,
                                'image' => $t->image ?? null,
                            ])->values()->all()
                            : [],
                    ];
                }

                return [
                    'id' => $n->id,
                    'creature_id' => $n->creature_id,
                    'story' => $n->story,
                    'historical' => $n->historical,
                    'age' => $n->age,
                    'size' => $n->size,
                    'npc_role' => $n->npc_role,
                    'breed_id' => $n->breed_id,
                    'specialization_id' => $n->specialization_id,
                    'state' => $n->state,
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
                    'languages' => LanguageResource::collection($n->languages)->resolve($request),
                    'panoplies_count' => (int) ($n->panoplies_count ?? 0),
                    'campaigns_count' => (int) ($n->campaigns_count ?? 0),
                    'scenarios_count' => (int) ($n->scenarios_count ?? 0),
                    'has_shop' => (bool) ($n->shop_exists ?? false),
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
                        'page' => $page,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => $filterOptions,
                    'pagination' => $pagination,
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
                        'panoplies_count' => (int) ($n->panoplies_count ?? 0),
                        'campaigns_count' => (int) ($n->campaigns_count ?? 0),
                        'scenarios_count' => (int) ($n->scenarios_count ?? 0),
                        'has_shop' => (bool) ($n->shop_exists ?? false),
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
                'pagination' => $pagination,
            ],
            'rows' => $tableRows,
        ]);
    }

    /**
     * Filtres PNJ (colonnes propres + créature liée).
     *
     * @param  Builder<Npc>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyNpcTableFilters(Builder $query, array $filters): void
    {
        $own = [
            'size' => ['size', 'int'],
            'id' => ['id', 'int'],
            'breed_id' => ['breed_id', 'int'],
            'specialization_id' => ['specialization_id', 'int'],
            'npc_role' => ['npc_role', 'string'],
            'state' => ['state', 'string'],
        ];
        foreach ($own as $key => [$column, $cast]) {
            if ($this->hasFilterValue($filters, $key)) {
                $this->applyEqualityFilter($query, $column, $filters[$key], $cast);
            }
        }

        $creatureRange = [
            'creature_level' => 'level',
            'creature_life' => 'life',
            'creature_pa' => 'pa',
            'creature_pm' => 'pm',
            'creature_po' => 'po',
            'creature_ini' => 'ini',
            'creature_ca' => 'ca',
            'creature_strong' => 'strong',
            'creature_intel' => 'intel',
            'creature_agi' => 'agi',
            'creature_chance' => 'chance',
            'creature_vitality' => 'vitality',
            'creature_critical_hit' => 'critical_hit',
            'creature_heal_bonus' => 'heal_bonus',
        ];
        foreach ($creatureRange as $key => $column) {
            if ($this->hasFilterValue($filters, $key)) {
                $this->applyRelationIntegerRangeFilter($query, 'creature', $column, $filters[$key]);
            }
        }

        if ($this->hasFilterValue($filters, 'creature_hostility')) {
            $this->applyRelationEqualityFilter($query, 'creature', 'hostility', $filters['creature_hostility'], 'int');
        }
    }

    /**
     * Tri PNJ : colonnes SQL + sous-requête créature (nom, niveau, stats).
     *
     * Un JOIN `creatures` rend `state`/`id` ambigus avec `visibleToUser()`.
     *
     * @param  Builder<Npc>  $query
     */
    private function applyNpcTableSort(Builder $query, Request $request, string $sort, string $order): void
    {
        $sortsPayload = $request->input('sorts');
        if (is_array($sortsPayload) && isset($sortsPayload[0]) && is_array($sortsPayload[0])) {
            $sort = (string) ($sortsPayload[0]['field'] ?? $sortsPayload[0]['column'] ?? $sort);
            $order = strtolower((string) ($sortsPayload[0]['dir'] ?? $sortsPayload[0]['order'] ?? $order));
        }
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $creatureSort = [
            'name' => 'name',
            'creature_name' => 'name',
            'creature_level' => 'level',
            'creature_life' => 'life',
            'creature_pa' => 'pa',
            'creature_pm' => 'pm',
            'creature_po' => 'po',
            'creature_ini' => 'ini',
            'creature_ca' => 'ca',
            'creature_hostility' => 'hostility',
            'creature_state' => 'state',
            'creature_location' => 'location',
            'creature_strong' => 'strong',
            'creature_intel' => 'intel',
            'creature_agi' => 'agi',
            'creature_chance' => 'chance',
            'creature_vitality' => 'vitality',
            'creature_critical_hit' => 'critical_hit',
            'creature_heal_bonus' => 'heal_bonus',
        ];

        $allowedOwn = ['id', 'size', 'npc_role', 'breed_id', 'specialization_id', 'created_at', 'updated_at'];
        $aliases = [
            'breed' => 'breed_id',
            'specialization' => 'specialization_id',
        ];
        $ownField = $aliases[$sort] ?? $sort;

        if (array_key_exists($sort, $creatureSort)) {
            $column = $creatureSort[$sort];
            $query->orderBy(
                Creature::query()
                    ->select($column)
                    ->whereColumn('creatures.id', $query->qualifyColumn('creature_id'))
                    ->limit(1),
                $order
            );

            return;
        }

        if (in_array($ownField, $allowedOwn, true)) {
            $query->orderBy($ownField, $order);

            return;
        }

        $query->latest();
    }
}
