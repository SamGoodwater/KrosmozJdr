<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Models\Entity\Classe;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Services\Scrapping\Config\ScrappingConfigLoader;
use App\Services\Scrapping\DataCollect\ConfigDrivenDofusDbCollector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint de recherche (collect-only) basé sur config JSON.
 *
 * @description
 * Permet de récupérer des listes d'entités depuis DofusDB via pagination/filters,
 * sans lancer l'import (collecte uniquement). Utile pour alimenter le tableau UI
 * et démarrer les tests.
 */
class ScrappingSearchController extends Controller
{
    public function __construct(
        private ScrappingConfigLoader $configLoader,
        private ConfigDrivenDofusDbCollector $collector,
    ) {}

    public function search(Request $request, string $entity): JsonResponse
    {
        // Source unique pour l'instant (refonte progressive)
        $source = 'dofusdb';

        // 404 si l'entité n'existe pas en config (évite les appels arbitraires)
        $available = $this->configLoader->listEntities($source);
        if (!in_array($entity, $available, true)) {
            return response()->json([
                'success' => false,
                'message' => "Entité '{$entity}' non supportée.",
                'timestamp' => now()->toISOString(),
            ], 404);
        }

        $filters = $this->extractFilters($request);
        $options = $this->extractOptions($request);

        $result = $this->collector->fetchManyResult($entity, $filters, $options);
        $items = $this->withExistsFlag($entity, $result['items']);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'meta' => $result['meta'],
                'query' => [
                    'filters' => $filters,
                    'options' => $options,
                ],
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * @return array<string,mixed>
     */
    private function extractFilters(Request $request): array
    {
        $filters = [];

        foreach (['id', 'idMin', 'idMax', 'name', 'raceId', 'levelMin', 'levelMax', 'breedId', 'typeId'] as $key) {
            if ($request->has($key)) {
                $filters[$key] = $request->query($key);
            }
        }

        // ids peut être "1,2,3" ou ["1","2"]
        if ($request->has('ids')) {
            $ids = $request->query('ids');
            if (is_string($ids)) {
                $parts = array_filter(array_map('trim', explode(',', $ids)));
                $filters['ids'] = $parts;
            } elseif (is_array($ids)) {
                $filters['ids'] = $ids;
            }
        }

        return $filters;
    }

    /**
     * Ajoute `exists` + `existing` (id interne) aux items de DofusDB.
     *
     * @param string $entity
     * @param array<int, array<string,mixed>> $items
     * @return array<int, array<string,mixed>>
     */
    private function withExistsFlag(string $entity, array $items): array
    {
        $modelClass = match ($entity) {
            'class' => Classe::class,
            'monster' => Monster::class,
            'item' => Item::class,
            'spell' => Spell::class,
            'panoply' => Panoply::class,
            'resource' => Resource::class,
            'consumable' => Consumable::class,
            default => null,
        };

        if (!$modelClass) {
            return $items;
        }

        $dofusIds = [];
        foreach ($items as $it) {
            if (!is_array($it)) continue;
            if (isset($it['id']) && (is_int($it['id']) || (is_string($it['id']) && ctype_digit($it['id'])))) {
                $dofusIds[] = (string) (int) $it['id'];
            }
        }
        $dofusIds = array_values(array_unique($dofusIds));
        if (empty($dofusIds)) {
            return $items;
        }

        /** @var array<string,int> $existingMap */
        try {
            $existingMap = $modelClass::query()
                ->whereIn('dofusdb_id', $dofusIds)
                ->pluck('id', 'dofusdb_id')
                ->all();
        } catch (\Throwable) {
            // Best-effort: en environnement sans DB/migrations (tests), on n'échoue pas la recherche.
            $existingMap = [];
        }

        foreach ($items as $i => $it) {
            if (!is_array($it)) continue;
            $idStr = isset($it['id']) ? (string) (int) $it['id'] : null;
            $existingId = ($idStr !== null && isset($existingMap[$idStr])) ? (int) $existingMap[$idStr] : null;
            $items[$i]['exists'] = $existingId !== null;
            $items[$i]['existing'] = $existingId ? ['id' => $existingId] : null;
        }

        return $items;
    }

    /**
     * @return array{skip_cache?:bool, limit?:int, max_pages?:int, max_items?:int, start_skip?:int}
     */
    private function extractOptions(Request $request): array
    {
        $options = [];

        if ($request->has('skip_cache')) {
            $options['skip_cache'] = filter_var($request->query('skip_cache'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('limit')) {
            $limit = $request->integer('limit');
            $options['limit'] = max(1, min(200, $limit));
        }

        if ($request->has('start_skip')) {
            $startSkip = $request->integer('start_skip');
            $options['start_skip'] = max(0, $startSkip);
        }

        if ($request->has('max_pages')) {
            $maxPages = $request->integer('max_pages');
            // 0 = illimité
            $options['max_pages'] = max(0, min(200, $maxPages));
        }

        if ($request->has('max_items')) {
            $maxItems = $request->integer('max_items');
            // 0 = illimité
            $options['max_items'] = max(0, min(20000, $maxItems));
        }

        return $options;
    }
}

