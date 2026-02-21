<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use Illuminate\Http\JsonResponse;

/**
 * Expose les configs de scrapping (sources + entités) pour l'UI.
 *
 * Utilise le Core ConfigLoader (resources/scrapping/config/) comme source unique.
 * Résolution des alias (ex. class → breed) via CollectAliasResolver.
 */
class ScrappingConfigController extends Controller
{
    public function __construct(
        private ConfigLoader $loader,
        private CollectAliasResolver $aliasResolver,
    ) {}

    public function index(): JsonResponse
    {
        $source = $this->loader->loadSource('dofusdb');
        $entities = $this->loader->listEntities('dofusdb');
        if (in_array('breed', $entities, true)) {
            $entities[] = 'class';
            sort($entities);
        }

        $entityConfigs = [];
        $itemConfig = null;
        foreach ($entities as $entity) {
            $aliasCfg = $this->aliasResolver->resolve($entity);
            $configEntity = ($aliasCfg !== null && isset($aliasCfg['entity'])) ? $aliasCfg['entity'] : $entity;
            $cfg = $this->loader->loadEntity('dofusdb', $configEntity);
            $entityConfigs[] = [
                'entity' => $entity,
                'label' => (($aliasCfg !== null ? ($aliasCfg['label'] ?? null) : null) ?? $cfg['label'] ?? ucfirst((string) $entity)),
                'meta' => $cfg['meta'] ?? new \stdClass(),
                'filters' => $cfg['filters'] ?? new \stdClass(),
                'relations' => array_keys((array) ($cfg['relations'] ?? [])),
                'comparisonKeys' => $this->extractComparisonKeys($cfg),
            ];
            if ($configEntity === 'item') {
                $itemConfig = $cfg;
            }
        }

        // Exposer resource, consumable, equipment (même config que item) pour le sélecteur d'entité UI
        if ($itemConfig !== null) {
            $itemLike = [
                'meta' => $itemConfig['meta'] ?? new \stdClass(),
                'filters' => $itemConfig['filters'] ?? new \stdClass(),
                'relations' => array_keys((array) ($itemConfig['relations'] ?? [])),
                'comparisonKeys' => $this->extractComparisonKeys($itemConfig),
            ];
            foreach (['resource' => 'Ressources', 'consumable' => 'Consommables', 'equipment' => 'Équipements'] as $alias => $label) {
                $entityConfigs[] = [
                    'entity' => $alias,
                    'label' => $label,
                    'meta' => $itemLike['meta'],
                    'filters' => $itemLike['filters'],
                    'relations' => $itemLike['relations'],
                    'comparisonKeys' => $itemLike['comparisonKeys'],
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'source' => [
                    'source' => $source['source'],
                    'label' => $source['label'] ?? $source['source'],
                    'baseUrl' => $source['baseUrl'] ?? null,
                ],
                'entities' => $entityConfigs,
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Extrait les clés du mapping (une seule source pour l'affichage comparaison).
     *
     * @param array<string, mixed> $cfg Config entité (mapping avec entrées "key")
     * @return list<string>
     */
    private function extractComparisonKeys(array $cfg): array
    {
        $mapping = $cfg['mapping'] ?? [];
        if (! is_array($mapping)) {
            return [];
        }
        $keys = [];
        foreach ($mapping as $entry) {
            if (! is_array($entry) || ! isset($entry['key']) || ! is_string($entry['key'])) {
                continue;
            }
            $keys[] = $entry['key'];
        }

        return $keys;
    }
}

