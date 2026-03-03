<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Characteristic;
use App\Models\DofusdbEffectMapping;
use App\Models\SubEffect;
use App\Services\Scrapping\Core\Conversion\SpellEffects\DofusdbEffectMappingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Administration des mappings effectId DofusDB → sous-effet Krosmoz (effets de sorts).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 */
class DofusdbEffectMappingController extends Controller
{
    public function __construct(
        private readonly DofusdbEffectMappingService $mappingService
    ) {
    }

    /**
     * Page liste des mappings + formulaire création/édition.
     */
    public function index(Request $request): InertiaResponse
    {
        $effectIdFilter = trim((string) $request->query('effect_id', ''));

        $mappings = DofusdbEffectMapping::orderBy('dofusdb_effect_id')->get()
            ->map(fn (DofusdbEffectMapping $m) => $this->formatMappingForResponse($m))
            ->values()
            ->all();

        $subEffectsForSelect = SubEffect::orderBy('slug')
            ->get(['id', 'slug'])
            ->map(fn ($s) => ['value' => $s->slug, 'label' => $s->slug])
            ->values()
            ->all();

        $characteristicSourceOptions = [
            ['value' => DofusdbEffectMapping::SOURCE_ELEMENT, 'label' => 'Élément (résolu à l’exécution)'],
            ['value' => DofusdbEffectMapping::SOURCE_CHARACTERISTIC, 'label' => 'Caractéristique (clé fixe)'],
            ['value' => DofusdbEffectMapping::SOURCE_NONE, 'label' => 'Aucune'],
        ];

        $characteristicsForSelect = Characteristic::orderBy('sort_order')->orderBy('key')
            ->get(['id', 'key', 'name'])
            ->map(fn ($c) => ['value' => $c->key, 'label' => ($c->name ?? $c->key) . ' (' . $c->key . ')'])
            ->values()
            ->all();

        return Inertia::render('Admin/dofusdb-effect-mappings/Index', [
            'effectIdFilter' => $effectIdFilter,
            'mappings' => $mappings,
            'subEffectsForSelect' => $subEffectsForSelect,
            'characteristicSourceOptions' => $characteristicSourceOptions,
            'characteristicsForSelect' => $characteristicsForSelect,
        ]);
    }

    /**
     * Création d'un mapping.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dofusdb_effect_id' => 'required|integer|min:1|unique:dofusdb_effect_mappings,dofusdb_effect_id',
            'sub_effect_slug' => 'required|string|max:64|exists:sub_effects,slug',
            'characteristic_source' => 'required|string|in:element,characteristic,none',
            'characteristic_key' => 'nullable|string|max:64',
        ]);

        $mapping = DofusdbEffectMapping::create([
            'dofusdb_effect_id' => (int) $validated['dofusdb_effect_id'],
            'sub_effect_slug' => $validated['sub_effect_slug'],
            'characteristic_source' => $validated['characteristic_source'],
            'characteristic_key' => $this->normalizeCharacteristicKey($validated['characteristic_source'], $validated['characteristic_key'] ?? null),
        ]);

        $this->mappingService->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Mapping créé.',
            'mapping' => $this->formatMappingForResponse($mapping),
        ], 201);
    }

    /**
     * Mise à jour d'un mapping.
     */
    public function update(Request $request, DofusdbEffectMapping $mapping): JsonResponse
    {
        $validated = $request->validate([
            'dofusdb_effect_id' => 'sometimes|integer|min:1',
            'sub_effect_slug' => 'sometimes|string|max:64|exists:sub_effects,slug',
            'characteristic_source' => 'sometimes|string|in:element,characteristic,none',
            'characteristic_key' => 'nullable|string|max:64',
        ]);

        if (isset($validated['dofusdb_effect_id']) && (int) $validated['dofusdb_effect_id'] !== $mapping->dofusdb_effect_id) {
            $request->validate(['dofusdb_effect_id' => 'unique:dofusdb_effect_mappings,dofusdb_effect_id,' . $mapping->id]);
        }

        $mapping->fill(array_filter([
            'dofusdb_effect_id' => isset($validated['dofusdb_effect_id']) ? (int) $validated['dofusdb_effect_id'] : null,
            'sub_effect_slug' => $validated['sub_effect_slug'] ?? null,
            'characteristic_source' => $validated['characteristic_source'] ?? null,
            'characteristic_key' => isset($validated['characteristic_source']) || array_key_exists('characteristic_key', $validated)
                ? $this->normalizeCharacteristicKey(
                    $validated['characteristic_source'] ?? $mapping->characteristic_source,
                    $validated['characteristic_key'] ?? $mapping->characteristic_key
                )
                : null,
        ], fn ($v) => $v !== null));
        $mapping->save();

        $this->mappingService->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Mapping mis à jour.',
            'mapping' => $this->formatMappingForResponse($mapping),
        ]);
    }

    /**
     * Suppression d'un mapping.
     */
    public function destroy(DofusdbEffectMapping $mapping): JsonResponse
    {
        $mapping->delete();
        $this->mappingService->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Mapping supprimé.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatMappingForResponse(DofusdbEffectMapping $m): array
    {
        return [
            'id' => $m->id,
            'dofusdb_effect_id' => $m->dofusdb_effect_id,
            'sub_effect_slug' => $m->sub_effect_slug,
            'characteristic_source' => $m->characteristic_source,
            'characteristic_key' => $m->characteristic_key,
        ];
    }

    private function normalizeCharacteristicKey(string $source, ?string $key): ?string
    {
        if ($source !== DofusdbEffectMapping::SOURCE_CHARACTERISTIC) {
            return null;
        }
        return $key !== null && $key !== '' ? $key : null;
    }
}
