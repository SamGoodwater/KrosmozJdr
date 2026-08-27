<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Scrapping\Concerns\AppliesTypeRegistryListFilters;
use App\Http\Controllers\Scrapping\Concerns\BulkDecisionUpdateTrait;
use App\Http\Controllers\Scrapping\Concerns\UpdatesCatalogVisibilityTrait;
use App\Models\Type\SpellType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API d'administration des types de sorts (SpellType).
 *
 * Flags persistés : `show_in_catalog` (visible / tableaux) et `allow_scrap` (maj DofusDB).
 */
class SpellTypeApiController extends Controller
{
    use AppliesTypeRegistryListFilters;
    use BulkDecisionUpdateTrait;
    use UpdatesCatalogVisibilityTrait;

    /**
     * Liste des types de sorts (filtrable par flags).
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', SpellType::class);

        $query = SpellType::query()->orderBy('name');
        $this->applyTypeRegistryListFilters($query, $request);

        $rows = $query->get([
            'id',
            'name',
            'description',
            'color',
            'icon',
            'state',
            'show_in_catalog',
            'allow_scrap',
            'created_at',
            'updated_at',
        ]);

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * Mise à jour en masse des flags (et `state` optionnel, historique).
     *
     * @example
     * PATCH /api/types/spell-types/bulk
     * { "ids":[1,2,3], "show_in_catalog":true }
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        return $this->bulkUpdateDecision($request, SpellType::class);
    }

    /**
     * Affiche ou masque ce type dans les filtres catalogue.
     *
     * @example PATCH /api/types/spell-types/{spellType}/catalog { "show_in_catalog": true }
     */
    public function updateCatalog(Request $request, SpellType $spellType): JsonResponse
    {
        return $this->updateShowInCatalog($request, $spellType);
    }

    /**
     * Supprime un type de sort (soft delete).
     *
     * @example
     * DELETE /api/types/spell-types/{spellType}
     */
    public function destroy(SpellType $spellType): JsonResponse
    {
        $this->authorize('delete', $spellType);

        $spellType->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
