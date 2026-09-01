<?php

namespace App\Http\Controllers\Api\Table;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
use App\Models\Entity\Capability;
use App\Services\Characteristic\CharacteristicMetaByDbColumnService;
use App\Support\ElementBitmask;
use App\Support\ElementConstants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * CapabilityTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les capacités.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class CapabilityTableController extends Controller
{
    use InterpretsEntityTableFilters;
    use InterpretsEntityTableSort;
    use PaginatesEntityTable;

    /**
     * Cellule d'affichage du statut passif, pilotée par la caractéristique `is_passive`.
     *
     * @param  array<string, mixed>|null  $definition
     * @return array<string, mixed>
     */
    private function buildPassiveCell(bool $isPassive, ?array $definition): array
    {
        if (! $isPassive && (bool) ($definition['hide_when_false'] ?? true)) {
            return [
                'type' => 'text',
                'value' => '',
                'params' => ['filterValue' => '0', 'sortValue' => 0, 'searchValue' => ''],
            ];
        }

        $override = collect($definition['value_overrides'] ?? [])
            ->first(fn ($row) => is_array($row) && ($row['value'] ?? null) === true);
        $available = collect($definition['value_available'] ?? [])
            ->first(fn ($row) => is_array($row) && ($row['value'] ?? null) === true);

        $label = (string) (
            ($available['label'] ?? null)
            ?: ($definition['short_name'] ?? null)
            ?: ($definition['name'] ?? null)
            ?: 'Passif'
        );
        $descriptions = $definition['descriptions'] ?? '';
        $descriptionText = is_array($descriptions) ? implode(' ', $descriptions) : (string) $descriptions;
        $subtitle = is_array($override) ? (string) ($override['subtitle'] ?? '') : '';

        return [
            'type' => 'chips',
            'value' => '',
            'params' => [
                'items' => [[
                    'key' => $definition['key'] ?? 'is_passive',
                    'icon' => is_array($override) ? ($override['icon'] ?? ($definition['icon'] ?? '')) : ($definition['icon'] ?? ''),
                    'color' => is_array($override) ? ($override['color'] ?? ($definition['color'] ?? '')) : ($definition['color'] ?? ''),
                    'name' => $definition['name'] ?? $label,
                    'shortLabel' => $definition['short_name'] ?? $label,
                    'value' => $label,
                    'tooltip' => $subtitle ?: (($definition['helper'] ?? '') ?: $descriptionText),
                    'helper' => $definition['helper'] ?? '',
                    'descriptions' => $descriptionText,
                    'subtitle' => $subtitle,
                    'def' => $definition ?? [],
                ]],
                'filterValue' => '1',
                'sortValue' => 1,
                'searchValue' => $label,
            ],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Capability::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);

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

        $query = Capability::query()
            ->visibleToUser($request->user())
            ->with(['createdBy'])
            ->withCount(['specializations', 'creatures']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('effect', 'like', "%{$search}%");
            });
        }

        $capFilters = [
            'state' => ['state', 'string'],
            'element' => ['element', 'int'],
            'id' => ['id', 'int'],
            'is_magic' => ['is_magic', 'int'],
            'ritual_available' => ['ritual_available', 'int'],
            'po_editable' => ['po_editable', 'int'],
            'is_passive' => ['is_passive', 'int'],
        ];
        foreach ($capFilters as $key => [$column, $cast]) {
            if ($this->hasFilterValue($filters, $key)) {
                $this->applyEqualityFilter($query, $column, $filters[$key], $cast);
            }
        }
        foreach (['level' => 'level', 'pa' => 'pa', 'po' => 'po'] as $key => $column) {
            if ($this->normalizeRangeBounds($filters[$key] ?? null) !== null) {
                $this->applyIntegerRangeFilter($query, $column, $filters[$key]);
            } elseif ($this->hasFilterValue($filters, $key)) {
                $this->applyEqualityFilter($query, $column, $filters[$key]);
            }
        }

        $this->applyEntityTableIdList($query, $request);

        $allowedSort = [
            'id', 'name', 'description', 'effect', 'level', 'pa', 'po', 'element', 'is_magic', 'ritual_available',
            'time_before_use_again', 'casting_time', 'duration', 'state', 'po_editable', 'is_passive', 'powerful',
            'read_level', 'write_level',
            'created_at', 'updated_at',
        ];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $limit = $pageResult['limit'];
        $page = $pageResult['page'];
        $pagination = $pageResult['pagination'];

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Capability::class),
            'createAny' => Gate::allows('createAny', Capability::class),
            'updateAny' => Gate::allows('updateAny', Capability::class),
            'deleteAny' => Gate::allows('deleteAny', Capability::class),
            'manageAny' => Gate::allows('manageAny', Capability::class),
        ];

        $filterOptions = [
            'level' => $this->integerColumnBounds(Capability::query()->visibleToUser($request->user()), 'level', 0, 20),
            'pa' => $this->integerColumnBounds(Capability::query()->visibleToUser($request->user()), 'pa', 0, 12),
            'po' => $this->integerColumnBounds(Capability::query()->visibleToUser($request->user()), 'po', 0, 20),
            'state' => EntityState::options(),
            'element' => ElementBitmask::allFilterOptions(),
            'is_magic' => [
                ['value' => '1', 'label' => 'Wakfu'],
                ['value' => '0', 'label' => 'Physique'],
            ],
            'ritual_available' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'po_editable' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'is_passive' => [
                ['value' => '1', 'label' => 'Passif'],
                ['value' => '0', 'label' => 'Actif'],
            ],
        ];

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Capability $c) {
                $createdBy = $c->createdBy;

                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'description' => $c->description,
                    'effect' => $c->effect,
                    'level' => $c->level,
                    'pa' => $c->pa,
                    'po' => $c->po,
                    'po_editable' => (bool) $c->po_editable,
                    'time_before_use_again' => $c->time_before_use_again,
                    'casting_time' => $c->casting_time,
                    'duration' => $c->duration,
                    'element' => $c->element,
                    'is_magic' => (bool) $c->is_magic,
                    'ritual_available' => (bool) $c->ritual_available,
                    'is_passive' => (bool) ($c->is_passive ?? false),
                    'powerful' => $c->powerful,
                    'state' => (string) ($c->state ?? 'draft'),
                    'read_level' => (int) ($c->read_level ?? 0),
                    'write_level' => (int) ($c->write_level ?? 0),
                    'image' => $c->image,
                    'created_by' => $c->created_by,
                    'specializations_count' => (int) ($c->specializations_count ?? 0),
                    'creatures_count' => (int) ($c->creatures_count ?? 0),
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $c->created_at?->toISOString(),
                    'updated_at' => $c->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'capabilities',
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

        $passiveDefinition = app(CharacteristicMetaByDbColumnService::class)->buildSpellByDbColumn()['is_passive'] ?? null;

        $tableRows = $rows->map(function (Capability $c) use ($passiveDefinition) {
            $showHref = route('entities.capabilities.show', $c->id);
            $createdBy = $c->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $c->created_at ? $c->created_at->getTimestamp() : 0;
            $updatedAtLabel = $c->updated_at ? $c->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $c->updated_at ? $c->updated_at->getTimestamp() : 0;

            $effectPlain = $c->effect ? trim(preg_replace('/\s+/', ' ', strip_tags((string) $c->effect))) : '';
            $descriptionPlain = $c->description ? trim(preg_replace('/\s+/', ' ', strip_tags((string) $c->description))) : '';

            return [
                'id' => $c->id,
                'cells' => [
                    'id' => [
                        'type' => 'text',
                        'value' => (string) $c->id,
                        'params' => ['sortValue' => $c->id, 'filterValue' => (string) $c->id],
                    ],
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $c->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $c->name,
                            'sortValue' => (string) $c->name,
                        ],
                    ],
                    'description' => [
                        'type' => 'text',
                        'value' => $descriptionPlain !== '' ? $descriptionPlain : '-',
                        'params' => [
                            'searchValue' => $descriptionPlain,
                            'sortValue' => $descriptionPlain,
                        ],
                    ],
                    'effect' => [
                        'type' => 'text',
                        'value' => $effectPlain !== '' ? $effectPlain : '-',
                        'params' => [
                            'searchValue' => $effectPlain,
                            'sortValue' => $effectPlain,
                        ],
                    ],
                    'level' => [
                        'type' => 'text',
                        'value' => $c->level ?: '-',
                        'params' => [
                            'filterValue' => (string) ($c->level ?? ''),
                            'sortValue' => is_numeric((string) $c->level) ? (int) $c->level : (string) ($c->level ?? ''),
                            'searchValue' => (string) ($c->level ?? ''),
                        ],
                    ],
                    'pa' => [
                        'type' => 'text',
                        'value' => $c->pa ?: '-',
                        'params' => [
                            'filterValue' => (string) ($c->pa ?? ''),
                            'sortValue' => is_numeric((string) $c->pa) ? (int) $c->pa : (string) ($c->pa ?? ''),
                        ],
                    ],
                    'po' => [
                        'type' => 'text',
                        'value' => $c->po ?: '-',
                        'params' => [
                            'filterValue' => (string) ($c->po ?? ''),
                            'sortValue' => (string) ($c->po ?? ''),
                        ],
                    ],
                    'image' => [
                        'type' => 'thumb',
                        'value' => $c->image ?: '',
                        'params' => ['sortValue' => $c->image ? 1 : 0],
                    ],
                    'po_editable' => [
                        'type' => 'badge',
                        'value' => $c->po_editable ? 'Oui' : 'Non',
                        'params' => ['sortValue' => $c->po_editable ? 1 : 0, 'filterValue' => $c->po_editable ? '1' : '0'],
                    ],
                    'element' => [
                        'type' => 'element',
                        'value' => ElementConstants::getLabel((int) ($c->element ?? 0)) ?? '-',
                        'params' => [
                            'element' => (int) ($c->element ?? 0),
                            'sortValue' => (int) ($c->element ?? 0),
                            'searchValue' => ElementConstants::getLabel((int) ($c->element ?? 0)) ?? '',
                            'filterValue' => (int) ($c->element ?? 0),
                        ],
                    ],
                    'is_magic' => [
                        'type' => 'badge',
                        'value' => $c->is_magic ? 'Wakfu' : 'Physique',
                        'params' => ['filterValue' => $c->is_magic ? '1' : '0', 'sortValue' => $c->is_magic ? 1 : 0],
                    ],
                    'ritual_available' => [
                        'type' => 'badge',
                        'value' => $c->ritual_available ? 'Oui' : 'Non',
                        'params' => ['filterValue' => $c->ritual_available ? '1' : '0', 'sortValue' => $c->ritual_available ? 1 : 0],
                    ],
                    'is_passive' => $this->buildPassiveCell((bool) ($c->is_passive ?? false), $passiveDefinition),
                    'time_before_use_again' => [
                        'type' => 'text',
                        'value' => $c->time_before_use_again ?: '-',
                        'params' => ['sortValue' => (string) ($c->time_before_use_again ?? '')],
                    ],
                    'casting_time' => [
                        'type' => 'text',
                        'value' => $c->casting_time ?: '-',
                        'params' => ['sortValue' => (string) ($c->casting_time ?? '')],
                    ],
                    'duration' => [
                        'type' => 'text',
                        'value' => $c->duration ?: '-',
                        'params' => ['sortValue' => (string) ($c->duration ?? '')],
                    ],
                    'state' => [
                        'type' => 'badge',
                        'value' => (string) ($c->state ?? 'draft'),
                        'params' => ['filterValue' => (string) ($c->state ?? ''), 'sortValue' => (string) ($c->state ?? '')],
                    ],
                    'read_level' => [
                        'type' => 'badge',
                        'value' => (string) ($c->read_level ?? 0),
                        'params' => ['sortValue' => (int) ($c->read_level ?? 0)],
                    ],
                    'write_level' => [
                        'type' => 'badge',
                        'value' => (string) ($c->write_level ?? 0),
                        'params' => ['sortValue' => (int) ($c->write_level ?? 0)],
                    ],
                    'created_by' => [
                        'type' => 'text',
                        'value' => $createdByLabel,
                        'params' => [
                            'searchValue' => $createdByLabel,
                            'sortValue' => $createdByLabel,
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
                        'id' => $c->id,
                        'name' => $c->name,
                        'description' => $c->description,
                        'effect' => $c->effect,
                        'level' => $c->level,
                        'pa' => $c->pa,
                        'po' => $c->po,
                        'po_editable' => (bool) $c->po_editable,
                        'time_before_use_again' => $c->time_before_use_again,
                        'casting_time' => $c->casting_time,
                        'duration' => $c->duration,
                        'element' => $c->element,
                        'is_magic' => (bool) $c->is_magic,
                        'ritual_available' => (bool) $c->ritual_available,
                        'is_passive' => (bool) ($c->is_passive ?? false),
                        'powerful' => $c->powerful,
                        'state' => (string) ($c->state ?? 'draft'),
                        'read_level' => (int) ($c->read_level ?? 0),
                        'write_level' => (int) ($c->write_level ?? 0),
                        'image' => $c->image,
                        'created_by' => $c->created_by,
                        'specializations_count' => (int) ($c->specializations_count ?? 0),
                        'creatures_count' => (int) ($c->creatures_count ?? 0),
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
                'entityType' => 'capabilities',
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
}
