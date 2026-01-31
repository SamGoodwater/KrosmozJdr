<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Catalog\DofusDbMonsterRacesCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Catalogue des races de monstres DofusDB.
 *
 * @description
 * Utilisé par l'UI pour afficher des noms (au lieu d'IDs) et construire des filtres.
 */
class DofusDbMonsterRacesCatalogController extends Controller
{
    public function __construct(private DofusDbMonsterRacesCatalogService $catalog) {}

    public function index(Request $request): JsonResponse
    {
        // UX: endpoint consommé par l'UI (auth requis comme le reste du scrapping)
        $lang = (string) ($request->query('lang') ?: 'fr');
        $skipCache = (bool) filter_var($request->query('skip_cache'), FILTER_VALIDATE_BOOLEAN);

        $rows = $this->catalog->listAll($lang, $skipCache);

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }
}

