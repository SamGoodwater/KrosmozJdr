<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserFavorite;
use App\Services\Search\GlobalSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CRUD des favoris utilisateur (persistés en BDD).
 *
 * @example
 * GET /api/favorites → ids_by_type + items hydratés
 */
class UserFavoriteController extends Controller
{
    public function __construct(
        private readonly GlobalSearchService $globalSearch
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'entity_type' => ['sometimes', 'nullable', 'string', 'max:120', Rule::in(GlobalSearchService::ALLOWED_TYPES)],
            'hydrate' => ['sometimes', 'boolean'],
        ]);

        $query = UserFavorite::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        if (! empty($data['entity_type'])) {
            $query->where('entity_type', (string) $data['entity_type']);
        }

        $favorites = $query->get();

        $idsByType = [];
        foreach ($favorites as $favorite) {
            $type = (string) $favorite->entity_type;
            $idsByType[$type] ??= [];
            $idsByType[$type][] = (string) $favorite->entity_id;
        }

        $hydrate = $request->boolean('hydrate', true);

        $items = [];
        if ($hydrate) {
            $pairs = $favorites->map(static fn (UserFavorite $f): array => [
                'entity_type' => $f->entity_type,
                'entity_id' => $f->entity_id,
            ])->all();
            $items = $this->globalSearch->resolveHits($user, $pairs);
        }

        return response()->json([
            'ids_by_type' => $idsByType,
            'items' => $items,
            'count' => $favorites->count(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'entity_type' => ['required', 'string', 'max:120', Rule::in(GlobalSearchService::ALLOWED_TYPES)],
            'entity_id' => ['required', 'integer', 'min:1'],
        ]);

        $favorite = UserFavorite::query()->firstOrCreate([
            'user_id' => $user->id,
            'entity_type' => (string) $data['entity_type'],
            'entity_id' => (int) $data['entity_id'],
        ]);

        return response()->json([
            'favorite' => $this->toPayload($favorite),
            'favorited' => true,
        ], $favorite->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'entity_type' => ['required', 'string', 'max:120', Rule::in(GlobalSearchService::ALLOWED_TYPES)],
            'entity_id' => ['required', 'integer', 'min:1'],
        ]);

        $deleted = UserFavorite::query()
            ->where('user_id', $user->id)
            ->where('entity_type', (string) $data['entity_type'])
            ->where('entity_id', (int) $data['entity_id'])
            ->delete();

        return response()->json([
            'success' => true,
            'favorited' => false,
            'deleted' => $deleted > 0,
        ]);
    }

    /**
     * @return array{id:string, entity_type:string, entity_id:int, created_at:?string}
     */
    private function toPayload(UserFavorite $favorite): array
    {
        return [
            'id' => (string) $favorite->id,
            'entity_type' => $favorite->entity_type,
            'entity_id' => (int) $favorite->entity_id,
            'created_at' => $favorite->created_at?->toISOString(),
        ];
    }
}
