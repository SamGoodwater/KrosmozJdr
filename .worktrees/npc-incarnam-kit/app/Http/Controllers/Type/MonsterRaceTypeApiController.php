<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Scrapping\Concerns\AppliesTypeRegistryListFilters;
use App\Http\Controllers\Scrapping\Concerns\BulkDecisionUpdateTrait;
use App\Http\Controllers\Scrapping\Concerns\UpdatesCatalogVisibilityTrait;
use App\Models\Type\MonsterRace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API d'administration des races de monstres (MonsterRace).
 *
 * Flags persistés : `show_in_catalog` (visible / tableaux) et `allow_scrap` (maj DofusDB).
 */
class MonsterRaceTypeApiController extends Controller
{
    use AppliesTypeRegistryListFilters;
    use BulkDecisionUpdateTrait;
    use UpdatesCatalogVisibilityTrait;

    /**
     * Liste des races (filtrable par flags).
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MonsterRace::class);

        $query = MonsterRace::query()->orderBy('name');
        $this->applyTypeRegistryListFilters($query, $request);

        $rows = $query->get([
            'id',
            'dofusdb_race_id',
            'name',
            'state',
            'id_super_race',
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
     * PATCH /api/types/monster-races/bulk
     * { "ids":[1,2,3], "allow_scrap":true, "show_in_catalog":false }
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        return $this->bulkUpdateDecision($request, MonsterRace::class);
    }

    /**
     * Affiche ou masque cette race dans les filtres catalogue.
     *
     * @example PATCH /api/types/monster-races/{monsterRace}/catalog { "show_in_catalog": true }
     */
    public function updateCatalog(Request $request, MonsterRace $monsterRace): JsonResponse
    {
        return $this->updateShowInCatalog($request, $monsterRace);
    }

    /**
     * Supprime une race (soft delete).
     *
     * @example
     * DELETE /api/types/monster-races/{monsterRace}
     */
    public function destroy(MonsterRace $monsterRace): JsonResponse
    {
        $this->authorize('delete', $monsterRace);

        $monsterRace->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
