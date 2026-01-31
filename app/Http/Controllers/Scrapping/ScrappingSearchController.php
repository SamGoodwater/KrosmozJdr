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
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
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
        $filters = $this->applyEntityDefaults($entity, $filters);
        $options = $this->extractOptions($request);

        $result = $this->collector->fetchManyResult($entity, $filters, $options);
        $items = $this->withExistsFlag($entity, $result['items']);
        $items = $this->withTypeLabelsAndRegistry($entity, $items);

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

        if ($request->has('typeIds')) {
            $typeIds = $request->query('typeIds');
            if (is_string($typeIds)) {
                $parts = array_filter(array_map('trim', explode(',', $typeIds)));
                $filters['typeIds'] = $parts;
            } elseif (is_array($typeIds)) {
                $filters['typeIds'] = $typeIds;
            }
        }

        if ($request->has('typeIdsNot')) {
            $typeIdsNot = $request->query('typeIdsNot');
            if (is_string($typeIdsNot)) {
                $parts = array_filter(array_map('trim', explode(',', $typeIdsNot)));
                $filters['typeIdsNot'] = $parts;
            } elseif (is_array($typeIdsNot)) {
                $filters['typeIdsNot'] = $typeIdsNot;
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
            'equipment' => Item::class,
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
     * Ajoute des informations de type (nom) + enregistre le typeId en base si absent.
     *
     * @param string $entity
     * @param array<int, array<string,mixed>> $items
     * @return array<int, array<string,mixed>>
     */
    private function withTypeLabelsAndRegistry(string $entity, array $items): array
    {
        $entity = strtolower($entity);
        $registry = match ($entity) {
            'resource' => ResourceType::class,
            'consumable' => ConsumableType::class,
            'item', 'equipment' => ItemType::class,
            default => null,
        };

        if (!$registry) {
            return $items;
        }

        // 1) Extraire les typeId uniques
        $typeIds = [];
        foreach ($items as $it) {
            if (!is_array($it)) continue;
            $typeId = $it['typeId'] ?? null;
            if (is_int($typeId) || (is_string($typeId) && ctype_digit($typeId))) {
                $n = (int) $typeId;
                if ($n > 0) {
                    $typeIds[] = $n;
                }
            }
        }
        $typeIds = array_values(array_unique($typeIds));
        if (empty($typeIds)) {
            return $items;
        }

        // 2) Charger en bulk les types connus
        $byTypeId = [];
        try {
            /** @var class-string $registry */
            $rows = $registry::query()->whereIn('dofusdb_type_id', $typeIds)->get();
            foreach ($rows as $row) {
                $id = is_numeric($row->dofusdb_type_id ?? null) ? (int) $row->dofusdb_type_id : 0;
                if ($id > 0) {
                    $byTypeId[$id] = $row;
                }
            }
        } catch (\Throwable) {
            $byTypeId = [];
        }

        // 3) Enrichir items + auto-touch des types absents
        foreach ($items as $i => $it) {
            if (!is_array($it)) continue;

            $typeId = $it['typeId'] ?? null;
            if (!(is_int($typeId) || (is_string($typeId) && ctype_digit($typeId)))) {
                continue;
            }
            $typeId = (int) $typeId;
            if ($typeId <= 0) continue;

            try {
                $typeModel = $byTypeId[$typeId] ?? null;
                if (!$typeModel) {
                    /** @var class-string $registry */
                    $typeModel = $registry::touchDofusdbType($typeId);
                    $byTypeId[$typeId] = $typeModel;
                }

                $items[$i]['typeName'] = is_string($typeModel->name ?? null) ? (string) $typeModel->name : null;
                $items[$i]['typeDecision'] = is_string($typeModel->decision ?? null) ? (string) $typeModel->decision : null;
                $items[$i]['typeKnown'] = ($items[$i]['typeDecision'] ?? null) === 'allowed';
            } catch (\Throwable) {
                $items[$i]['typeName'] = null;
                $items[$i]['typeDecision'] = null;
                $items[$i]['typeKnown'] = null;
            }
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

    /**
     * Injecte des filtres implicites pour certaines entités "alias" de /items.
     *
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    private function applyEntityDefaults(string $entity, array $filters): array
    {
        $entity = strtolower($entity);

        $hasExplicitType = array_key_exists('typeId', $filters) || array_key_exists('typeIds', $filters) || array_key_exists('typeIdsNot', $filters);
        if ($entity === 'resource') {
            // Par défaut: ne retourner que les items dont le typeId est autorisé comme ressource.
            if (!$hasExplicitType) {
                try {
                    $allowed = ResourceType::query()->allowed()->pluck('dofusdb_type_id')->all();
                } catch (\Throwable) {
                    $allowed = [];
                }
                if (!empty($allowed)) {
                    $filters['typeIds'] = $allowed;
                }
            }
        }

        if ($entity === 'consumable') {
            // Par défaut: typeIds consumables basés sur la config d'intégration (target_table=consumables).
            if (!$hasExplicitType) {
                $ids = $this->getConsumableDofusdbTypeIdsFromIntegrationConfig();
                if (!empty($ids)) {
                    $filters['typeIds'] = $ids;
                }
            }
        }

        if ($entity === 'equipment') {
            // Par défaut: on exclut ressources + consumables pour éviter les mélanges.
            if (!$hasExplicitType) {
                $exclude = [];

                try {
                    $exclude = array_merge($exclude, ResourceType::query()->allowed()->pluck('dofusdb_type_id')->all());
                } catch (\Throwable) {
                    // ignore
                }

                $exclude = array_merge($exclude, $this->getConsumableDofusdbTypeIdsFromIntegrationConfig());
                $exclude = array_values(array_unique(array_map('intval', $exclude)));

                if (!empty($exclude)) {
                    $filters['typeIdsNot'] = $exclude;
                }
            }
        }

        return $filters;
    }

    /**
     * @return array<int,int>
     */
    private function getConsumableDofusdbTypeIdsFromIntegrationConfig(): array
    {
        try {
            /** @var array<string,mixed> $cfg */
            $cfg = require app_path('Services/Scrapping/DataIntegration/config.php');
        } catch (\Throwable) {
            return [];
        }

        $mapping = $cfg['dofusdb_mapping']['items_type_mapping'] ?? null;
        if (!is_array($mapping)) {
            return [];
        }

        $ids = [];
        foreach ($mapping as $key => $entry) {
            if (!is_array($entry)) continue;
            if (($entry['target_table'] ?? null) !== 'consumables') continue;
            $id = $entry['dofusdb_type_id'] ?? null;
            if (is_int($id) || (is_string($id) && ctype_digit($id))) {
                $ids[] = (int) $id;
            }
        }

        return array_values(array_unique($ids));
    }
}

