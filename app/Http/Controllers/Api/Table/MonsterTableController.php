<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Support\Creature\CreatureMasteryColumns;
use App\Http\Resources\Entity\LanguageResource;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\Type\MonsterRace;
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
 *   - `entities[]` : monstre + créature (stats) + sorts **allégés** (pas d’arbre d’effets)
 *   - `meta.entityType` = `monsters`
 *   - `meta.query` = paramètres réellement appliqués
 *   - `meta.capabilities` = droits de l'utilisateur courant
 *   - `meta.filterOptions` = options pour les filtres (taille, boss)
 */
class MonsterTableController extends Controller
{
    use PaginatesEntityTable;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Monster::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['size', 'is_boss', 'monster_race_id'] as $k) {
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
                        // Sorts allégés (pas d’arbre effets) : liste + hover Minimal basique.
                        'spells' => fn ($sq) => $sq
                            ->orderBy('name')
                            ->with(['spellTypes']),
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

        if (array_key_exists('size', $filters) && $filters['size'] !== '' && $filters['size'] !== null) {
            $query->where('size', (int) $filters['size']);
        }
        if (array_key_exists('is_boss', $filters) && $filters['is_boss'] !== '' && $filters['is_boss'] !== null) {
            $query->where('is_boss', (int) $filters['is_boss']);
        }
        if (array_key_exists('id', $filters) && $filters['id'] !== '' && $filters['id'] !== null) {
            $query->where('id', (int) $filters['id']);
        }
        if (array_key_exists('monster_race_id', $filters) && $filters['monster_race_id'] !== '' && $filters['monster_race_id'] !== null) {
            $query->where('monster_race_id', (int) $filters['monster_race_id']);
        }

        // Liste blanche de tri : id, size, is_boss, dofusdb_id, dates, nom de créature (name ou creature_name).
        $allowedSort = ['id', 'size', 'is_boss', 'boss_pa', 'dofusdb_id', 'created_at', 'updated_at', 'name', 'creature_name'];

        if ($sort === 'name' || $sort === 'creature_name') {
            // Tri par nom de créature (alphabétique)
            $query->join('creatures', 'monsters.creature_id', '=', 'creatures.id')
                ->orderBy('creatures.name', $order)
                ->select('monsters.*');
        } elseif (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        // Whitelist / blacklist d'ids (utiles pour le moteur de recherche)
        $whitelist = $request->input('whitelist', $request->input('ids', []));
        $blacklist = $request->input('blacklist', $request->input('exclude', []));

        $whitelistIds = collect((array) $whitelist)
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->values()
            ->all();

        $blacklistIds = collect((array) $blacklist)
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->values()
            ->all();

        if (! empty($whitelistIds)) {
            $query->whereIn('id', $whitelistIds);
        }

        if (! empty($blacklistIds)) {
            $query->whereNotIn('id', $blacklistIds);
        }

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
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(fn (MonsterRace $race) => [
                'value' => (string) $race->id,
                'label' => (string) $race->name,
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
        $creatureStateOptions = [
            ['value' => Creature::STATE_RAW, 'label' => 'Brouillon (raw)'],
            ['value' => Creature::STATE_DRAFT, 'label' => 'Brouillon'],
            ['value' => Creature::STATE_PLAYABLE, 'label' => 'Jouable'],
            ['value' => Creature::STATE_ARCHIVED, 'label' => 'Archivé'],
        ];
        $toDistinctOptions = function ($values, $sort = true) {
            $collected = collect($values)->filter(fn ($v) => $v !== null && $v !== '')->map(fn ($v) => (string) $v)->unique()->values();
            if ($sort) {
                $collected = $collected->sort(SORT_NATURAL)->values();
            }

            return $collected->map(fn ($v) => ['value' => $v, 'label' => $v])->all();
        };
        $filterOptions = [
            'size' => collect(Monster::SIZE)->map(fn ($label, $value) => ['value' => (string) $value, 'label' => (string) $label])->values()->all(),
            'is_boss' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'monster_race_id' => $monsterRaceOptions,
            'creature_level' => $toDistinctOptions($rows->pluck('creature.level')),
            'creature_life' => $toDistinctOptions($rows->pluck('creature.life')),
            'creature_pa' => $toDistinctOptions($rows->pluck('creature.pa')),
            'creature_pm' => $toDistinctOptions($rows->pluck('creature.pm')),
            'creature_po' => $toDistinctOptions($rows->pluck('creature.po')),
            'creature_ini' => $toDistinctOptions($rows->pluck('creature.ini')),
            'creature_ca' => $toDistinctOptions($rows->pluck('creature.ca')),
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
                            ? $c->spells->map(fn (Spell $s) => [
                                'id' => $s->id,
                                'name' => $s->name,
                                'description' => $s->description,
                                'level' => $s->level,
                                'pa' => $s->pa,
                                'po_min' => $s->po_min,
                                'po_max' => $s->po_max,
                                'image' => $s->image,
                                'category' => $s->category,
                                'element' => $s->element,
                                'is_magic' => $s->is_magic,
                                'sight_line' => $s->sight_line,
                                'po_editable' => $s->po_editable,
                                'effect' => $s->effect ?? null,
                                'spellTypes' => $s->relationLoaded('spellTypes')
                                    ? $s->spellTypes->map(fn ($t) => [
                                        'id' => $t->id,
                                        'name' => $t->name,
                                        'color' => $t->color ?? null,
                                        'icon' => $t->icon ?? null,
                                    ])->values()->all()
                                    : [],
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
