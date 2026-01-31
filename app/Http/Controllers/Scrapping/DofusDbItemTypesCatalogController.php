<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Catalogue DofusDB : item-types + superTypes (paginated).
 *
 * @description
 * DofusDB limite fréquemment les listes à 50 éléments par page.
 * Ce contrôleur pagine l'endpoint `/item-types` et retourne une vue
 * "mapping-friendly" groupée par `superTypeId`.
 */
class DofusDbItemTypesCatalogController extends Controller
{
    public function __construct(private DofusDbItemTypesCatalogService $catalog) {}

    public function index(Request $request): JsonResponse
    {
        $skipCache = filter_var($request->query('skip_cache', false), FILTER_VALIDATE_BOOLEAN);
        $lang = (string) $request->query('lang', (string) config('scrapping.data_collect.default_language', 'fr'));

        $catalog = $this->catalog->getCatalog($lang, $skipCache);

        return response()->json([
            'success' => true,
            'data' => [
                'meta' => $catalog['meta'] ?? [],
                'superTypes' => $catalog['superTypes'] ?? [],
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }
}

