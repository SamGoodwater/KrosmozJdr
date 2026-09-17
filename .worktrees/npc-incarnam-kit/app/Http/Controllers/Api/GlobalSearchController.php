<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Search\GlobalSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Recherche globale JSON (session web + cookies) pour le champ du header.
 */
class GlobalSearchController extends Controller
{
    public function __invoke(Request $request, GlobalSearchService $globalSearchService): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        /** @var array<int, string> $types */
        $types = array_values(array_filter((array) $request->query('types', []), fn ($v): bool => is_string($v)));
        /** @var array<int, string> $states */
        $states = array_values(array_filter((array) $request->query('states', []), fn ($v): bool => is_string($v)));

        $limit = (int) $request->integer('limit', 40);

        $payload = $globalSearchService->search($request->user(), $q, $types, $states, $limit);

        return response()->json([
            'results' => $payload['results'],
            'meta' => [
                'limit' => $limit,
                'hasMore' => $payload['hasMore'],
            ],
        ]);
    }
}
