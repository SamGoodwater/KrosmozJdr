<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use App\Services\Characteristic\Conversion\ConversionFormulaGenerator;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Formula\FormulaConfigDecoder;
use App\Services\Characteristic\CharacteristicColorCssGenerator;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Administration des caractéristiques (nouvelle structure : characteristics + characteristic_creature/object/spell).
 */
class CharacteristicController extends Controller
{
    /** Entités possibles + '*' = toutes les entités du groupe (défaut). */
    private const ENTITIES = ['*', 'monster', 'class', 'npc', 'item', 'consumable', 'resource', 'panoply', 'spell'];

    /** Entités par groupe (pour la création). */
    private const ENTITIES_BY_GROUP = [
        'creature' => ['*', 'monster', 'class', 'npc'],
        'object' => ['*', 'item', 'consumable', 'resource', 'panoply'],
        'spell' => ['*', 'spell'],
    ];

    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly CharacteristicFormulaService $formulaService,
        private readonly ConversionFormulaGenerator $conversionFormulaGenerator,
        private readonly CharacteristicColorCssGenerator $colorCssGenerator
    ) {
    }

    public function index(): InertiaResponse
    {
        $characteristicsByGroup = $this->buildCharacteristicsByGroup();

        return Inertia::render('Admin/characteristics/Index', [
            'characteristicsByGroup' => $characteristicsByGroup,
            'selected' => null,
            'entitiesByGroup' => self::ENTITIES_BY_GROUP,
        ]);
    }

    /**
     * Formulaire de création d'une caractéristique.
     */
    public function create(): InertiaResponse
    {
        $characteristicsByGroup = $this->buildCharacteristicsByGroup();

        $defaultEntityRow = [
            'entity' => '*',
            'db_column' => null,
            'min' => null,
            'max' => null,
            'formula' => null,
            'formula_display' => null,
            'default_value' => null,
            'conversion_formula' => null,
            'conversion_dofus_sample' => null,
            'conversion_krosmoz_sample' => null,
            'conversion_sample_rows' => null,
            'forgemagie_allowed' => false,
            'forgemagie_max' => 0,
            'base_price_per_unit' => null,
            'rune_price_per_unit' => null,
        ];
        $entitiesTemplate = [];
        foreach (self::ENTITIES_BY_GROUP as $group => $entities) {
            $entitiesTemplate[$group] = array_map(fn (string $entity) => array_merge($defaultEntityRow, ['entity' => $entity]), $entities);
        }

        return Inertia::render('Admin/characteristics/Index', [
            'characteristicsByGroup' => $characteristicsByGroup,
            'selected' => null,
            'createMode' => true,
            'groups' => array_keys(self::ENTITIES_BY_GROUP),
            'entitiesByGroup' => self::ENTITIES_BY_GROUP,
            'entitiesTemplate' => $entitiesTemplate,
        ]);
    }

    /**
     * Enregistre une nouvelle caractéristique (table générale + lignes du groupe choisi).
     * La clé est normalisée : si elle ne se termine pas par _creature, _object ou _spell,
     * le suffixe correspondant au groupe choisi est ajouté automatiquement (ex. life_dice → life_dice_creature).
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/'],
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'helper' => 'nullable|string',
            'icon' => 'nullable|string|max:64',
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#([0-9A-Fa-f]{3}){1,2}$/'],
            'type' => 'required|in:int,string,array',
            'unit' => 'nullable|string|max:64',
            'sort_order' => 'nullable|integer',
            'group' => 'required|in:creature,object,spell',
            'entities' => 'array',
            'entities.*.entity' => 'required|string|max:32',
            'entities.*.db_column' => 'nullable|string|max:64',
            'entities.*.min' => 'nullable|string|max:512',
            'entities.*.max' => 'nullable|string|max:512',
            'entities.*.formula' => 'nullable|string',
            'entities.*.formula_display' => 'nullable|string',
            'entities.*.default_value' => 'nullable|string|max:512',
            'entities.*.conversion_formula' => 'nullable|string',
            'entities.*.forgemagie_allowed' => 'nullable|boolean',
            'entities.*.forgemagie_max' => 'nullable|integer',
            'entities.*.base_price_per_unit' => 'nullable|numeric',
            'entities.*.rune_price_per_unit' => 'nullable|numeric',
        ]);

        $key = $this->normalizeCharacteristicKey(trim($data['key']), $data['group']);
        Validator::make(
            ['key' => $key],
            ['key' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/', 'unique:characteristics,key']]
        )->validate();

        $allowedEntities = self::ENTITIES_BY_GROUP[$data['group']];
        $entities = array_filter($data['entities'] ?? [], function ($ent) use ($allowedEntities) {
            return in_array($ent['entity'] ?? '', $allowedEntities, true);
        });

        $characteristic = Characteristic::create([
            'key' => $key,
            'name' => $data['name'],
            'short_name' => $data['short_name'] ?? null,
            'helper' => $data['helper'] ?? null,
            'descriptions' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? null,
            'type' => $data['type'],
            'unit' => $data['unit'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        foreach ($entities as $ent) {
            $this->updateGroupRow($characteristic->id, $ent['entity'], $ent);
        }

        $this->getter->clearCache();
        $this->colorCssGenerator->generate();

        return redirect()
            ->route('admin.characteristics.show', $characteristic->key)
            ->with('success', 'Caractéristique créée.');
    }

    public function show(string $characteristic_key): InertiaResponse
    {
        $characteristic = Characteristic::where('key', $characteristic_key)->first();
        if ($characteristic === null) {
            return Inertia::render('Admin/characteristics/Index', [
                'characteristicsByGroup' => $this->buildCharacteristicsByGroup(),
                'selected' => null,
                'entitiesByGroup' => self::ENTITIES_BY_GROUP,
            ])->with('error', 'Caractéristique introuvable.');
        }

        $entities = [];
        foreach (CharacteristicCreature::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
            $entities[] = $this->groupRowToEntity($row, $characteristic);
        }
        foreach (CharacteristicObject::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
            $entities[] = $this->groupRowToEntity($row, $characteristic);
        }
        foreach (CharacteristicSpell::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
            $entities[] = $this->groupRowToEntity($row, $characteristic);
        }

        $group = $this->inferPrimaryGroup($characteristic);
        $entitiesForGroup = self::ENTITIES_BY_GROUP[$group] ?? ['*'];
        // Ne garder que les lignes du groupe (évite d'afficher des panneaux pour d'autres groupes si des lignes ont été créées par erreur)
        $entities = array_values(array_filter($entities, fn (array $ent): bool => in_array($ent['entity'] ?? '', $entitiesForGroup, true)));

        $conversionFormulas = [];
        foreach ($entitiesForGroup as $entity) {
            if ($entity === '*') {
                continue;
            }
            $def = $this->getter->getDefinition($characteristic_key, $entity);
            $conversionFormulas[] = [
                'entity' => $entity,
                'conversion_formula' => $def['conversion_formula'] ?? '',
                'formula_display' => $def['formula_display'] ?? '',
                'handler_name' => '',
            ];
        }

        $entityOverrideKeys = array_values(array_filter(
            array_unique(array_column($entities, 'entity')),
            fn (string $e): bool => $e !== '*'
        ));

        // Ne renvoyer que les entités qui existent en base (pas de lignes par défaut pour class/npc/etc.).
        // Sinon le formulaire les envoie à l'enregistrement et on recrée des lignes qu'on venait de supprimer.
        $selected = [
            'id' => $characteristic->key,
            'name' => $characteristic->name,
            'short_name' => $characteristic->short_name,
            'description' => $characteristic->descriptions,
            'helper' => $characteristic->helper,
            'icon' => $characteristic->icon,
            'color' => $characteristic->color,
            'type' => $characteristic->type,
            'unit' => $characteristic->unit,
            'sort_order' => $characteristic->sort_order,
            'entities' => $entities,
            'entity_override_keys' => $entityOverrideKeys,
            'conversion_formulas' => $conversionFormulas,
            'group' => $group,
            'entitiesByGroup' => self::ENTITIES_BY_GROUP,
        ];

        return Inertia::render('Admin/characteristics/Index', [
            'characteristicsByGroup' => $this->buildCharacteristicsByGroup(),
            'selected' => $selected,
            'entitiesByGroup' => self::ENTITIES_BY_GROUP,
        ]);
    }

    /**
     * Supprime une caractéristique (et ses lignes de groupe par cascade).
     */
    public function destroy(string $characteristic_key): \Illuminate\Http\RedirectResponse
    {
        $characteristic = Characteristic::where('key', $characteristic_key)->first();
        if ($characteristic === null) {
            return redirect()->route('admin.characteristics.index')->with('error', 'Caractéristique introuvable.');
        }
        $characteristic->delete();
        $this->getter->clearCache();
        $this->colorCssGenerator->generate();

        return redirect()->route('admin.characteristics.index')->with('success', 'Caractéristique supprimée.');
    }

    /**
     * Infère le groupe principal de la caractéristique (creature, object ou spell) selon les tables qui ont des lignes.
     */
    private function inferPrimaryGroup(Characteristic $characteristic): string
    {
        if (CharacteristicCreature::where('characteristic_id', $characteristic->id)->exists()) {
            return 'creature';
        }
        if (CharacteristicObject::where('characteristic_id', $characteristic->id)->exists()) {
            return 'object';
        }
        if (CharacteristicSpell::where('characteristic_id', $characteristic->id)->exists()) {
            return 'spell';
        }
        return 'creature';
    }

    public function formulaPreview(Request $request): JsonResponse
    {
        $characteristicId = $request->query('characteristic_id', '');
        $entity = $request->query('entity', 'resource');
        $variable = $request->query('variable', 'level');
        $formulaOverride = $request->query('formula');

        $def = $this->getter->getDefinition($characteristicId, $entity);
        $formula = $formulaOverride !== null && $formulaOverride !== ''
            ? $formulaOverride
            : ($def['formula'] ?? $def['conversion_formula'] ?? null);
        if ($formula === null || $formula === '') {
            return response()->json(['points' => []]);
        }

        $decoded = FormulaConfigDecoder::decode($formula);
        $axisVar = $variable;
        if ($decoded['type'] === 'table') {
            $axisVar = $decoded['characteristic'];
        }

        $limits = $this->getter->getLimitsByField($axisVar, $entity);
        if ($limits === null) {
            $limits = ['min' => 1, 'max' => 20];
        }
        $min = $limits['min'];
        $max = min($limits['max'], $min + 50);

        $defaults = $this->buildDefaultVariablesForPreview($formula, $decoded, $axisVar, $entity);

        $range = $this->formulaService->evaluateForVariableRange($formula, $axisVar, $min, $max, $defaults);
        $points = [];
        foreach ($range as $x => $y) {
            $points[] = ['x' => $x, 'y' => round($y, 2)];
        }

        return response()->json(['points' => $points]);
    }

    /**
     * Suggère une formule de conversion à partir des échantillons ou des paires (d, k).
     * Body : soit (conversion_dofus_sample + conversion_krosmoz_sample), soit (pairs : [{d, k}, ...]), et curve_type (table|linear|power|shifted_power).
     */
    public function suggestConversionFormula(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conversion_dofus_sample' => 'nullable|array',
            'conversion_dofus_sample.*' => 'numeric',
            'conversion_krosmoz_sample' => 'nullable|array',
            'conversion_krosmoz_sample.*' => 'numeric',
            'pairs' => 'nullable|array',
            'pairs.*.d' => 'required_with:pairs|numeric',
            'pairs.*.k' => 'required_with:pairs|numeric',
            'curve_type' => 'required|in:table,linear,power,shifted_power',
        ]);
        $curveType = $validated['curve_type'];

        if (! empty($validated['pairs'])) {
            $pairs = array_map(function (array $p): array {
                return ['d' => (float) $p['d'], 'k' => (float) $p['k']];
            }, $validated['pairs']);
            $suggestions = $this->conversionFormulaGenerator->suggestFormulasFromPairs($pairs);
        } else {
            $dofusSample = $this->normalizeSampleKeys($validated['conversion_dofus_sample'] ?? []);
            $krosmozSample = $this->normalizeSampleKeys($validated['conversion_krosmoz_sample'] ?? []);
            if ($dofusSample === [] || $krosmozSample === []) {
                return response()->json(
                    ['message' => 'Fournissez soit des paires (d, k), soit conversion_dofus_sample et conversion_krosmoz_sample.'],
                    422
                );
            }
            $suggestions = $this->conversionFormulaGenerator->suggestFormulas($dofusSample, $krosmozSample);
        }

        if ($curveType === 'table') {
            return response()->json(['formula' => $suggestions['table']]);
        }
        $result = $suggestions[$curveType] ?? null;
        if ($result === null) {
            return response()->json(
                ['message' => "Ajustement {$curveType} impossible avec les données fournies."],
                422
            );
        }
        return response()->json([
            'formula' => $result['formula'],
            'r2' => $result['r2'],
        ]);
    }

    /**
     * @param array<string, mixed> $sample
     * @return array<int|string, float>
     */
    private function normalizeSampleKeys(array $sample): array
    {
        $out = [];
        foreach ($sample as $k => $v) {
            $key = is_numeric($k) ? (int) $k : $k;
            $out[$key] = (float) $v;
        }
        return $out;
    }

    /**
     * Normalise les échantillons pour la sauvegarde (null ou tableau clé → valeur numérique).
     *
     * @param mixed $value
     * @return array<int|string, float>|null
     */
    private function normalizeSampleForSave(mixed $value): ?array
    {
        if ($value === null || $value === []) {
            return null;
        }
        if (! is_array($value)) {
            return null;
        }
        $out = [];
        foreach ($value as $k => $v) {
            if (is_numeric($v)) {
                $out[is_numeric($k) ? (int) $k : (string) $k] = (float) $v;
            }
        }
        return $out ?: null;
    }

    /**
     * Normalise conversion_sample_rows pour la sauvegarde (liste de {dofus_level, dofus_value, krosmoz_level, krosmoz_value}).
     *
     * @param mixed $value
     * @return list<array{dofus_level: int|float, dofus_value: int|float, krosmoz_level: int|float, krosmoz_value: int|float}>|null
     */
    private function normalizeSampleRowsForSave(mixed $value): ?array
    {
        if ($value === null || ! is_array($value)) {
            return null;
        }
        $out = [];
        foreach ($value as $row) {
            if (! is_array($row)) {
                continue;
            }
            $dofusLevel = isset($row['dofus_level']) && is_numeric($row['dofus_level']) ? (float) $row['dofus_level'] : null;
            $krosmozLevel = isset($row['krosmoz_level']) && is_numeric($row['krosmoz_level']) ? (float) $row['krosmoz_level'] : null;
            if ($dofusLevel === null || $krosmozLevel === null) {
                continue;
            }
            $dofusValue = isset($row['dofus_value']) && $row['dofus_value'] !== '' && is_numeric($row['dofus_value']) ? (float) $row['dofus_value'] : null;
            $krosmozValue = isset($row['krosmoz_value']) && $row['krosmoz_value'] !== '' && is_numeric($row['krosmoz_value']) ? (float) $row['krosmoz_value'] : null;
            $out[] = [
                'dofus_level' => $dofusLevel,
                'dofus_value' => $dofusValue,
                'krosmoz_level' => $krosmozLevel,
                'krosmoz_value' => $krosmozValue,
            ];
        }
        return $out ?: null;
    }

    /**
     * Dérive un échantillon (niveau → valeur) à partir des lignes, pour compatibilité avec les lecteurs existants.
     *
     * @param list<array{dofus_level: int|float, dofus_value: int|float, krosmoz_level: int|float, krosmoz_value: int|float}>|null $rows
     * @return array<int|string, float>|null
     */
    private function deriveSampleFromRows(?array $rows, string $which): ?array
    {
        if ($rows === null || $rows === []) {
            return null;
        }
        $out = [];
        foreach ($rows as $row) {
            if ($which === 'dofus') {
                $key = $row['dofus_level'];
                $val = $row['dofus_value'];
            } else {
                $key = $row['krosmoz_level'];
                $val = $row['krosmoz_value'];
            }
            if ($key !== null && $val !== null && $val !== '' && is_numeric($val)) {
                $out[is_numeric($key) ? (int) $key : (string) $key] = (float) $val;
            }
        }
        return $out ?: null;
    }

    public function update(Request $request, string $characteristic_key): \Illuminate\Http\RedirectResponse
    {
        $characteristic = Characteristic::where('key', $characteristic_key)->first();
        if ($characteristic === null) {
            return redirect()->route('admin.characteristics.index')->with('error', 'Caractéristique introuvable.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'helper' => 'nullable|string',
            'icon' => 'nullable|string|max:64',
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#([0-9A-Fa-f]{3}){1,2}$/'],
            'type' => 'required|in:int,string,array',
            'unit' => 'nullable|string|max:64',
            'sort_order' => 'nullable|integer',
            'entities' => 'array',
            'entities.*.entity' => 'required|in:' . implode(',', self::ENTITIES),
            'entities.*.db_column' => 'nullable|string|max:64',
            'entities.*.min' => 'nullable|string|max:512',
            'entities.*.max' => 'nullable|string|max:512',
            'entities.*.formula' => 'nullable|string',
            'entities.*.formula_display' => 'nullable|string',
            'entities.*.default_value' => 'nullable|string|max:512',
            'entities.*.conversion_formula' => 'nullable|string',
            'entities.*.conversion_dofus_sample' => 'nullable|array',
            'entities.*.conversion_dofus_sample.*' => 'numeric',
            'entities.*.conversion_krosmoz_sample' => 'nullable|array',
            'entities.*.conversion_krosmoz_sample.*' => 'numeric',
            'entities.*.conversion_sample_rows' => 'nullable|array',
            'entities.*.conversion_sample_rows.*.dofus_level' => 'nullable|numeric',
            'entities.*.conversion_sample_rows.*.dofus_value' => 'nullable|numeric',
            'entities.*.conversion_sample_rows.*.krosmoz_level' => 'nullable|numeric',
            'entities.*.conversion_sample_rows.*.krosmoz_value' => 'nullable|numeric',
            'entities.*.forgemagie_allowed' => 'nullable|boolean',
            'entities.*.forgemagie_max' => 'nullable|integer',
            'entities.*.base_price_per_unit' => 'nullable|numeric',
            'entities.*.rune_price_per_unit' => 'nullable|numeric',
        ]);

        $characteristic->update([
            'name' => $data['name'],
            'short_name' => $data['short_name'] ?? null,
            'descriptions' => $data['description'] ?? null,
            'helper' => $data['helper'] ?? null,
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? null,
            'type' => $data['type'],
            'unit' => $data['unit'] ?? null,
            'sort_order' => $data['sort_order'] ?? $characteristic->sort_order,
        ]);

        $group = $this->inferPrimaryGroup($characteristic);
        $allowedForGroup = self::ENTITIES_BY_GROUP[$group] ?? [];
        $entitiesToProcess = array_filter(
            $data['entities'] ?? [],
            fn (array $ent): bool => in_array($ent['entity'] ?? '', $allowedForGroup, true)
        );
        $sentEntities = array_column($entitiesToProcess, 'entity');
        foreach ($entitiesToProcess as $ent) {
            $entity = $ent['entity'];
            $this->updateGroupRow($characteristic->id, $entity, $ent);
        }

        $toRemove = array_filter($allowedForGroup, fn (string $e): bool => $e !== '*' && ! in_array($e, $sentEntities, true));
        foreach ($toRemove as $entity) {
            $this->deleteGroupRow($characteristic->id, $entity, $group);
        }

        $this->deleteOrphanGroupRows($characteristic->id, $group);

        $this->getter->clearCache();
        $this->colorCssGenerator->generate();

        return redirect()->route('admin.characteristics.show', $characteristic_key)->with('success', 'Caractéristique mise à jour.');
    }

    /**
     * Upload d'icône pour une caractéristique (Spatie Media Library).
     * Requiert characteristic_id ; attache le média à la caractéristique et met à jour la colonne icon.
     */
    public function uploadIcon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'image', 'max:2048'], // 2MB
            'characteristic_id' => ['required', 'integer', 'exists:characteristics,id'],
        ]);

        $characteristic = Characteristic::findOrFail($validated['characteristic_id']);
        $this->authorize('update', $characteristic);

        $characteristic->clearMediaCollection('icons');
        $ext = $request->file('file')->getClientOriginalExtension() ?: 'png';
        $customName = $characteristic->getMediaFileNameForCollection('icons', $ext);
        $adder = $characteristic->addMediaFromRequest('file');
        if ($customName !== null && $customName !== '') {
            $adder->usingFileName($customName);
        }
        $media = $adder->toMediaCollection('icons');
        $characteristic->update(['icon' => $media->getUrl()]);

        return response()->json([
            'success' => true,
            'icon' => $media->getUrl(),
        ]);
    }

    /**
     * Liste des caractéristiques regroupées par groupe (creature, object, spell) pour le menu gauche.
     *
     * @return array<string, list<array{id: string, name: string, short_name: string|null, icon: string|null, color: string|null}>>
     */
    private function buildCharacteristicsByGroup(): array
    {
        $byGroup = [
            'creature' => [],
            'object' => [],
            'spell' => [],
        ];
        $characteristics = Characteristic::orderBy('sort_order')->orderBy('key')->get();
        foreach ($characteristics as $c) {
            $group = $this->inferPrimaryGroup($c);
            $byGroup[$group][] = [
                'id' => $c->key,
                'name' => $c->name,
                'short_name' => $c->short_name,
                'icon' => $c->icon,
                'color' => $c->color,
            ];
        }
        return $byGroup;
    }

    /**
     * Navigation par entité : pour chaque entité, liste des caractéristiques qui ont une ligne pour cette entité.
     *
     * @return array<string, list<array{id: string, name: string}>>
     */
    private function buildEntityNav(): array
    {
        $byEntity = [];
        foreach (self::ENTITIES as $entity) {
            $byEntity[$entity] = [];
        }
        $ids = [];
        foreach (CharacteristicCreature::select('characteristic_id', 'entity')->distinct()->get() as $row) {
            $ids[$row->entity][] = $row->characteristic_id;
        }
        foreach (CharacteristicObject::select('characteristic_id', 'entity')->distinct()->get() as $row) {
            $ids[$row->entity][] = $row->characteristic_id;
        }
        foreach (CharacteristicSpell::select('characteristic_id', 'entity')->distinct()->get() as $row) {
            $ids[$row->entity][] = $row->characteristic_id;
        }
        foreach ($ids as $entity => $characteristicIds) {
            $characteristicIds = array_unique($characteristicIds);
            $chars = Characteristic::whereIn('id', $characteristicIds)->orderBy('sort_order')->orderBy('key')->get();
            $byEntity[$entity] = $chars->map(fn ($c) => ['id' => $c->key, 'name' => $c->name ?? $c->key])->all();
        }
        return $byEntity;
    }

    /**
     * @param CharacteristicCreature|CharacteristicObject|CharacteristicSpell $row
     * @return array<string, mixed>
     */
    private function groupRowToEntity(CharacteristicCreature|CharacteristicObject|CharacteristicSpell $row, Characteristic $characteristic): array
    {
        $out = [
            'entity' => $row->entity,
            'db_column' => $row->db_column,
            'min' => $row->min,
            'max' => $row->max,
            'formula' => $row->formula,
            'formula_display' => $row->formula_display,
            'default_value' => $row->default_value,
            'conversion_formula' => $row->conversion_formula,
            'conversion_dofus_sample' => $row->conversion_dofus_sample,
            'conversion_krosmoz_sample' => $row->conversion_krosmoz_sample,
            'conversion_sample_rows' => $row->conversion_sample_rows,
            'forgemagie_allowed' => false,
            'forgemagie_max' => 0,
            'base_price_per_unit' => null,
            'rune_price_per_unit' => null,
        ];
        if ($row instanceof CharacteristicObject) {
            $out['forgemagie_allowed'] = $row->forgemagie_allowed;
            $out['forgemagie_max'] = $row->forgemagie_max;
            $out['base_price_per_unit'] = $row->base_price_per_unit;
            $out['rune_price_per_unit'] = $row->rune_price_per_unit;
        }
        return $out;
    }

    /**
     * Construit les variables par défaut pour la prévisualisation (autres que la variable d’axe).
     *
     * @param array{type: string, characteristic?: string, entries?: list<array{value?: string}>} $decoded
     * @return array<string, int|float>
     */
    private function buildDefaultVariablesForPreview(string $formula, array $decoded, string $axisVar, string $entity): array
    {
        $defaults = [];
        $collectVar = function (string $varId) use ($axisVar, $entity, &$defaults): void {
            if ($varId === $axisVar || isset($defaults[$varId])) {
                return;
            }
            $lim = $this->getter->getLimitsByField($varId, $entity);
            $defaults[$varId] = $lim !== null ? (int) round(($lim['min'] + $lim['max']) / 2) : 10;
        };

        if ($decoded['type'] === 'table' && isset($decoded['entries'])) {
            foreach ($decoded['entries'] as $entry) {
                $v = $entry['value'] ?? null;
                if (is_string($v) && $v !== '' && preg_match_all('/\[(\w+)\]/', $v, $m)) {
                    foreach (array_unique($m[1]) as $varId) {
                        $collectVar($varId);
                    }
                }
            }
        } else {
            if (preg_match_all('/\[(\w+)\]/', $formula, $m)) {
                foreach (array_unique($m[1]) as $varId) {
                    $collectVar($varId);
                }
            }
        }

        return $defaults;
    }

    /**
     * Fusionne les définitions d'entités avec des valeurs par défaut pour les entités du groupe uniquement.
     * Évite de renvoyer des entités d'autres groupes (object/spell pour une caractéristique creature).
     */
    private function mergeDefaultEntityDefinitionsForGroup(array $entities, string $group): array
    {
        $byEntity = [];
        foreach ($entities as $ent) {
            $e = $ent['entity'] ?? null;
            if (is_string($e)) {
                $byEntity[$e] = $ent;
            }
        }
        $allowed = self::ENTITIES_BY_GROUP[$group] ?? ['*'];
        $out = [];
        foreach ($allowed as $entity) {
            $out[] = $byEntity[$entity] ?? [
                'entity' => $entity,
                'db_column' => null,
                'min' => null,
                'max' => null,
                'formula' => null,
                'formula_display' => null,
                'default_value' => null,
                'conversion_formula' => null,
                'forgemagie_allowed' => false,
                'forgemagie_max' => 0,
                'base_price_per_unit' => null,
                'rune_price_per_unit' => null,
            ];
        }
        return $out;
    }

    /**
     * Normalise la clé d'une caractéristique : ajoute le suffixe du groupe si absent.
     * Ex. life_dice + groupe creature → life_dice_creature ; life_creature → inchangé.
     */
    private function normalizeCharacteristicKey(string $key, string $group): string
    {
        $suffix = '_' . $group;
        if (strlen($key) >= strlen($suffix) && str_ends_with($key, $suffix)) {
            return $key;
        }
        return $key . $suffix;
    }

    private function updateGroupRow(int $characteristicId, string $entity, array $data): void
    {
        $sampleRows = $this->normalizeSampleRowsForSave($data['conversion_sample_rows'] ?? null);
        $common = [
            'db_column' => $data['db_column'] ?? null,
            'min' => $data['min'] ?? null,
            'max' => $data['max'] ?? null,
            'formula' => $data['formula'] ?? null,
            'formula_display' => $data['formula_display'] ?? null,
            'default_value' => $data['default_value'] ?? null,
            'conversion_formula' => $data['conversion_formula'] ?? null,
            'conversion_dofus_sample' => $this->deriveSampleFromRows($sampleRows, 'dofus') ?? $this->normalizeSampleForSave($data['conversion_dofus_sample'] ?? null),
            'conversion_krosmoz_sample' => $this->deriveSampleFromRows($sampleRows, 'krosmoz') ?? $this->normalizeSampleForSave($data['conversion_krosmoz_sample'] ?? null),
            'conversion_sample_rows' => $sampleRows,
        ];

        if (in_array($entity, ['*', 'monster', 'class', 'npc'], true)) {
            CharacteristicCreature::updateOrCreate(
                ['characteristic_id' => $characteristicId, 'entity' => $entity],
                $common
            );
        }
        if (in_array($entity, ['*', 'item', 'consumable', 'resource', 'panoply'], true)) {
            $basePrice = $data['base_price_per_unit'] ?? null;
            $runePrice = $data['rune_price_per_unit'] ?? null;
            CharacteristicObject::updateOrCreate(
                ['characteristic_id' => $characteristicId, 'entity' => $entity],
                array_merge($common, [
                    'forgemagie_allowed' => (bool) ($data['forgemagie_allowed'] ?? false),
                    'forgemagie_max' => (int) ($data['forgemagie_max'] ?? 0),
                    'base_price_per_unit' => $basePrice !== null && $basePrice !== '' ? (float) $basePrice : null,
                    'rune_price_per_unit' => $runePrice !== null && $runePrice !== '' ? (float) $runePrice : null,
                ])
            );
        }
        if ($entity === 'spell' || $entity === '*') {
            CharacteristicSpell::updateOrCreate(
                ['characteristic_id' => $characteristicId, 'entity' => $entity],
                $common
            );
        }
    }

    /**
     * Supprime la ligne d'une entité donnée pour une caractéristique (suppression de la spécificité).
     */
    private function deleteGroupRow(int $characteristicId, string $entity, string $group): void
    {
        if ($group === 'creature' && in_array($entity, ['monster', 'class', 'npc'], true)) {
            CharacteristicCreature::where('characteristic_id', $characteristicId)->where('entity', $entity)->delete();
        }
        if ($group === 'object' && in_array($entity, ['item', 'consumable', 'resource', 'panoply'], true)) {
            CharacteristicObject::where('characteristic_id', $characteristicId)->where('entity', $entity)->delete();
        }
        if ($group === 'spell' && $entity === 'spell') {
            CharacteristicSpell::where('characteristic_id', $characteristicId)->where('entity', $entity)->delete();
        }
    }

    /**
     * Supprime les lignes créées par erreur dans les tables des autres groupes (ex. lignes object/spell pour une caractéristique creature).
     */
    private function deleteOrphanGroupRows(int $characteristicId, string $currentGroup): void
    {
        if ($currentGroup !== 'creature') {
            CharacteristicCreature::where('characteristic_id', $characteristicId)->delete();
        }
        if ($currentGroup !== 'object') {
            CharacteristicObject::where('characteristic_id', $characteristicId)->delete();
        }
        if ($currentGroup !== 'spell') {
            CharacteristicSpell::where('characteristic_id', $characteristicId)->delete();
        }
    }
}
