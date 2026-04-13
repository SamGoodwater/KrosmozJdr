<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\Type\SpellType;
use App\Services\Effect\SpellEffectDefinitionsSerializer;
use App\Services\Effect\SpellEffectUsagesDataService;
use App\Support\AreaConstants;
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
    use InterpretsEntityTableSort;

    public function __construct(
        private readonly SpellEffectUsagesDataService $spellEffectUsagesDataService,
        private readonly SpellEffectDefinitionsSerializer $spellEffectDefinitionsSerializer
    ) {}

    /**
     * Données communes liste / ligne (effets résolus, définitions + invocations, portée brute).
     *
     * @return array{
     *     effect_usages_summary: string,
     *     effect_usages_chips: list<array<string, mixed>>,
     *     effects_definitions: list<array<string, mixed>>,
     *     po_min: string|null,
     *     po_max: string|null
     * }
     */
    private function buildSpellTableDisplayPayload(Spell $spell): array
    {
        $effectUsagesData = $this->spellEffectUsagesDataService->build($spell);

        return [
            'effect_usages_summary' => $effectUsagesData['summary'],
            'effect_usages_chips' => $effectUsagesData['chips'],
            'effects_definitions' => $this->spellEffectDefinitionsSerializer->serialize($spell->effects ?? collect()),
            'po_min' => $spell->po_min,
            'po_max' => $spell->po_max,
            'resolution_mode' => (string) ($spell->resolution_mode ?? 'attack_roll'),
            'attack_characteristic_key' => $spell->attack_characteristic_key,
            'save_characteristic_key' => $spell->save_characteristic_key,
            'save_dc_formula' => $spell->save_dc_formula,
            'save_success_note' => $spell->save_success_note,
            'auto_success_if_willing_target' => (bool) ($spell->auto_success_if_willing_target ?? false),
        ];
    }

    /** Construit la cellule area (chips avec icône) pour le format cells. */
    private function buildAreaCell(?string $area): array
    {
        if ($area === null || trim($area) === '') {
            return [
                'type' => 'text',
                'value' => '—',
                'params' => ['sortValue' => '', 'searchValue' => ''],
            ];
        }
        $value = (string) $area;

        return [
            'type' => 'chips',
            'value' => '',
            'params' => [
                'items' => [
                    [
                        'icon' => AreaConstants::getIconPath($area),
                        'value' => $value,
                        'tooltip' => 'Zone: '.$value,
                    ],
                ],
                'sortValue' => $value,
                'searchValue' => $value,
            ],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Spell::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['level', 'pa', 'category', 'element', 'is_magic', 'allows_reaction', 'powerful', 'state', 'sight_line', 'po_editable'] as $k) {
            if (! array_key_exists($k, $filters) && $request->has($k)) {
                $filters[$k] = $request->get($k);
            }
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', $request->has('page') ? 25 : 5000);
        $limit = max(1, min($limit, 20000));

        $page = max(1, (int) $request->integer('page', 1));
        $offset = ($page - 1) * $limit;

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

        $query = Spell::query()
            ->with(['createdBy', 'spellTypes', 'effects.degrees.effectSubEffects.subEffect'])
            ->withCount(['spellTypes', 'breeds', 'creatures', 'monsters']);

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
        if (array_key_exists('id', $filters) && $filters['id'] !== '' && $filters['id'] !== null) {
            $query->where('id', (int) $filters['id']);
        }
        if (array_key_exists('category', $filters) && $filters['category'] !== '' && $filters['category'] !== null) {
            $query->where('category', (int) $filters['category']);
        }
        if (array_key_exists('element', $filters) && $filters['element'] !== '' && $filters['element'] !== null) {
            $query->where('element', (int) $filters['element']);
        }
        if (array_key_exists('is_magic', $filters) && $filters['is_magic'] !== '' && $filters['is_magic'] !== null) {
            $query->where('is_magic', (int) $filters['is_magic']);
        }
        if (array_key_exists('allows_reaction', $filters) && $filters['allows_reaction'] !== '' && $filters['allows_reaction'] !== null) {
            $query->where('allows_reaction', (int) $filters['allows_reaction']);
        }
        if (array_key_exists('powerful', $filters) && $filters['powerful'] !== '' && $filters['powerful'] !== null) {
            $query->where('powerful', (int) $filters['powerful']);
        }
        if (array_key_exists('state', $filters) && $filters['state'] !== '' && $filters['state'] !== null) {
            $query->where('state', (string) $filters['state']);
        }
        if (array_key_exists('sight_line', $filters) && $filters['sight_line'] !== '' && $filters['sight_line'] !== null) {
            $query->where('sight_line', (int) $filters['sight_line']);
        }
        if (array_key_exists('po_editable', $filters) && $filters['po_editable'] !== '' && $filters['po_editable'] !== null) {
            $query->where('po_editable', (int) $filters['po_editable']);
        }
        if (array_key_exists('ritual_available', $filters) && $filters['ritual_available'] !== '' && $filters['ritual_available'] !== null) {
            $query->where('ritual_available', (int) $filters['ritual_available']);
        }
        if (array_key_exists('auto_update', $filters) && $filters['auto_update'] !== '' && $filters['auto_update'] !== null) {
            $query->where('auto_update', (int) $filters['auto_update']);
        }

        $allowedSort = ['id', 'name', 'description', 'level', 'pa', 'po', 'area', 'element', 'category', 'is_magic', 'allows_reaction', 'casting_time', 'ritual_available', 'cast_per_turn', 'cast_per_target', 'number_between_two_cast', 'duration', 'sight_line', 'po_editable', 'state', 'auto_update', 'read_level', 'write_level', 'dofusdb_id', 'created_at', 'updated_at'];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $total = $query->count();
        $lastPage = (int) max(1, ceil($total / $limit));
        $rows = $query->skip($offset)->limit($limit)->get();

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
            'area' => collect(AreaConstants::SHAPES)
                ->map(fn (string $shape) => ['value' => $shape, 'label' => AreaConstants::getShapeLabel($shape)])
                ->values()->all(),
            'types' => SpellType::query()->orderBy('name')->get(['id', 'name', 'color', 'icon'])
                ->map(fn (SpellType $t) => [
                    'value' => (string) $t->id,
                    'label' => $t->name,
                    'color' => $t->color,
                    'icon' => $t->icon,
                ])
                ->values()->all(),
            'pa' => [
                ['value' => '1', 'label' => '1'],
                ['value' => '2', 'label' => '2'],
                ['value' => '3', 'label' => '3'],
                ['value' => '4', 'label' => '4'],
                ['value' => '5', 'label' => '5'],
                ['value' => '6', 'label' => '6'],
            ],
            'po' => [
                ['value' => '0', 'label' => '0 (soi)'],
                ['value' => '1', 'label' => '1 (CàC)'],
                ['value' => '2', 'label' => '2'],
                ['value' => '3', 'label' => '3'],
                ['value' => '4', 'label' => '4'],
                ['value' => '5', 'label' => '5'],
                ['value' => '6', 'label' => '6+'],
            ],
            'sub_effect' => SubEffect::query()->orderBy('type_slug')->orderBy('slug')->get(['id', 'slug', 'type_slug'])
                ->map(fn (SubEffect $s) => ['value' => $s->slug, 'label' => $s->slug])
                ->values()->all(),
            'category' => [
                ['value' => '0', 'label' => 'Sort de classe'],
                ['value' => '1', 'label' => 'Sort de créature'],
                ['value' => '2', 'label' => 'Sort apprenable'],
                ['value' => '3', 'label' => 'Sort consommable'],
            ],
            'element' => \App\Support\ElementBitmask::allFilterOptions(),
            'is_magic' => [
                ['value' => '1', 'label' => 'Wakfu'],
                ['value' => '0', 'label' => 'Physique'],
            ],
            'allows_reaction' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'powerful' => [
                ['value' => '0', 'label' => 'Normal'],
                ['value' => '1', 'label' => 'Puissant'],
            ],
            'sight_line' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'po_editable' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'ritual_available' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'auto_update' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'state' => [
                ['value' => 'raw', 'label' => 'Brut'],
                ['value' => 'draft', 'label' => 'Brouillon'],
                ['value' => 'playable', 'label' => 'Jouable'],
                ['value' => 'archived', 'label' => 'Archivé'],
            ],
        ];

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Spell $sp) {
                $createdBy = $sp->createdBy;
                $displayPayload = $this->buildSpellTableDisplayPayload($sp);
                $effectSubEffectSlugs = $sp->effects
                    ->flatMap(fn ($e) => $e->degrees->flatMap(fn ($d) => $d->effectSubEffects))
                    ->map(fn ($ese) => $ese->subEffect?->slug)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return array_merge([
                    'id' => $sp->id,
                    'official_id' => $sp->official_id,
                    'dofusdb_id' => $sp->dofusdb_id,
                    'name' => $sp->name,
                    'description' => $sp->description,
                    'effect' => $sp->effect,
                    'effect_sub_effect_slugs' => $effectSubEffectSlugs,
                    'area' => $sp->area,
                    'level' => $sp->level,
                    'po' => $sp->po_display,
                    'po_editable' => (bool) $sp->po_editable,
                    'pa' => $sp->pa,
                    'casting_time' => $sp->casting_time,
                    'ritual_available' => $sp->ritual_available,
                    'cast_per_turn' => $sp->cast_per_turn,
                    'cast_per_target' => $sp->cast_per_target,
                    'sight_line' => (bool) $sp->sight_line,
                    'number_between_two_cast' => $sp->number_between_two_cast,
                    'duration' => $sp->duration,
                    'element' => $sp->element,
                    'category' => $sp->category,
                    'is_magic' => (bool) $sp->is_magic,
                    'allows_reaction' => (bool) ($sp->allows_reaction ?? false),
                    'powerful' => $sp->powerful,
                    'state' => (string) ($sp->state ?? 'draft'),
                    'read_level' => (int) ($sp->read_level ?? 0),
                    'write_level' => (int) ($sp->write_level ?? 0),
                    'image' => $sp->image,
                    'auto_update' => (bool) $sp->auto_update,
                    'spellTypes' => $sp->spellTypes?->map(fn ($t) => [
                        'id' => $t->id,
                        'name' => $t->name,
                        'color' => $t->color,
                        'icon' => $t->icon,
                    ])->values()->all() ?? [],
                    'spell_types_count' => (int) ($sp->spell_types_count ?? 0),
                    'breeds_count' => (int) ($sp->breeds_count ?? 0),
                    'creatures_count' => (int) ($sp->creatures_count ?? 0),
                    'monsters_count' => (int) ($sp->monsters_count ?? 0),
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $sp->created_at?->toISOString(),
                    'updated_at' => $sp->updated_at?->toISOString(),
                ], $displayPayload);
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
                        'page' => $page,
                    ],
                    'pagination' => [
                        'total' => $total,
                        'perPage' => $limit,
                        'currentPage' => $page,
                        'lastPage' => $lastPage,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => $filterOptions,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

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

            $displayPayload = $this->buildSpellTableDisplayPayload($sp);

            return [
                'id' => $sp->id,
                'cells' => [
                    'id' => [
                        'type' => 'text',
                        'value' => (string) $sp->id,
                        'params' => ['sortValue' => $sp->id, 'filterValue' => (string) $sp->id],
                    ],
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $sp->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $sp->name,
                            'sortValue' => (string) $sp->name,
                        ],
                    ],
                    'description' => [
                        'type' => 'text',
                        'value' => $sp->description ?: '-',
                        'params' => ['searchValue' => (string) ($sp->description ?? ''), 'sortValue' => (string) ($sp->description ?? '')],
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
                        'value' => $sp->po_display ?: '-',
                        'params' => [
                            'sortValue' => (string) ($sp->po_display ?? ''),
                        ],
                    ],
                    'area' => $this->buildAreaCell($sp->area),
                    'element' => [
                        'type' => 'badge',
                        'value' => $sp->element !== null ? (string) $sp->element : '-',
                        'params' => ['filterValue' => (string) ($sp->element ?? ''), 'sortValue' => $sp->element ?? 0],
                    ],
                    'category' => [
                        'type' => 'badge',
                        'value' => $sp->category !== null ? (string) $sp->category : '-',
                        'params' => ['filterValue' => (string) ($sp->category ?? ''), 'sortValue' => $sp->category ?? 0],
                    ],
                    'spell_types' => [
                        'type' => 'text',
                        'value' => $typesLabel,
                        'params' => [
                            'searchValue' => $typesLabel,
                            'sortValue' => $typesLabel,
                        ],
                    ],
                    'is_magic' => [
                        'type' => 'badge',
                        'value' => $sp->is_magic ? 'Wakfu' : 'Physique',
                        'params' => ['filterValue' => $sp->is_magic ? '1' : '0', 'sortValue' => $sp->is_magic ? 1 : 0],
                    ],
                    'allows_reaction' => [
                        'type' => 'badge',
                        'value' => $sp->allows_reaction ? 'Oui' : 'Non',
                        'params' => ['filterValue' => $sp->allows_reaction ? '1' : '0', 'sortValue' => $sp->allows_reaction ? 1 : 0],
                    ],
                    'casting_time' => [
                        'type' => 'text',
                        'value' => $sp->casting_time ?: '-',
                        'params' => ['sortValue' => (string) ($sp->casting_time ?? '')],
                    ],
                    'ritual_available' => [
                        'type' => 'badge',
                        'value' => $sp->ritual_available ? 'Oui' : 'Non',
                        'params' => ['filterValue' => $sp->ritual_available ? '1' : '0', 'sortValue' => $sp->ritual_available ? 1 : 0],
                    ],
                    'cast_per_turn' => [
                        'type' => 'text',
                        'value' => $sp->cast_per_turn ?: '-',
                        'params' => ['sortValue' => (string) ($sp->cast_per_turn ?? '')],
                    ],
                    'cast_per_target' => [
                        'type' => 'text',
                        'value' => $sp->cast_per_target ?: '-',
                        'params' => ['sortValue' => (string) ($sp->cast_per_target ?? '')],
                    ],
                    'number_between_two_cast' => [
                        'type' => 'text',
                        'value' => $sp->number_between_two_cast ?: '-',
                        'params' => ['sortValue' => (string) ($sp->number_between_two_cast ?? '')],
                    ],
                    'duration' => [
                        'type' => 'text',
                        'value' => $sp->duration ?: '-',
                        'params' => ['sortValue' => (string) ($sp->duration ?? '')],
                    ],
                    'sight_line' => [
                        'type' => 'badge',
                        'value' => $sp->sight_line ? 'Oui' : 'Non',
                        'params' => ['sortValue' => $sp->sight_line ? 1 : 0, 'filterValue' => $sp->sight_line ? '1' : '0'],
                    ],
                    'po_editable' => [
                        'type' => 'badge',
                        'value' => $sp->po_editable ? 'Oui' : 'Non',
                        'params' => ['sortValue' => $sp->po_editable ? 1 : 0, 'filterValue' => $sp->po_editable ? '1' : '0'],
                    ],
                    'state' => [
                        'type' => 'badge',
                        'value' => (string) ($sp->state ?? 'draft'),
                        'params' => ['filterValue' => (string) ($sp->state ?? ''), 'sortValue' => (string) ($sp->state ?? '')],
                    ],
                    'auto_update' => [
                        'type' => 'badge',
                        'value' => $sp->auto_update ? 'Oui' : 'Non',
                        'params' => ['sortValue' => $sp->auto_update ? 1 : 0],
                    ],
                    'image' => [
                        'type' => 'thumb',
                        'value' => $sp->image ?: '',
                        'params' => ['sortValue' => $sp->image ? 1 : 0],
                    ],
                    'read_level' => [
                        'type' => 'badge',
                        'value' => (string) ($sp->read_level ?? 0),
                        'params' => ['sortValue' => (int) ($sp->read_level ?? 0)],
                    ],
                    'write_level' => [
                        'type' => 'badge',
                        'value' => (string) ($sp->write_level ?? 0),
                        'params' => ['sortValue' => (int) ($sp->write_level ?? 0)],
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
                    'entity' => array_merge([
                        'id' => $sp->id,
                        'official_id' => $sp->official_id,
                        'dofusdb_id' => $sp->dofusdb_id,
                        'name' => $sp->name,
                        'description' => $sp->description,
                        'effect' => $sp->effect,
                        'area' => $sp->area,
                        'level' => $sp->level,
                        'po' => $sp->po_display,
                        'po_editable' => (bool) $sp->po_editable,
                        'pa' => $sp->pa,
                        'casting_time' => $sp->casting_time,
                        'ritual_available' => $sp->ritual_available,
                        'cast_per_turn' => $sp->cast_per_turn,
                        'cast_per_target' => $sp->cast_per_target,
                        'sight_line' => (bool) $sp->sight_line,
                        'number_between_two_cast' => $sp->number_between_two_cast,
                        'duration' => $sp->duration,
                        'element' => $sp->element,
                        'category' => $sp->category,
                        'is_magic' => (bool) $sp->is_magic,
                        'allows_reaction' => (bool) ($sp->allows_reaction ?? false),
                        'powerful' => $sp->powerful,
                        'state' => (string) ($sp->state ?? 'draft'),
                        'read_level' => (int) ($sp->read_level ?? 0),
                        'write_level' => (int) ($sp->write_level ?? 0),
                        'image' => $sp->image,
                        'auto_update' => (bool) $sp->auto_update,
                        'spellTypes' => $sp->spellTypes?->map(fn ($t) => [
                            'id' => $t->id,
                            'name' => $t->name,
                            'color' => $t->color,
                            'icon' => $t->icon,
                        ])->values()->all() ?? [],
                        'spell_types_count' => (int) ($sp->spell_types_count ?? 0),
                        'breeds_count' => (int) ($sp->breeds_count ?? 0),
                        'creatures_count' => (int) ($sp->creatures_count ?? 0),
                        'monsters_count' => (int) ($sp->monsters_count ?? 0),
                        'createdBy' => $createdBy ? [
                            'id' => $createdBy->id,
                            'name' => $createdBy->name,
                            'email' => $createdBy->email,
                        ] : null,
                    ], $displayPayload),
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
                    'page' => $page,
                ],
                'pagination' => [
                    'total' => $total,
                    'perPage' => $limit,
                    'currentPage' => $page,
                    'lastPage' => $lastPage,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
            ],
            'rows' => $tableRows,
        ]);
    }
}
