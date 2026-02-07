<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use Illuminate\Http\JsonResponse;

/**
 * Expose les configs de scrapping (sources + entités) pour l'UI.
 *
 * Utilise le Core ConfigLoader (resources/scrapping/config/) comme source unique.
 */
class ScrappingConfigController extends Controller
{
    private const ENTITY_ALIASES = ['class' => 'breed'];

    public function __construct(private ConfigLoader $loader) {}

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
            $configEntity = self::ENTITY_ALIASES[$entity] ?? $entity;
            $cfg = $this->loader->loadEntity('dofusdb', $configEntity);
            $entityConfigs[] = [
                'entity' => $entity,
                'label' => $cfg['label'] ?? ucfirst((string) $entity),
                'meta' => $cfg['meta'] ?? new \stdClass(),
                'filters' => $cfg['filters'] ?? new \stdClass(),
                'relations' => array_keys((array) ($cfg['relations'] ?? [])),
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
            ];
            foreach (['resource' => 'Ressources', 'consumable' => 'Consommables', 'equipment' => 'Équipements'] as $alias => $label) {
                $entityConfigs[] = [
                    'entity' => $alias,
                    'label' => $label,
                    'meta' => $itemLike['meta'],
                    'filters' => $itemLike['filters'],
                    'relations' => $itemLike['relations'],
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
}

