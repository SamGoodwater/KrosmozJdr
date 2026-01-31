<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Models\Type\ConsumableType;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * API de gestion des typeId DofusDB détectés (registry) pour les consommables.
 *
 * Permet de marquer un typeId comme "utilisé" (allowed), "non utilisé" (blocked)
 * ou le remettre en attente (pending).
 */
class ConsumableTypeRegistryController extends Controller
{
    /**
     * Cache local (requête) pour éviter de refetch le même typeId.
     *
     * @var array<int, string|null>
     */
    private array $itemTypeNameCache = [];

    public function __construct(private DofusDbClient $dofusDbClient) {}

    /**
     * Normalise un libellé métier (used/unused) vers le stockage (allowed/blocked).
     */
    private function normalizeDecision(string $decision): string
    {
        return match ($decision) {
            'used' => ConsumableType::DECISION_ALLOWED,
            'unused' => ConsumableType::DECISION_BLOCKED,
            default => $decision,
        };
    }

    private function stripDofusdbSuffix(?string $name): ?string
    {
        if (!$name) return $name;
        $n = trim($name);
        if (str_ends_with($n, ' (DofusDB)')) {
            $n = trim(substr($n, 0, -strlen(' (DofusDB)')));
        }
        return $n;
    }

    private function fetchDofusdbItemTypeName(int $typeId, bool $skipCache = false): ?string
    {
        if ($typeId <= 0) return null;
        if (array_key_exists($typeId, $this->itemTypeNameCache)) {
            return $this->itemTypeNameCache[$typeId];
        }

        $baseUrl = (string) config('scrapping.data_collect.dofusdb_base_url', 'https://api.dofusdb.fr');
        $lang = (string) config('scrapping.data_collect.default_language', 'fr');
        $url = rtrim($baseUrl, '/') . "/item-types/{$typeId}?lang={$lang}";

        try {
            $payload = $this->dofusDbClient->getJson($url, ['skip_cache' => $skipCache]);

            // DofusDB peut renvoyer l'entité directement, ou une forme "data".
            $row = $payload;
            if (isset($payload['data']) && is_array($payload['data']) && isset($payload['data'][0]) && is_array($payload['data'][0])) {
                $row = (array) $payload['data'][0];
            }

            $name = null;
            if (isset($row['name']) && is_array($row['name'])) {
                $cand = $row['name']['fr'] ?? $row['name'][$lang] ?? null;
                if (is_string($cand) && trim($cand) !== '') {
                    $name = trim($cand);
                }
            } elseif (isset($row['name']) && is_string($row['name']) && trim($row['name']) !== '') {
                $name = trim($row['name']);
            }

            $name = $this->stripDofusdbSuffix($name);
            $this->itemTypeNameCache[$typeId] = $name;
            return $name;
        } catch (\Throwable $e) {
            Log::debug('consumable-types: cannot resolve dofusdb item-type name', [
                'typeId' => $typeId,
                'error' => $e->getMessage(),
            ]);
            $this->itemTypeNameCache[$typeId] = null;
            return null;
        }
    }

    /**
     * Liste des ConsumableType avec dofusdb_type_id, filtrable par décision.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ConsumableType::class);

        $decision = $request->query('decision');
        if (is_string($decision)) {
            $decision = $this->normalizeDecision($decision);
        }

        $query = ConsumableType::query()
            ->whereNotNull('dofusdb_type_id')
            ->orderByDesc('last_seen_at');

        if (is_string($decision) && in_array($decision, ['pending', 'allowed', 'blocked'], true)) {
            $query->where('decision', $decision);
        }

        $rows = $query->get([
            'id',
            'name',
            'dofusdb_type_id',
            'decision',
            'seen_count',
            'last_seen_at',
        ]);

        // Améliorer les placeholders "DofusDB type #X" en allant chercher le vrai nom côté DofusDB.
        foreach ($rows as $model) {
            $typeId = is_numeric($model->dofusdb_type_id) ? (int) $model->dofusdb_type_id : 0;
            if ($typeId <= 0) continue;

            $currentName = $this->stripDofusdbSuffix(is_string($model->name) ? $model->name : null);
            $isPlaceholder = $currentName === null || $currentName === '' || str_starts_with($currentName, 'DofusDB type #');

            if (!$isPlaceholder) {
                $model->name = $currentName;
                continue;
            }

            $resolved = $this->fetchDofusdbItemTypeName($typeId, false);
            if ($resolved) {
                $model->name = $resolved;
                try {
                    $model->save();
                } catch (\Throwable) {
                    // Non bloquant
                }
            } else {
                $model->name = $currentName ?: $model->name;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * Liste des types en attente de décision.
     */
    public function pending(Request $request): JsonResponse
    {
        $request->merge(['decision' => 'pending']);
        return $this->index($request);
    }

    /**
     * Met à jour la décision d'un type détecté.
     */
    public function updateDecision(Request $request, ConsumableType $consumableType): JsonResponse
    {
        $this->authorize('update', $consumableType);

        if ($consumableType->dofusdb_type_id === null) {
            return response()->json([
                'success' => false,
                'message' => 'Ce type n’est pas lié à un typeId DofusDB.',
            ], 422);
        }

        $validated = $request->validate([
            'decision' => ['required', 'string', 'in:pending,allowed,blocked,used,unused'],
        ]);

        $consumableType->decision = $this->normalizeDecision($validated['decision']);
        $consumableType->save();

        return response()->json([
            'success' => true,
            'data' => $consumableType->only([
                'id',
                'name',
                'dofusdb_type_id',
                'decision',
                'seen_count',
                'last_seen_at',
            ]),
        ]);
    }
}

