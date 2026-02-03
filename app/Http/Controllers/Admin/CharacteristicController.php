<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntityCharacteristic;
use App\Models\DofusdbConversionFormula;
use App\Services\Characteristic\CharacteristicService;
use App\Services\Characteristic\FormulaEvaluator;
use App\Services\Characteristic\DofusConversion\ConversionHandlerRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Administration des caractéristiques (entity_characteristics).
 * Liste par characteristic_key à gauche, panneau d'édition à droite (agrégat par entité).
 */
class CharacteristicController extends Controller
{
    public function __construct(
        private readonly CharacteristicService $characteristicService,
        private readonly FormulaEvaluator $formulaEvaluator,
        private readonly ConversionHandlerRegistry $handlerRegistry
    ) {
    }

    public function index(): InertiaResponse
    {
        $list = $this->characteristicService->getCharacteristics();

        return Inertia::render('Admin/characteristics/Index', [
            'characteristics' => $this->buildListForPanel($list),
            'selected' => null,
        ]);
    }

    /**
     * Affiche une caractéristique par characteristic_key (agrégat des lignes entity_characteristics).
     */
    public function show(string $characteristic_key): InertiaResponse
    {
        $rows = EntityCharacteristic::where('characteristic_key', $characteristic_key)->orderBy('entity')->get();
        if ($rows->isEmpty()) {
            return Inertia::render('Admin/characteristics/Index', [
                'characteristics' => $this->buildListForPanel($this->characteristicService->getCharacteristics()),
                'selected' => null,
            ])->with('error', 'Caractéristique introuvable.');
        }

        $list = $this->characteristicService->getCharacteristics();
        $conversionFormulas = [];
        foreach (['monster', 'class', 'item', 'spell'] as $entity) {
            $row = DofusdbConversionFormula::where('characteristic_key', $characteristic_key)
                ->where('entity', $entity)
                ->first();
            $conversionFormulas[] = [
                'entity' => $entity,
                'conversion_formula' => $row?->conversion_formula ?? '',
                'formula_display' => $row?->formula_display ?? '',
                'handler_name' => $row?->handler_name ?? '',
            ];
        }

        $selected = $this->buildSelectedFromRows($rows);
        $selected['id'] = $characteristic_key;
        $selected['entities'] = $this->mergeDefaultEntityDefinitions($selected['entities'] ?? []);
        $selected['conversion_formulas'] = $conversionFormulas;

        return Inertia::render('Admin/characteristics/Index', [
            'characteristics' => $this->buildListForPanel($list),
            'selected' => $selected,
        ]);
    }

    private function buildListForPanel(array $list): array
    {
        $listForPanel = [];
        foreach ($list as $id => $def) {
            $listForPanel[] = [
                'id' => $id,
                'name' => $def['name'] ?? $id,
                'short_name' => $def['short_name'] ?? null,
                'icon' => $def['icon'] ?? null,
                'color' => $def['color'] ?? null,
            ];
        }
        usort($listForPanel, fn ($a, $b) => ($a['name'] ?? '') <=> ($b['name'] ?? ''));

        return $listForPanel;
    }

    public function uploadIcon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'image', 'max:2048'],
        ]);

        $file = $validated['file'];
        $ext = $file->getClientOriginalExtension() ?: 'png';
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $name = $name ?: Str::random(8);
        $filename = $name . '_' . time() . '.' . strtolower($ext);

        $dir = 'images/icons/characteristics';
        $path = $file->storeAs($dir, $filename, ['disk' => 'public']);

        if (!$path) {
            return response()->json(['success' => false, 'message' => "Impossible d'uploader l'icône."], 500);
        }

        return response()->json(['success' => true, 'icon' => $filename]);
    }

    public function formulaPreview(Request $request): JsonResponse
    {
        $v = Validator::make($request->query(), [
            'characteristic_id' => 'required|string',
            'entity' => 'required|in:monster,class,item,spell',
            'variable' => 'nullable|string',
            'formula' => 'nullable|string',
        ]);
        if ($v->fails()) {
            return response()->json(['points' => []], 422);
        }

        $characteristicId = $request->query('characteristic_id');
        $entity = $request->query('entity');
        $variable = $request->query('variable', 'level');
        $formulaOverride = $request->query('formula');

        $def = $this->characteristicService->getCharacteristic($characteristicId);
        $entityDef = $def !== null ? ($def['entities'][$entity] ?? null) : null;
        $formula = $formulaOverride !== null && $formulaOverride !== ''
            ? $formulaOverride
            : ($entityDef['formula'] ?? null);
        if ($formula === null || $formula === '') {
            return response()->json(['points' => []]);
        }

        $decoded = \App\Services\Characteristic\FormulaConfigDecoder::decode($formula);
        $axisVar = $variable;
        if ($decoded['type'] === 'table') {
            $axisVar = $decoded['characteristic'];
        }

        $limits = $this->characteristicService->getLimits($axisVar, $entity);
        if ($limits === null) {
            $limits = ['min' => 1, 'max' => 20];
        }
        $min = $limits['min'];
        $max = min($limits['max'], $min + 50);

        $defaults = [];
        if ($decoded['type'] === 'table') {
            foreach ($decoded['entries'] as $entry) {
                $v = $entry['value'];
                if (is_string($v) && $v !== '' && preg_match_all('/\[(\w+)\]/', $v, $m)) {
                    foreach (array_unique($m[1]) as $varId) {
                        if ($varId === $axisVar || isset($defaults[$varId])) {
                            continue;
                        }
                        $lim = $this->characteristicService->getLimits($varId, $entity);
                        $defaults[$varId] = $lim !== null
                            ? (int) round(($lim['min'] + $lim['max']) / 2)
                            : 10;
                    }
                }
            }
        } else {
            if (preg_match_all('/\[(\w+)\]/', $formula, $m)) {
                foreach (array_unique($m[1]) as $varId) {
                    if ($varId === $axisVar) {
                        continue;
                    }
                    $lim = $this->characteristicService->getLimits($varId, $entity);
                    $defaults[$varId] = $lim !== null
                        ? (int) round(($lim['min'] + $lim['max']) / 2)
                        : 10;
                }
            }
        }

        $points = [];
        for ($x = $min; $x <= $max; $x++) {
            $vars = $defaults;
            $vars[$axisVar] = $x;
            $y = $this->formulaEvaluator->evaluateFormulaOrTable($formula, $vars);
            $points[] = ['x' => $x, 'y' => $y !== null ? round($y, 2) : 0];
        }

        return response()->json(['points' => $points]);
    }

    /**
     * Met à jour les entity_characteristics pour ce characteristic_key (une ligne par entité).
     */
    public function update(Request $request, string $characteristic_key): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:64',
            'color' => 'nullable|string|max:64',
            'type' => 'required|in:int,string,array',
            'unit' => 'nullable|string|max:64',
            'sort_order' => 'integer',
            'applies_to' => 'nullable',
            'value_available' => 'nullable',
            'entities' => 'array',
            'entities.*.entity' => 'required|in:' . implode(',', EntityCharacteristic::ENTITIES),
            'entities.*.min' => 'nullable|integer',
            'entities.*.max' => 'nullable|integer',
            'entities.*.formula' => 'nullable|string',
            'entities.*.formula_display' => 'nullable|string',
            'entities.*.default_value' => 'nullable|string',
            'entities.*.required' => 'boolean',
            'entities.*.validation_message' => 'nullable|string',
            'entities.*.forgemagie_allowed' => 'boolean',
            'entities.*.forgemagie_max' => 'integer',
            'entities.*.base_price_per_unit' => 'nullable|numeric',
            'entities.*.rune_price_per_unit' => 'nullable|numeric',
            'conversion_formulas' => 'nullable|array',
            'conversion_formulas.*.entity' => 'required|in:monster,class,item,spell',
            'conversion_formulas.*.conversion_formula' => 'nullable|string',
            'conversion_formulas.*.formula_display' => 'nullable|string',
            'conversion_formulas.*.handler_name' => [
                'nullable',
                'string',
                'max:64',
                Rule::in(array_merge(
                    [''],
                    array_column($this->handlerRegistry->allHandlersForSelect(), 'name')
                )),
            ],
        ]);

        $entities = $data['entities'] ?? [];
        foreach ($entities as $ent) {
            $entity = $ent['entity'];
            EntityCharacteristic::updateOrCreate(
                ['entity' => $entity, 'characteristic_key' => $characteristic_key],
                [
                    'name' => $data['name'],
                    'short_name' => $data['short_name'] ?? null,
                    'descriptions' => $data['description'] ?? null,
                    'icon' => $data['icon'] ?? null,
                    'color' => $data['color'] ?? null,
                    'type' => $data['type'],
                    'unit' => $data['unit'] ?? null,
                    'sort_order' => (int) ($data['sort_order'] ?? 0),
                    'applies_to' => $this->normalizeArrayInput($data['applies_to'] ?? null),
                    'value_available' => $this->normalizeArrayInput($data['value_available'] ?? null),
                    'min' => isset($ent['min']) ? (int) $ent['min'] : null,
                    'max' => isset($ent['max']) ? (int) $ent['max'] : null,
                    'formula' => $ent['formula'] ?? null,
                    'formula_display' => $ent['formula_display'] ?? null,
                    'default_value' => isset($ent['default_value']) ? (string) $ent['default_value'] : null,
                    'required' => (bool) ($ent['required'] ?? false),
                    'validation_message' => $ent['validation_message'] ?? null,
                    'forgemagie_allowed' => (bool) ($ent['forgemagie_allowed'] ?? false),
                    'forgemagie_max' => (int) ($ent['forgemagie_max'] ?? 0),
                    'base_price_per_unit' => isset($ent['base_price_per_unit']) && $ent['base_price_per_unit'] !== '' ? (float) $ent['base_price_per_unit'] : null,
                    'rune_price_per_unit' => isset($ent['rune_price_per_unit']) && $ent['rune_price_per_unit'] !== '' ? (float) $ent['rune_price_per_unit'] : null,
                ]
            );
        }

        $this->characteristicService->clearCache();

        $conversionFormulas = $data['conversion_formulas'] ?? [];
        foreach ($conversionFormulas as $cf) {
            $entity = $cf['entity'];
            $row = DofusdbConversionFormula::firstOrCreate(
                ['characteristic_key' => $characteristic_key, 'entity' => $entity],
                ['formula_type' => 'custom', 'parameters' => null]
            );
            $row->conversion_formula = isset($cf['conversion_formula']) && $cf['conversion_formula'] !== '' ? $cf['conversion_formula'] : null;
            $row->formula_display = $cf['formula_display'] ?? null;
            $row->handler_name = isset($cf['handler_name']) && $cf['handler_name'] !== '' ? $cf['handler_name'] : null;
            $row->save();
        }

        return redirect()->route('admin.characteristics.show', ['characteristic_key' => $characteristic_key])
            ->with('success', 'Caractéristique mise à jour.');
    }

    private function normalizeArrayInput(array|string|null $input): ?array
    {
        $items = match (true) {
            $input === null || $input === '' => [],
            is_array($input) => $input,
            default => explode("\n", (string) $input),
        };
        $out = array_values(array_filter(array_map('trim', $items)));

        return $out === [] ? null : $out;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection<int, EntityCharacteristic> $rows
     * @return array<string, mixed>
     */
    private function buildSelectedFromRows($rows): array
    {
        $first = $rows->first();
        $entities = [];
        foreach ($rows as $e) {
            $entities[] = [
                'entity' => $e->entity,
                'min' => $e->min,
                'max' => $e->max,
                'formula' => $e->formula,
                'formula_display' => $e->formula_display,
                'default_value' => $e->default_value,
                'required' => $e->required,
                'validation_message' => $e->validation_message,
                'forgemagie_allowed' => $e->forgemagie_allowed ?? false,
                'forgemagie_max' => $e->forgemagie_max ?? 0,
                'base_price_per_unit' => $e->base_price_per_unit,
                'rune_price_per_unit' => $e->rune_price_per_unit,
            ];
        }

        return [
            'db_column' => $first->db_column,
            'name' => $first->name,
            'short_name' => $first->short_name,
            'description' => $first->descriptions,
            'icon' => $first->icon,
            'color' => $first->color,
            'type' => $first->type,
            'unit' => $first->unit,
            'sort_order' => $first->sort_order,
            'applies_to' => $first->applies_to ?? [],
            'value_available' => $first->value_available ?? [],
            'entities' => $entities,
        ];
    }

    private function mergeDefaultEntityDefinitions(array $entities): array
    {
        $byEntity = [];
        foreach ($entities as $ent) {
            $e = $ent['entity'] ?? null;
            if (is_string($e)) {
                $byEntity[$e] = $ent;
            }
        }
        $out = [];
        foreach (EntityCharacteristic::ENTITIES as $entity) {
            $out[] = $byEntity[$entity] ?? [
                'entity' => $entity,
                'min' => null,
                'max' => null,
                'formula' => null,
                'formula_display' => null,
                'default_value' => null,
                'required' => false,
                'validation_message' => null,
                'forgemagie_allowed' => false,
                'forgemagie_max' => 0,
                'base_price_per_unit' => null,
                'rune_price_per_unit' => null,
            ];
        }

        return $out;
    }
}
