<?php

namespace App\Http\Controllers\Api\Table;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
use App\Http\Resources\Entity\LanguageResource;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\Type\MonsterRace;
use App\Services\Effect\SpellNestedPreviewSerializer;
use App\Support\Creature\CreatureMasteryColumns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * MonsterTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les monstres.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 *
 * Contrat commun (utilisé par le moteur de recherche d'entités) :
 * - Paramètres acceptés :
 *   - `search` : recherche texte (id Dofus, nom de la créature, nom de la race)
 *   - `filters[size]`, `filters[is_boss]`, `filters[id]` (+ équivalents plats `size`, `is_boss`, `id`)
 *   - `limit` : nombre max de résultats (1..20000)
 *   - `sort` : colonne de tri (`id`, `size`, `is_boss`, `dofusdb_id`, `created_at`, `updated_at`, `name`)
 *   - `order` : `asc` ou `desc`
 *   - `format` : `cells` (défaut) ou `entities` (renvoie `entities[]`)
 *   - `whitelist` / `ids[]` : liste d'ids à inclure uniquement
 *   - `blacklist` / `exclude[]` : liste d'ids à exclure
 * - Réponse `format=entities` :
 *   - `entities[]` : monstre + créature (stats) + sorts liés (chips d’effets) + équipements allégés
 *   - `meta.entityType` = `monsters`
 *   - `meta.query` = paramètres réellement appliqués
 *   - `meta.capabilities` = droits de l'utilisateur courant
 *   - `meta.filterOptions` = options pour les filtres (taille, boss)
 */
class MonsterTableController extends Controller
{
    use InterpretsEntityTableFilters;
    use PaginatesEntityTable;

    public function __construct(
        private readonly SpellNestedPreviewSerializer $spellNestedPreviewSerializer,
    ) {}

    /**
     * Filtres monstres (colonnes propres + créature liée).
     *
     * @param  Builder<Monster>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyMonsterTableFilters(Builder $query, array $filters): void
    {
        $own = [
            'size' => ['size', 'int'],
            'is_boss' => ['is_boss', 'int'],
            'id' => ['id', 'int'],
            'monster_race_id' => ['monster_race_id', 'int'],
        ];
        foreach ($own as $key => [$column, $cast]) {
            if ($this->hasFilterValue($filters, $key)) {
                $this->applyEqualityFilter($query, $column, $filters[$key], $cast);
            }
        }

        $creature = [
            'creature_hostility' => ['hostility', 'int'],
            'creature_state' => ['state', 'string'],
            'creature_location' => ['location', 'string'],
        ];
        foreach ($creature as $key => [$column, $cast]) {
            if ($this->hasFilterValue($filters, $key)) {
                $this->applyRelationEqualityFilter($query, 'creature', $column, $filters[$key], $cast);
            }
        }

        $creatureRanges = [
            'creature_level' => 'level',
            'creature_pa' => 'pa',
            'creature_pm' => 'pm',
            'creature_po' => 'po',
            'creature_life' => 'life',
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
        foreach ($creatureRanges as $key => $column) {
            if ($this->normalizeRangeBounds($filters[$key] ?? null) !== null) {
                $this->applyRelationIntegerRangeFilter($query, 'creature', $column, $filters[$key]);
            } elseif ($this->hasFilterValue($filters, $key)) {
                $this->applyRelationEqualityFilter($query, 'creature', $column, $filters[$key]);
            }
        }
    }

    /**
     * Tri monstres : colonnes SQL + sous-requête créature (nom, niveau, stats).
     *
     * Un JOIN `creatures` rend `state`/`id` ambigus avec `visibleToUser()`.
     *
     * @param  Builder<Monster>  $query
     */
    private function applyMonsterTableSort(Builder $query, Request $request, string $sort, string $order): void
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

        $allowedOwn = ['id', 'size', 'is_boss', 'boss_pa', 'dofusdb_id', 'monster_race_id', 'created_at', 'updated_at'];
        $aliases = ['monster_race' => 'monster_race_id'];
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

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Monster::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['size', 'is_boss', 'monster_race_id', 'creature_level', 'creature_state', 'creature_hostility'] as $k) {
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

        $query = Monster::query()
            ->visibleToUser($request->user())
            ->with([
                'languages',
                'creature' => fn ($q) => $q
                    ->with([
                        'creatureTraits',
                        // Sorts liés visibles du viewer : méta + chips d’effets (vue minimale), sans dump de l’arbre.
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
                    ])
                    ->withCount(['resources', 'items', 'consumables']),
                'monsterRace',
            ])
            ->withCount(['spellInvocations', 'campaigns', 'scenarios']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('dofusdb_id', 'like', "%{$search}%")
                    ->orWhereHas('creature', fn ($qq) => $qq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('monsterRace', fn ($qq) => $qq->where('name', 'like', "%{$search}%"));
            });
        }

        $this->applyMonsterTableFilters($query, $filters);
        $this->applyMonsterTableSort($query, $request, $sort, $order);
        $this->applyEntityTableIdList($query, $request);

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $limit = $pageResult['limit'];
        $page = $pageResult['page'];
        $pagination = $pageResult['pagination'];

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Monster::class),
            'createAny' => Gate::allows('createAny', Monster::class),
            'updateAny' => Gate::allows('updateAny', Monster::class),
            'deleteAny' => Gate::allows('deleteAny', Monster::class),
            'manageAny' => Gate::allows('manageAny', Monster::class),
        ];

        $monsterRaceOptions = MonsterRace::query()
            ->select(['id', 'name', 'show_in_catalog'])
            ->orderBy('name')
            ->get()
            ->map(fn (MonsterRace $race) => [
                'value' => (string) $race->id,
                'label' => (string) $race->name,
                'show_in_catalog' => (bool) $race->show_in_catalog,
            ])
            ->values()
            ->all();

        $creatureHostilityOptions = [
            ['value' => '0', 'label' => 'Amical'],
            ['value' => '1', 'label' => 'Curieux'],
            ['value' => '2', 'label' => 'Neutre'],
            ['value' => '3', 'label' => 'Hostile'],
            ['value' => '4', 'label' => 'Agressif'],
        ];
        $creatureStateOptions = EntityState::options();
        $filterOptions = [
            'size' => collect(Monster::SIZE)->map(fn ($label, $value) => ['value' => (string) $value, 'label' => (string) $label])->values()->all(),
            'is_boss' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'monster_race_id' => $monsterRaceOptions,
            'creature_level' => $this->integerColumnBounds(Creature::query(), 'level', 1, 200),
            'creature_life' => $this->integerColumnBounds(Creature::query(), 'life', 0, 1000),
            'creature_pa' => $this->integerColumnBounds(Creature::query(), 'pa', 0, 20),
            'creature_pm' => $this->integerColumnBounds(Creature::query(), 'pm', 0, 20),
            'creature_po' => $this->integerColumnBounds(Creature::query(), 'po', 0, 20),
            'creature_ini' => $this->integerColumnBounds(Creature::query(), 'ini', 0, 50),
            'creature_ca' => $this->integerColumnBounds(Creature::query(), 'ca', 0, 30),
            'creature_strong' => $this->integerColumnBounds(Creature::query(), 'strong', 0, 30),
            'creature_intel' => $this->integerColumnBounds(Creature::query(), 'intel', 0, 30),
            'creature_agi' => $this->integerColumnBounds(Creature::query(), 'agi', 0, 30),
            'creature_chance' => $this->integerColumnBounds(Creature::query(), 'chance', 0, 30),
            'creature_vitality' => $this->integerColumnBounds(Creature::query(), 'vitality', 0, 30),
            'creature_critical_hit' => $this->integerColumnBounds(Creature::query(), 'critical_hit', 0, 3),
            'creature_heal_bonus' => $this->integerColumnBounds(Creature::query(), 'heal_bonus', 0, 20),
            'creature_hostility' => $creatureHostilityOptions,
            'creature_state' => $creatureStateOptions,
        ];

        // Mode "entities" : retourner les entités brutes (monstre + créature complète pour le tableau)
        if ($format === 'entities') {
            $entities = $rows->map(function (Monster $m) use ($request) {
                $creature = null;
                if ($m->creature) {
                    $c = $m->creature;
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
                        'critical_hit' => $c->critical_hit ?? 0,
                        'heal_bonus' => $c->heal_bonus ?? 0,
                        'save_vitality_bonus' => $c->save_vitality_bonus ?? 0,
                        'save_wisdom_bonus' => $c->save_wisdom_bonus ?? 0,
                        'save_strength_bonus' => $c->save_strength_bonus ?? 0,
                        'save_intelligence_bonus' => $c->save_intelligence_bonus ?? 0,
                        'save_chance_bonus' => $c->save_chance_bonus ?? 0,
                        'save_agility_bonus' => $c->save_agility_bonus ?? 0,
                        'save_vitality_mastery' => $c->save_vitality_mastery ?? 0,
                        'save_wisdom_mastery' => $c->save_wisdom_mastery ?? 0,
                        'save_strength_mastery' => $c->save_strength_mastery ?? 0,
                        'save_intelligence_mastery' => $c->save_intelligence_mastery ?? 0,
                        'save_chance_mastery' => $c->save_chance_mastery ?? 0,
                        'save_agility_mastery' => $c->save_agility_mastery ?? 0,
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
                    'creature' => $creature,
                    'monsterRace' => $m->monsterRace ? [
                        'id' => $m->monsterRace->id,
                        'name' => $m->monsterRace->name,
                    ] : null,
                    'spell_invocations_count' => (int) ($m->spell_invocations_count ?? 0),
                    'resources_count' => (int) ($m->creature?->resources_count ?? 0),
                    'items_count' => (int) ($m->creature?->items_count ?? 0),
                    'consumables_count' => (int) ($m->creature?->consumables_count ?? 0),
                    'campaigns_count' => (int) ($m->campaigns_count ?? 0),
                    'scenarios_count' => (int) ($m->scenarios_count ?? 0),
                    'languages' => LanguageResource::collection($m->languages)->resolve($request),
                    'created_at' => $m->created_at?->toISOString(),
                    'updated_at' => $m->updated_at?->toISOString(),
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

        $tableRows = $rows->map(function (Monster $m) use ($request) {
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
                            'creatureTraits' => $m->creature->relationLoaded('creatureTraits')
                                ? $m->creature->creatureTraits->map(fn ($t) => [
                                    'id' => $t->id,
                                    'name' => $t->name,
                                ])->values()->all()
                                : [],
                        ] : null,
                        'monsterRace' => $m->monsterRace ? [
                            'id' => $m->monsterRace->id,
                            'name' => $m->monsterRace->name,
                        ] : null,
                        'spell_invocations_count' => (int) ($m->spell_invocations_count ?? 0),
                        'resources_count' => (int) ($m->creature?->resources_count ?? 0),
                        'items_count' => (int) ($m->creature?->items_count ?? 0),
                        'consumables_count' => (int) ($m->creature?->consumables_count ?? 0),
                        'campaigns_count' => (int) ($m->campaigns_count ?? 0),
                        'scenarios_count' => (int) ($m->scenarios_count ?? 0),
                        'languages' => LanguageResource::collection($m->languages)->resolve($request),
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
                    'page' => $page,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
                'pagination' => $pagination,
            ],
            'rows' => $tableRows,
        ]);
    }
}
