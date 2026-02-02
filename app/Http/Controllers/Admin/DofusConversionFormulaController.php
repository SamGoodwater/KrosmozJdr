<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\ConversionHandlerRegistry;
use App\Services\Scrapping\V2\Conversion\DofusDbConversionFormulas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API d'aperçu des formules de conversion DofusDB → KrosmozJDR.
 * Utilisée par Admin > Caractéristiques pour les graphiques (section Formules de conversion Dofus → JDR).
 * L'édition des formules se fait dans la page Caractéristiques.
 */
class DofusConversionFormulaController extends Controller
{
    public function __construct(
        private readonly DofusDbConversionFormulas $conversionFormulas,
        private readonly ConversionHandlerRegistry $handlerRegistry
    ) {
    }

    /**
     * Liste des handlers disponibles (value + batch) pour le select admin.
     *
     * @return JsonResponse { handlers: list<{name: string, label: string, type: string}> }
     */
    public function handlers(): JsonResponse
    {
        return response()->json([
            'handlers' => $this->handlerRegistry->allHandlersForSelect(),
        ]);
    }

    /**
     * Aperçu graphique : points (d Dofus → k JDR) pour une formule.
     * Si conversion_formula est fourni (édition depuis la page caractéristique), il est utilisé pour l'aperçu.
     */
    public function formulaPreview(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'characteristic_id' => 'required|string|max:64',
            'entity' => 'required|in:monster,class,item',
            'd_min' => 'nullable|integer',
            'd_max' => 'nullable|integer',
            'steps' => 'nullable|integer|min:5|max:200',
            'conversion_formula' => 'nullable|string',
        ]);

        $points = $this->conversionFormulas->getPreviewPoints(
            $validated['characteristic_id'],
            $validated['entity'],
            isset($validated['d_min']) ? (int) $validated['d_min'] : null,
            isset($validated['d_max']) ? (int) $validated['d_max'] : null,
            (int) ($validated['steps'] ?? 50),
            isset($validated['conversion_formula']) && $validated['conversion_formula'] !== '' ? (string) $validated['conversion_formula'] : null
        );

        return response()->json(['points' => $points]);
    }
}
