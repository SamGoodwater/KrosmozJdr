<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API d’aperçu des formules de conversion Dofus → Krosmoz (conversion_formula dans les tables de groupe).
 */
class DofusConversionFormulaController extends Controller
{
    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly CharacteristicFormulaService $formulaService,
        private readonly DofusConversionService $conversionService
    ) {
    }

    public function handlers(): JsonResponse
    {
        return response()->json(['handlers' => []]);
    }

    public function formulaPreview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'characteristic_id' => 'required|string|max:64',
            'entity' => 'required|in:monster,class,item,spell,resource,consumable,panoply',
            'd_min' => 'nullable|integer',
            'd_max' => 'nullable|integer',
            'steps' => 'nullable|integer|min:5|max:200',
            'conversion_formula' => 'nullable|string',
        ]);

        $formula = isset($validated['conversion_formula']) && $validated['conversion_formula'] !== ''
            ? (string) $validated['conversion_formula']
            : $this->getter->getConversionFormula($validated['characteristic_id'], $validated['entity']);
        if ($formula === null || $formula === '') {
            return response()->json(['points' => []]);
        }

        $dMin = isset($validated['d_min']) ? (int) $validated['d_min'] : 0;
        $dMax = isset($validated['d_max']) ? (int) $validated['d_max'] : 200;
        $steps = (int) ($validated['steps'] ?? 50);
        if ($dMax <= $dMin || $steps < 2) {
            return response()->json(['points' => []]);
        }

        $step = ($dMax - $dMin) / ($steps - 1);
        $levelKrosmoz = 10;
        $points = [];
        for ($i = 0; $i < $steps; $i++) {
            $d = (float) ($dMin + $i * $step);
            $vars = ['d' => $d, 'level' => $levelKrosmoz];
            $y = $this->formulaService->evaluate($formula, $vars);
            $points[] = ['x' => (int) round($d), 'y' => $y !== null ? round($y, 2) : 0];
        }

        return response()->json(['points' => $points]);
    }
}
