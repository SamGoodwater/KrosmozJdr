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
                'mappingDiagnostics' => $this->buildMappingDiagnostics($cfg, $source['source'], $configEntity),
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
                    'mappingDiagnostics' => $this->buildMappingDiagnostics($itemConfig, $source['source'], 'item'),
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

    /**
     * Calcule un diagnostic de couverture du mapping pour l'UI admin.
     *
     * @param array<string, mixed> $cfg
     * @return array{
     *   total: int,
     *   valid: int,
     *   invalid: int,
     *   blocking: int,
     *   improvable: int,
     *   coveragePct: int,
     *   sourcePath: int,
     *   sourceExtract: int,
     *   withTargets: int,
     *   withFormatters: int,
     *   warnings: list<array{
     *     code: string,
     *     severity: 'blocking'|'improvable',
     *     message: string,
     *     mappingKey: string,
     *     actionUrl: string
     *   }>
     * }
     */
    private function buildMappingDiagnostics(array $cfg, string $source, string $entity): array
    {
        $mapping = $cfg['mapping'] ?? [];
        if (! is_array($mapping) || $mapping === []) {
            return [
                'total' => 0,
                'valid' => 0,
                'invalid' => 0,
                'blocking' => 1,
                'improvable' => 0,
                'coveragePct' => 0,
                'sourcePath' => 0,
                'sourceExtract' => 0,
                'withTargets' => 0,
                'withFormatters' => 0,
                'warnings' => [[
                    'code' => 'mapping.empty',
                    'severity' => 'blocking',
                    'message' => 'Aucune entrée de mapping trouvée.',
                    'mappingKey' => '',
                    'actionUrl' => route('admin.scrapping-mappings.index', ['source' => $source, 'entity' => $entity]),
                ]],
            ];
        }

        $total = 0;
        $valid = 0;
        $blocking = 0;
        $improvable = 0;
        $sourcePath = 0;
        $sourceExtract = 0;
        $withTargets = 0;
        $withFormatters = 0;
        $warnings = [];

        foreach ($mapping as $index => $entry) {
            if (! is_array($entry)) {
                $warnings[] = [
                    'code' => 'mapping.invalid_entry',
                    'severity' => 'blocking',
                    'message' => "Entrée #{$index} invalide (format non tableau).",
                    'mappingKey' => "#{$index}",
                    'actionUrl' => route('admin.scrapping-mappings.index', ['source' => $source, 'entity' => $entity]),
                ];
                $blocking++;
                continue;
            }

            $total++;
            $key = isset($entry['key']) && is_string($entry['key']) ? trim($entry['key']) : '';
            $from = $entry['from'] ?? null;
            $targets = $entry['to'] ?? [];
            $formatters = $entry['formatters'] ?? [];

            $hasPath = is_array($from) && isset($from['path']) && is_string($from['path']) && trim($from['path']) !== '';
            $hasExtract = is_array($from) && array_key_exists('extract', $from);
            if ($hasPath) {
                $sourcePath++;
            }
            if ($hasExtract) {
                $sourceExtract++;
            }

            $validTargets = 0;
            if (is_array($targets)) {
                foreach ($targets as $target) {
                    if (is_array($target)
                        && isset($target['model'], $target['field'])
                        && is_string($target['model'])
                        && is_string($target['field'])
                        && trim($target['model']) !== ''
                        && trim($target['field']) !== '') {
                        $validTargets++;
                    }
                }
            }
            if ($validTargets > 0) {
                $withTargets++;
            }

            $hasFormatters = is_array($formatters) && $formatters !== [];
            if ($hasFormatters) {
                $withFormatters++;
            }

            $isValid = $key !== '' && ($hasPath || $hasExtract) && $validTargets > 0;
            if ($isValid) {
                $valid++;
                if (! $hasFormatters) {
                    $improvable++;
                    $warnings[] = [
                        'code' => 'mapping.no_formatter',
                        'severity' => 'improvable',
                        'message' => "Règle {$key} valide sans formatter explicite.",
                        'mappingKey' => $key,
                        'actionUrl' => route('admin.scrapping-mappings.index', [
                            'source' => $source,
                            'entity' => $entity,
                            'mapping_key' => $key,
                        ]),
                    ];
                }
                continue;
            }

            $parts = [];
            if ($key === '') {
                $parts[] = 'clé absente';
            }
            if (! $hasPath && ! $hasExtract) {
                $parts[] = 'source absente';
            }
            if ($validTargets === 0) {
                $parts[] = 'cible absente';
            }
            $label = $key !== '' ? $key : "#{$index}";
            $warnings[] = [
                'code' => 'mapping.incomplete',
                'severity' => 'blocking',
                'message' => "Règle {$label} incomplète (" . implode(', ', $parts) . ').',
                'mappingKey' => $label,
                'actionUrl' => route('admin.scrapping-mappings.index', [
                    'source' => $source,
                    'entity' => $entity,
                    'mapping_key' => $key !== '' ? $key : null,
                ]),
            ];
            $blocking++;
        }

        $invalid = max(0, $total - $valid);

        return [
            'total' => $total,
            'valid' => $valid,
            'invalid' => $invalid,
            'blocking' => $blocking,
            'improvable' => $improvable,
            'coveragePct' => $total > 0 ? (int) round(($valid / $total) * 100) : 0,
            'sourcePath' => $sourcePath,
            'sourceExtract' => $sourceExtract,
            'withTargets' => $withTargets,
            'withFormatters' => $withFormatters,
            'warnings' => array_slice($warnings, 0, 8),
        ];
    }
}

