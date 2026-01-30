<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Config\ScrappingConfigLoader;
use Illuminate\Http\JsonResponse;

/**
 * Expose les configs de scrapping (sources + entités) pour l'UI.
 *
 * @description
 * Fournit un endpoint JSON consumable par la Section Scrapping (page/modal),
 * sans créer un second système : c'est une couche "meta" au-dessus de la refonte.
 */
class ScrappingConfigController extends Controller
{
    public function __construct(private ScrappingConfigLoader $loader) {}

    public function index(): JsonResponse
    {
        // Pour l'instant on expose uniquement dofusdb (refonte progressive).
        $source = $this->loader->loadSource('dofusdb');
        $entities = $this->loader->listEntities('dofusdb');

        $entityConfigs = [];
        foreach ($entities as $entity) {
            $cfg = $this->loader->loadEntity('dofusdb', $entity);
            $entityConfigs[] = [
                'entity' => $cfg['entity'],
                'label' => $cfg['label'] ?? ucfirst((string) $cfg['entity']),
                'meta' => $cfg['meta'] ?? new \stdClass(),
                'filters' => $cfg['filters'] ?? new \stdClass(),
                'relations' => array_keys((array) ($cfg['relations'] ?? [])),
            ];
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

