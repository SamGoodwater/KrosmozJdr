<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Characteristic;
use App\Models\CharacteristicEntity;
use App\Models\DofusdbConversionFormula;
use App\Services\Characteristic\CharacteristicService;
use App\Services\Characteristic\FormulaEvaluator;
use App\Services\Scrapping\ConversionHandlerRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Administration des caractéristiques (admin et super_admin).
 * Liste à gauche, panneau d'édition à droite, graphiques pour les formules.
 */
class CharacteristicController extends Controller
{
    public function __construct(
        private readonly CharacteristicService $characteristicService,
        private readonly FormulaEvaluator $formulaEvaluator,
        private readonly ConversionHandlerRegistry $handlerRegistry
    ) {
    }

    /**
     * Liste des caractéristiques (page avec liste à gauche, panneau vide à droite).
     */
    public function index(): InertiaResponse
    {
        $list = $this->characteristicService->getCharacteristics();

        return Inertia::render('Admin/characteristics/Index', [
            'characteristics' => $this->buildListForPanel($list),
            'selected' => null,
        ]);
    }

    /**
     * Affiche une caractéristique (même page, panneau à droite rempli).
     * Inclut les formules de conversion Dofus → JDR par entité (monster, class, item).
     */
    public function show(Characteristic $characteristic): InertiaResponse
    {
        $characteristic->load('entityDefinitions');
        $list = $this->characteristicService->getCharacteristics();

        $conversionFormulas = [];
        foreach (['monster', 'class', 'item'] as $entity) {
            $row = DofusdbConversionFormula::where('characteristic_id', $characteristic->id)
                ->where('entity', $entity)
                ->first();
            $conversionFormulas[] = [
                'entity' => $entity,
                'conversion_formula' => $row?->conversion_formula ?? '',
                'formula_display' => $row?->formula_display ?? '',
                'handler_name' => $row?->handler_name ?? '',
            ];
        }

        $selected = $this->characteristicToArray($characteristic);
        $selected['entities'] = $this->mergeDefaultEntityDefinitions($selected['entities'] ?? []);
        $selected['conversion_formulas'] = $conversionFormulas;

        return Inertia::render('Admin/characteristics/Index', [
            'characteristics' => $this->buildListForPanel($list),
            'selected' => $selected,
        ]);
    }

    /**
     * Construit la liste pour le panneau gauche (id, name, short_name, icon, color) triée par nom.
     *
     * @param array<string, array<string, mixed>> $list
     * @return list<array{id: string, name: string|null, short_name: string|null, icon: string|null, color: string|null}>
     */
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

    /**
     * Upload d'une icône pour une caractéristique.
     * Stocke dans storage/app/public/images/icons/characteristics et retourne le nom du fichier.
     */
    public function uploadIcon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'image', 'max:2048'], // 2 Mo max
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

    /**
     * Aperçu graphique d'une formule : points (x = variable, y = résultat).
     * Query: characteristic_id, entity, variable (ex. level), formula (optionnel, pour aperçu non sauvegardé).
     */
    public function formulaPreview(Request $request): JsonResponse
    {
        $v = Validator::make($request->query(), [
            'characteristic_id' => 'required|string',
            'entity' => 'required|in:monster,class,item',
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
     * Met à jour une caractéristique et ses entity definitions.
     */
    public function update(Request $request, Characteristic $characteristic): \Illuminate\Http\RedirectResponse
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
            'entities.*.entity' => 'required|in:' . implode(',', CharacteristicEntity::VALIDATION_ENTITIES),
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
            'conversion_formulas.*.entity' => 'required|in:monster,class,item',
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

        $characteristic->update([
            'name' => $data['name'],
            'short_name' => $data['short_name'] ?? null,
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? null,
            'type' => $data['type'],
            'unit' => $data['unit'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? $characteristic->sort_order),
            'applies_to' => $this->normalizeArrayInput($data['applies_to'] ?? null),
            'value_available' => $this->normalizeArrayInput($data['value_available'] ?? null),
        ]);

        $entities = $data['entities'] ?? [];
        foreach ($entities as $ent) {
            $entity = $ent['entity'];
            $characteristic->entityDefinitions()->updateOrCreate(
                ['entity' => $entity],
                [
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
                ['characteristic_id' => $characteristic->id, 'entity' => $entity],
                ['formula_type' => 'custom', 'parameters' => null]
            );
            $row->conversion_formula = isset($cf['conversion_formula']) && $cf['conversion_formula'] !== '' ? $cf['conversion_formula'] : null;
            $row->formula_display = $cf['formula_display'] ?? null;
            $row->handler_name = isset($cf['handler_name']) && $cf['handler_name'] !== '' ? $cf['handler_name'] : null;
            $row->save();
        }

        return redirect()->route('admin.characteristics.show', $characteristic->id)
            ->with('success', 'Caractéristique mise à jour.');
    }

    /**
     * Normalise une entrée en tableau (accepte array ou string newline-separée).
     *
     * @param array<string>|string|null $input
     * @return array<string>|null
     */
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

    private function characteristicToArray(Characteristic $c): array
    {
        $entities = [];
        foreach ($c->entityDefinitions as $e) {
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
            'id' => $c->id,
            'db_column' => $c->db_column,
            'name' => $c->name,
            'short_name' => $c->short_name,
            'description' => $c->description,
            'icon' => $c->icon,
            'color' => $c->color,
            'type' => $c->type,
            'unit' => $c->unit,
            'sort_order' => $c->sort_order,
            'applies_to' => $c->applies_to ?? [],
            'value_available' => $c->value_available ?? [],
            'entities' => $entities,
        ];
    }

    /**
     * Fusionne les entity definitions existantes avec les entités supportées (monster, class, item, spell).
     * Ajoute une ligne par défaut pour chaque entité manquante afin que le formulaire affiche toutes les entités.
     *
     * @param array<int, array<string, mixed>> $entities
     * @return array<int, array<string, mixed>>
     */
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
        foreach (CharacteristicEntity::VALIDATION_ENTITIES as $entity) {
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
