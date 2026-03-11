<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\Type\SpellType;
use App\Services\Characteristic\CharacteristicMetaByDbColumnService;
use App\Support\AreaConstants;
use App\Services\Effect\EffectResolutionService;
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
    public function __construct(
        private readonly CharacteristicMetaByDbColumnService $characteristicMeta,
        private readonly EffectResolutionService $effectResolutionService
    ) {
    }

    /** Slugs élément → id (0=Neutre, 1=Terre, 2=Feu, 3=Air, 4=Eau). */
    private const ELEMENT_SLUG_TO_ID = [
        'neutral' => 0,
        'earth' => 1,
        'fire' => 2,
        'air' => 3,
        'water' => 4,
    ];

    /** Labels pour target_type. */
    private const TARGET_TYPE_LABELS = [
        'direct' => 'Direct',
        'trap' => 'Piège',
        'glyph' => 'Glyphe',
    ];

    /**
     * Construit le résumé texte (pour recherche/tri) et les chips structurés (pour affichage).
     *
     * @return array{summary: string, chips: list<array{text: string, degree: int|null, element: int, element_label: string, target_type: string, target_label: string, area: string|null, duration: int|null, duration_label: string, tooltip: string}>}
     */
    private function buildEffectUsagesData(Spell $spell): array
    {
        $usages = $spell->effectUsages ?? collect();
        if ($usages->isEmpty()) {
            return ['summary' => '', 'chips' => []];
        }

        $level = is_numeric((string) $spell->level) ? (int) $spell->level : 1;
        $baseContext = ['level' => $level];
        $parts = [];
        $chips = [];

        foreach ($usages->sortBy('level_min') as $usage) {
            $effect = $usage->effect;
            if (! $effect) {
                continue;
            }
            $degree = $effect->degree;
            $degreePrefix = $degree !== null ? 'D' . $degree . ' ' : '';
            $targetType = $effect->target_type ?? 'direct';
            $targetLabel = self::TARGET_TYPE_LABELS[$targetType] ?? 'Direct';
            $area = $effect->area;

            $resolved = $this->effectResolutionService->resolveEffect($effect, $baseContext, null, false, false);
            foreach ($resolved['sub_effects'] ?? [] as $sub) {
                $text = trim((string) ($sub['text'] ?? ''));
                if ($text === '') {
                    continue;
                }
                $text = $this->humanizeEffectText($text);
                $parts[] = $degreePrefix . $text;

                $charSlug = strtolower((string) ($sub['characteristic'] ?? ''));
                $elementId = self::ELEMENT_SLUG_TO_ID[$charSlug] ?? 0;
                $elementLabel = $this->elementIdToLabel($elementId);

                $duration = isset($sub['duration']) && is_numeric($sub['duration']) ? (int) $sub['duration'] : null;
                $durationLabel = $this->formatDurationLabel($duration);

                $details = [];
                if ($degree !== null) {
                    $details[] = 'Degré ' . $degree;
                }
                if ($targetType !== 'direct') {
                    $details[] = $targetLabel;
                }
                if ($area !== null && (string) $area !== '') {
                    $details[] = "zone {$area}";
                }
                $details[] = $durationLabel;
                $displayText = $degreePrefix . $text;
                $tooltip = $displayText . (\count($details) > 0 ? ' — ' . implode(', ', $details) : '');

                $chips[] = [
                    'text' => $displayText,
                    'degree' => $degree,
                    'element' => $elementId,
                    'element_label' => $elementLabel,
                    'target_type' => $targetType,
                    'target_label' => $targetLabel,
                    'area' => $area,
                    'duration' => $duration,
                    'duration_label' => $durationLabel,
                    'tooltip' => $tooltip,
                ];
            }
        }

        return [
            'summary' => implode(' • ', $parts),
            'chips' => $chips,
        ];
    }

    /** Traduit duration 0 ou 1 en "Immédiat", sinon "X tour(s)". */
    private function formatDurationLabel(?int $duration): string
    {
        if ($duration === null) {
            return 'Immédiat';
        }
        if ($duration === 0 || $duration === 1) {
            return 'Immédiat';
        }
        return $duration . ' tour' . ($duration > 1 ? 's' : '');
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
                        'tooltip' => 'Zone: ' . $value,
                    ],
                ],
                'sortValue' => $value,
                'searchValue' => $value,
            ],
        ];
    }

    private function elementIdToLabel(int $id): string
    {
        return match ($id) {
            1 => 'Terre',
            2 => 'Feu',
            3 => 'Air',
            4 => 'Eau',
            default => 'Neutre',
        };
    }

    /** Remplace les slugs d'éléments par les libellés français. */
    private function humanizeEffectText(string $text): string
    {
        $elementLabels = [
            'water' => 'Eau',
            'earth' => 'Terre',
            'fire' => 'Feu',
            'air' => 'Air',
            'neutral' => 'Neutre',
        ];
        foreach ($elementLabels as $slug => $label) {
            $text = preg_replace('/\b' . preg_quote($slug, '/') . '\b/i', $label, $text);
        }
        return $text;
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
        foreach (['level', 'pa', 'category', 'element', 'is_magic', 'powerful', 'state'] as $k) {
            if (!array_key_exists($k, $filters) && $request->has($k)) {
                $filters[$k] = $request->get($k);
            }
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', $request->has('page') ? 25 : 5000);
        $limit = max(1, min($limit, 20000));

        $page = max(1, (int) $request->integer('page', 1));
        $offset = ($page - 1) * $limit;

        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Spell::query()
            ->with(['createdBy', 'spellTypes', 'effectUsages.effect.effectSubEffects.subEffect'])
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
        if (array_key_exists('powerful', $filters) && $filters['powerful'] !== '' && $filters['powerful'] !== null) {
            $query->where('powerful', (int) $filters['powerful']);
        }
        if (array_key_exists('state', $filters) && $filters['state'] !== '' && $filters['state'] !== null) {
            $query->where('state', (string) $filters['state']);
        }

        $allowedSort = ['id', 'name', 'level', 'pa', 'po', 'area', 'element', 'category', 'dofusdb_id', 'created_at', 'updated_at', 'state'];
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

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
            'types' => SpellType::query()->orderBy('name')->get(['id', 'name'])
                ->map(fn (SpellType $t) => ['value' => (string) $t->id, 'label' => $t->name])
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
                ['value' => '1', 'label' => '1 (CAC)'],
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
            'element' => collect(\App\Models\Entity\Spell::ELEMENT)
                ->map(fn (string $label, int $value) => ['value' => (string) $value, 'label' => $label])
                ->values()
                ->all(),
            'is_magic' => [
                ['value' => '1', 'label' => 'Magique'],
                ['value' => '0', 'label' => 'Physique'],
            ],
            'powerful' => [
                ['value' => '0', 'label' => 'Normal'],
                ['value' => '1', 'label' => 'Puissant'],
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
                $effectUsagesData = $this->buildEffectUsagesData($sp);
                $effectSubEffectSlugs = $sp->effectUsages
                    ->flatMap(fn ($u) => $u->effect?->effectSubEffects ?? collect())
                    ->map(fn ($ese) => $ese->subEffect?->slug)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
                return [
                    'id' => $sp->id,
                    'official_id' => $sp->official_id,
                    'dofusdb_id' => $sp->dofusdb_id,
                    'name' => $sp->name,
                    'description' => $sp->description,
                    'effect' => $sp->effect,
                    'effect_usages_summary' => $effectUsagesData['summary'],
                    'effect_usages_chips' => $effectUsagesData['chips'],
                    'effect_sub_effect_slugs' => $effectSubEffectSlugs,
                    'area' => $sp->area,
                    'level' => $sp->level,
                    'po' => $sp->po_display,
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
                    'state' => (string) ($sp->state ?? 'draft'),
                    'read_level' => (int) ($sp->read_level ?? 0),
                    'write_level' => (int) ($sp->write_level ?? 0),
                    'image' => $sp->image,
                    'auto_update' => (bool) $sp->auto_update,
                    'spellTypes' => $sp->spellTypes?->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])->values()->all() ?? [],
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
                    'characteristics' => [
                        'spell' => [
                            'byDbColumn' => $this->characteristicMeta->buildSpellByDbColumn(),
                        ],
                    ],
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
                        'value' => $sp->po_display ?: '-',
                        'params' => [
                            'sortValue' => (string) ($sp->po_display ?? ''),
                        ],
                    ],
                    'area' => $this->buildAreaCell($sp->area),
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
                        'po' => $sp->po_display,
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
                        'state' => (string) ($sp->state ?? 'draft'),
                        'read_level' => (int) ($sp->read_level ?? 0),
                        'write_level' => (int) ($sp->write_level ?? 0),
                        'image' => $sp->image,
                        'auto_update' => (bool) $sp->auto_update,
                        'spellTypes' => $sp->spellTypes?->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])->values()->all() ?? [],
                        'spell_types_count' => (int) ($sp->spell_types_count ?? 0),
                        'breeds_count' => (int) ($sp->breeds_count ?? 0),
                        'creatures_count' => (int) ($sp->creatures_count ?? 0),
                        'monsters_count' => (int) ($sp->monsters_count ?? 0),
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


