<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use Illuminate\Support\Facades\DB;

/**
 * Persistance définition d’effet + degrés (admin / fiche sort).
 */
final class EffectGroupUpdateService
{
    public function __construct(
        private readonly EffectGroupEditorDataService $editorData
    ) {}

    /**
     * @param  array<string, mixed>  $validated  Sortie de UpdateEffectGroupRequest::validated()
     */
    public function updateGroup(Effect $definition, array $validated): void
    {
        $allowedIds = $this->editorData->degreeIdsForEffect($definition);

        $degreeIds = collect($validated['degrees'])->pluck('id');
        if ($degreeIds->unique()->count() !== $degreeIds->count()) {
            abort(422, 'Identifiants de degrés en double.');
        }
        if ($degreeIds->count() !== $allowedIds->count() || $degreeIds->diff($allowedIds)->isNotEmpty()) {
            abort(422, 'La liste des degrés doit correspondre exactement aux degrés de cette définition.');
        }

        $common = $validated['common'];

        DB::transaction(function () use ($validated, $common, $definition): void {
            $definition->update([
                'name' => $common['name'] ?? null,
                'description' => $common['description'] ?? null,
                'target_type' => $common['target_type'] ?? Effect::TARGET_DIRECT,
            ]);

            foreach ($validated['degrees'] as $row) {
                $degree = EffectDegree::query()->whereKey($row['id'])->where('effect_id', $definition->id)->firstOrFail();
                $degree->update([
                    'slug' => $row['slug'] ?? null,
                    'area' => $row['area'] ?? null,
                    'required_creature_level' => array_key_exists('required_creature_level', $row)
                        ? $row['required_creature_level']
                        : $degree->required_creature_level,
                ]);
                $this->syncSubEffects($degree, $row['effect_sub_effects']);
                $degree->load(['effectSubEffects', 'effect']);
                $newSignature = app(IntegrationService::class)->rebuildConfigSignatureForEffectDegree($degree);
                if ($newSignature !== null) {
                    $degree->update(['config_signature' => $newSignature]);
                }
            }
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function syncSubEffects(EffectDegree $degree, array $rows): void
    {
        foreach ($rows as $i => $row) {
            $params = $row['params'] ?? [];
            if (array_key_exists('monster_id', $params) && $params['monster_id'] === '') {
                $rows[$i]['params']['monster_id'] = null;
            }
        }

        $degree->effectSubEffects()->delete();
        $sanitizer = new EffectTextSanitizer;
        foreach ($rows as $i => $row) {
            $params = $row['params'] ?? null;
            if ($params && ! empty($params['value_formula'])) {
                $params['value_formula'] = $sanitizer->sanitize($params['value_formula']);
            }
            if ($params && ! empty($params['value_formula_crit'])) {
                $params['value_formula_crit'] = $sanitizer->sanitize($params['value_formula_crit']);
            }
            if ($params && ! empty($params['life_steal_formula'])) {
                $params['life_steal_formula'] = $sanitizer->sanitize($params['life_steal_formula']);
            }
            if ($params && ! empty($params['cells_formula'])) {
                $params['cells_formula'] = $sanitizer->sanitize($params['cells_formula']);
            }
            $durationFormula = $row['duration_formula'] ?? null;
            if ($durationFormula) {
                $durationFormula = $sanitizer->sanitize($durationFormula);
            }
            $logicCondition = $row['logic_condition'] ?? null;
            if ($logicCondition) {
                $logicCondition = $sanitizer->sanitize($logicCondition);
            }
            $degree->effectSubEffects()->create([
                'sub_effect_id' => $row['sub_effect_id'],
                'order' => $row['order'] ?? $i,
                'scope' => $row['scope'] ?? 'general',
                'value_min' => $row['value_min'] ?? null,
                'value_max' => $row['value_max'] ?? null,
                'dice_num' => $row['dice_num'] ?? null,
                'dice_side' => $row['dice_side'] ?? null,
                'duration_formula' => $durationFormula,
                'logic_group' => $row['logic_group'] ?? null,
                'logic_operator' => $row['logic_operator'] ?? null,
                'logic_condition' => $logicCondition,
                'crit_only' => (bool) ($row['crit_only'] ?? false),
                'params' => $params,
            ]);
        }
    }
}
