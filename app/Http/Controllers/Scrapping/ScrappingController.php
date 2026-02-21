<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Constants\EntityLimits;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use App\Services\Scrapping\Core\Orchestrator\OrchestratorResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Contrôleur principal pour le système de scrapping.
 *
 * Tous les imports passent par l'Orchestrator Core (collecte → conversion → validation → intégration).
 */
class ScrappingController extends Controller
{
    public function __construct(
        private ConfigLoader $configLoader,
        private CollectAliasResolver $aliasResolver,
        private IntegrationService $integrationService,
    ) {}

    /**
     * Récupère les métadonnées des types d'entités (limites, etc.) depuis le Core ConfigLoader.
     */
    public function meta(): JsonResponse
    {
        $metaByType = [];
        try {
            $entities = $this->configLoader->listEntities('dofusdb');
            if (in_array('breed', $entities, true)) {
                $entities[] = 'class';
                sort($entities);
            }
            foreach ($entities as $entity) {
                $aliasCfg = $this->aliasResolver->resolve($entity);
                $configEntity = ($aliasCfg !== null && isset($aliasCfg['entity'])) ? $aliasCfg['entity'] : $entity;
                $cfg = $this->configLoader->loadEntity('dofusdb', $configEntity);
                $maxId = (int) (($cfg['meta']['maxId'] ?? 0) ?: 0);
                if ($maxId > 0) {
                    $label = ($aliasCfg !== null ? ($aliasCfg['label'] ?? null) : null) ?? $cfg['label'] ?? $this->getEntityLabel($entity);
                    $metaByType[$entity] = [
                        'type' => $entity,
                        'maxId' => $maxId,
                        'label' => $label,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Impossible de charger les métadonnées depuis la config scrapping', ['error' => $e->getMessage()]);
        }
        foreach (EntityLimits::LIMITS as $type => $maxId) {
            if (!isset($metaByType[$type])) {
                $metaByType[$type] = [
                    'type' => $type,
                    'maxId' => $maxId,
                    'label' => $this->getEntityLabel($type),
                ];
            }
        }
        return response()->json([
            'success' => true,
            'data' => array_values($metaByType),
            'timestamp' => now()->toISOString(),
        ]);
    }

    private function getEntityLabel(string $type): string
    {
        return match ($type) {
            'class', 'breed' => 'Classe',
            'monster' => 'Monstre',
            'item' => 'Objet',
            'spell' => 'Sort',
            'panoply' => 'Panoplie',
            'resource' => 'Ressource',
            'consumable' => 'Consommable',
            'equipment' => 'Équipement',
            default => ucfirst($type),
        };
    }

    /**
     * Retourne la limite maxId pour un type d'entité : config (meta.maxId) en priorité, EntityLimits en secours.
     */
    private function getMaxIdForType(string $type): int
    {
        $resolved = $this->resolveEntityForImport($type);
        if ($resolved === null) {
            return EntityLimits::capFor($type);
        }
        try {
            $cfg = $this->configLoader->loadEntity($resolved['source'], $resolved['entity']);
            $maxId = (int) (($cfg['meta']['maxId'] ?? 0) ?: 0);
            if ($maxId > 0) {
                return $maxId;
            }
        } catch (\Throwable) {
            // fallback
        }
        return EntityLimits::capFor($type);
    }

    /** @return array{convert: bool, validate: bool, integrate: bool, dry_run: bool, force_update: bool, include_relations: bool, lang: string} */
    private function optionsFromRequest(Request $request): array
    {
        return [
            'convert' => true,
            'validate' => $request->boolean('validate', true),
            'integrate' => !$request->boolean('validate_only', false) && !$request->boolean('dry_run', false),
            'dry_run' => $request->boolean('dry_run', false),
            'force_update' => $request->boolean('force_update', false),
            'include_relations' => $request->boolean('include_relations', true),
            'lang' => (string) $request->input('lang', 'fr'),
        ];
    }

    private function resultToJson(OrchestratorResult $result, int $successStatus = 200): JsonResponse
    {
        if ($result->isSuccess()) {
            $data = $result->getIntegrationResult()?->getData() ?? $result->getConverted();
            return response()->json([
                'success' => true,
                'message' => $result->getMessage(),
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ], $successStatus);
        }
        return response()->json([
            'success' => false,
            'message' => $result->getMessage(),
            'error' => $result->getMessage(),
            'errors' => $result->getValidationErrors(),
            'timestamp' => now()->toISOString(),
        ], 400);
    }

    /** @return array{source: string, entity: string}|null */
    private function resolveEntityForImport(string $type): ?array
    {
        $cfg = $this->aliasResolver->resolve($type);
        if ($cfg !== null) {
            return ['source' => (string) ($cfg['source'] ?? 'dofusdb'), 'entity' => (string) ($cfg['entity'] ?? $type)];
        }
        return ['source' => 'dofusdb', 'entity' => $type];
    }

    /**
     * Type d'entité pour getExistingAttributesForComparison (monster, spell, breed, class, item, panoply).
     * @return string|null
     */
    private function entityTypeForComparison(string $normalizedType): ?string
    {
        return match ($normalizedType) {
            'class' => 'breed',
            'equipment', 'consumable', 'resource' => 'item',
            'monster', 'spell', 'panoply' => $normalizedType,
            default => null,
        };
    }

    /**
     * Import d'une classe depuis DofusDB (pipeline Core).
     */
    public function importClass(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->optionsFromRequest($request);
            $result = Orchestrator::default()->runOne('dofusdb', 'breed', $id, $options);
            return $this->resultToJson($result, 201);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'import de classe via API', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import de la classe',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import d'un monstre depuis DofusDB (pipeline Core).
     */
    public function importMonster(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->optionsFromRequest($request);
            $result = Orchestrator::default()->runOne('dofusdb', 'monster', $id, $options);
            return $this->resultToJson($result, 201);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'import de monstre via API', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import du monstre',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import d'un objet depuis DofusDB (pipeline Core).
     */
    public function importItem(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->optionsFromRequest($request);
            $result = Orchestrator::default()->runOne('dofusdb', 'item', $id, $options);
            return $this->resultToJson($result, 201);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'import d\'objet via API', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import de l\'objet',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import d'une ressource (item DofusDB, pipeline Core).
     */
    public function importResource(Request $request, int $id): JsonResponse
    {
        $resolved = $this->resolveEntityForImport('resource');
        if ($resolved === null) {
            return response()->json(['success' => false, 'message' => 'Entité resource non supportée.', 'timestamp' => now()->toISOString()], 422);
        }
        try {
            $options = $this->optionsFromRequest($request);
            $result = Orchestrator::default()->runOne($resolved['source'], $resolved['entity'], $id, $options);
            return $this->resultToJson($result, 201);
        } catch (\Throwable $e) {
            Log::error('Erreur import ressource via API', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import de la ressource', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }

    /**
     * Import d'un consommable (item DofusDB, pipeline Core).
     */
    public function importConsumable(Request $request, int $id): JsonResponse
    {
        $resolved = $this->resolveEntityForImport('consumable');
        if ($resolved === null) {
            return response()->json(['success' => false, 'message' => 'Entité consumable non supportée.', 'timestamp' => now()->toISOString()], 422);
        }
        try {
            $options = $this->optionsFromRequest($request);
            $result = Orchestrator::default()->runOne($resolved['source'], $resolved['entity'], $id, $options);
            return $this->resultToJson($result, 201);
        } catch (\Throwable $e) {
            Log::error('Erreur import consommable via API', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import du consommable', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }

    /**
     * Import d'un sort depuis DofusDB (pipeline Core).
     */
    public function importSpell(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->optionsFromRequest($request);
            $result = Orchestrator::default()->runOne('dofusdb', 'spell', $id, $options);
            return $this->resultToJson($result, 201);
        } catch (\Throwable $e) {
            Log::error('Erreur import sort via API', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import du sort', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }

    /**
     * Import d'une panoplie depuis DofusDB (pipeline Core, si l'entité est configurée).
     */
    public function importPanoply(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->optionsFromRequest($request);
            $result = Orchestrator::default()->runOne('dofusdb', 'panoply', $id, $options);
            return $this->resultToJson($result, 201);
        } catch (\Throwable $e) {
            Log::error('Erreur import panoplie via API', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import de la panoplie', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }

    /**
     * Import en lot : boucle runOne pour chaque entité (pipeline Core).
     */
    public function importBatch(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'entities' => ['required', 'array', 'min:1'],
                'entities.*.type' => ['required', 'string', 'in:class,monster,item,spell,panoply,resource,consumable,equipment'],
                'entities.*.id' => ['required', 'integer', 'min:1'],
            ]);
            $options = $this->optionsFromRequest($request);
            $entities = $request->input('entities');
            $orchestrator = Orchestrator::default();
            $results = [];
            $successCount = 0;
            $errorCount = 0;
            foreach ($entities as $item) {
                $type = (string) ($item['type'] ?? '');
                $id = (int) ($item['id'] ?? 0);
                $resolved = $this->resolveEntityForImport($type);
                if ($resolved === null || $id <= 0) {
                    $results[] = ['type' => $type, 'id' => $id, 'success' => false, 'error' => 'Entité ou ID invalide'];
                    $errorCount++;
                    continue;
                }
                try {
                    $result = $orchestrator->runOne($resolved['source'], $resolved['entity'], $id, $options);
                    $results[] = [
                        'type' => $type,
                        'id' => $id,
                        'success' => $result->isSuccess(),
                        'data' => $result->isSuccess() ? ($result->getIntegrationResult()?->getData() ?? $result->getConverted()) : null,
                        'error' => $result->isSuccess() ? null : $result->getMessage(),
                        'validation_errors' => $result->isSuccess() ? [] : $result->getValidationErrors(),
                    ];
                    $result->isSuccess() ? $successCount++ : $errorCount++;
                } catch (\Throwable $e) {
                    $results[] = ['type' => $type, 'id' => $id, 'success' => false, 'error' => $e->getMessage()];
                    $errorCount++;
                }
            }
            $statusCode = $errorCount === 0 ? 201 : 207;
            return response()->json([
                'success' => $errorCount === 0,
                'message' => $errorCount === 0 ? 'Tous les imports ont réussi' : 'Certains imports ont échoué',
                'summary' => ['total' => count($entities), 'success' => $successCount, 'errors' => $errorCount],
                'results' => $results,
                'timestamp' => now()->toISOString(),
            ], $statusCode);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Erreur de validation', 'errors' => $e->errors(), 'timestamp' => now()->toISOString()], 422);
        } catch (\Throwable $e) {
            Log::error('Erreur import en lot via API', ['count' => count($entities ?? []), 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import en lot', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }

    /**
     * Import avec fusion : import avec force_update (choix par propriété non implémenté dans le pipeline Core).
     */
    public function importWithMerge(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => ['required', 'string', 'in:class,monster,item,spell,panoply,resource,consumable,equipment'],
                'dofusdb_id' => ['required', 'integer', 'min:1'],
                'choices' => ['nullable', 'array'],
                'choices.*' => ['string', 'in:krosmoz,dofusdb'],
            ]);
            $type = (string) $validated['type'];
            $dofusdbId = (int) $validated['dofusdb_id'];
            $resolved = $this->resolveEntityForImport($type);
            if ($resolved === null) {
                return response()->json(['success' => false, 'message' => 'Type non supporté.', 'timestamp' => now()->toISOString()], 422);
            }
            $options = $this->optionsFromRequest($request);
            $options['force_update'] = true;
            $result = Orchestrator::default()->runOne($resolved['source'], $resolved['entity'], $dofusdbId, $options);
            return $this->resultToJson($result, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Erreur de validation', 'errors' => $e->errors(), 'timestamp' => now()->toISOString()], 422);
        } catch (\Throwable $e) {
            Log::error('Erreur import avec fusion', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import avec fusion', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }

    /**
     * Import d'une plage d'ID (boucle runOne, pipeline Core).
     */
    public function importRange(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(EntityLimits::LIMITS))],
            'start_id' => ['required', 'integer', 'min:1'],
            'end_id' => ['required', 'integer', 'min:1'],
        ]);
        $type = (string) $validated['type'];
        $startId = (int) $validated['start_id'];
        $endId = (int) $validated['end_id'];
        if ($startId > $endId) {
            return response()->json(['success' => false, 'message' => 'La valeur de début doit être inférieure ou égale à la valeur de fin'], 422);
        }
        $maxId = $this->getMaxIdForType($type);
        if ($startId < 1 || $endId > $maxId) {
            return response()->json(['success' => false, 'message' => "La plage doit être comprise entre 1 et {$maxId}"], 422);
        }
        $resolved = $this->resolveEntityForImport($type);
        if ($resolved === null) {
            return response()->json(['success' => false, 'message' => 'Type non supporté.'], 422);
        }
        try {
            $options = $this->optionsFromRequest($request);
            $options['limit'] = $endId - $startId + 1;
            $options['offset'] = 0;
            $filters = ['idMin' => $startId, 'idMax' => $endId];
            $orchestrator = Orchestrator::default();
            $result = $orchestrator->runMany($resolved['source'], $resolved['entity'], $filters, $options);

            if (!$result->isSuccess()) {
                return response()->json([
                    'success' => false,
                    'message' => $result->getMessage(),
                    'errors' => $result->getValidationErrors(),
                    'range' => ['type' => $type, 'start' => $startId, 'end' => $endId],
                    'timestamp' => now()->toISOString(),
                ], 400);
            }

            $convertedList = $result->getConverted();
            $integrationResults = $result->getIntegrationResults() ?? [];
            $results = [];
            $successCount = 0;
            $errorCount = 0;
            foreach ($convertedList as $i => $converted) {
                if (!is_array($converted)) {
                    continue;
                }
                $id = $this->extractDofusdbIdFromConverted($converted);
                $intResult = $integrationResults[$i] ?? null;
                $success = $intResult !== null && $intResult->isSuccess();
                $results[] = [
                    'id' => $id ?? $startId + $i,
                    'success' => $success,
                    'error' => $success ? null : ($intResult?->getMessage() ?? 'Erreur inconnue'),
                ];
                $success ? $successCount++ : $errorCount++;
            }
            $statusCode = $errorCount === 0 ? 201 : 207;
            return response()->json([
                'success' => $errorCount === 0,
                'message' => $errorCount === 0 ? 'Import de plage terminé' : 'Import de plage avec erreurs',
                'summary' => ['total' => count($results), 'success' => $successCount, 'errors' => $errorCount],
                'results' => $results,
                'range' => ['type' => $type, 'start' => $startId, 'end' => $endId],
                'timestamp' => now()->toISOString(),
            ], $statusCode);
        } catch (\Throwable $e) {
            Log::error('Erreur import de plage', ['type' => $type, 'start_id' => $startId, 'end_id' => $endId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import de la plage', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }

    /**
     * Extrait l'ID DofusDB d'une structure convertie (runMany) pour le rapport par item.
     */
    private function extractDofusdbIdFromConverted(array $converted): ?int
    {
        $id = $converted['monsters']['dofusdb_id'] ?? $converted['breeds']['dofusdb_id'] ?? $converted['items']['dofusdb_id']
            ?? $converted['spells']['dofusdb_id'] ?? $converted['panoplies']['dofusdb_id'] ?? null;
        if ($id !== null && (is_int($id) || (is_string($id) && ctype_digit($id)))) {
            return (int) $id;
        }
        return null;
    }

    /**
     * Import complet d'un type d'entité (pipeline Core).
     */
    public function importAll(Request $request): JsonResponse
    {
        $validated = $request->validate(['type' => ['required', 'string', Rule::in(array_keys(EntityLimits::LIMITS))]]);
        $type = (string) $validated['type'];
        $maxId = $this->getMaxIdForType($type);
        $request->merge(['start_id' => 1, 'end_id' => $maxId]);
        return $this->importRange($request);
    }

    /**
     * Prévisualisation d'une entité avant import (runOne avec dry_run, pipeline Core).
     */
    public function preview(string $type, int $id): JsonResponse
    {
        $normalizedType = strtolower($type);
        if (!array_key_exists($normalizedType, EntityLimits::LIMITS)) {
            return response()->json(['success' => false, 'message' => 'Type d\'entité non supporté'], 422);
        }
        $maxId = $this->getMaxIdForType($normalizedType);
        if ($id < 1 || $id > $maxId) {
            return response()->json(['success' => false, 'message' => "L'identifiant doit être compris entre 1 et {$maxId}"], 422);
        }
        $resolved = $this->resolveEntityForImport($normalizedType);
        if ($resolved === null) {
            return response()->json(['success' => false, 'message' => 'Type non supporté.'], 422);
        }
        try {
            $options = ['convert' => true, 'validate' => true, 'integrate' => false, 'dry_run' => true, 'force_update' => false, 'lang' => 'fr'];
            $result = Orchestrator::default()->runOne($resolved['source'], $resolved['entity'], $id, $options);
            $converted = $result->getConverted();
            $comparisonType = $this->entityTypeForComparison($normalizedType);
            $existingRecord = $comparisonType !== null
                ? $this->integrationService->getExistingAttributesForComparison($comparisonType, $converted)
                : null;
            $data = [
                'success' => $result->isSuccess(),
                'raw' => $result->getRaw(),
                'converted' => $converted,
                'validation_errors' => $result->getValidationErrors(),
                'existing' => $existingRecord !== null ? ['record' => $existingRecord] : null,
            ];
            return response()->json(['success' => true, 'data' => $data, 'timestamp' => now()->toISOString()]);
        } catch (\Throwable $e) {
            Log::error('Erreur prévisualisation', ['type' => $type, 'id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de la prévisualisation', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }

    /**
     * Prévisualisation en lot : pour chaque ID, runOne en dry_run et retourne les données converties.
     * Limite : 100 IDs par requête. Utilisé par l'UI pour afficher valeur convertie + brute par ligne.
     */
    public function previewBatch(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => ['required', 'string', 'in:class,monster,item,spell,panoply,resource,consumable,equipment'],
                'ids' => ['required', 'array', 'min:1', 'max:100'],
                'ids.*' => ['integer', 'min:1'],
            ]);
            $type = (string) $validated['type'];
            $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
            $resolved = $this->resolveEntityForImport($type);
            if ($resolved === null) {
                return response()->json(['success' => false, 'message' => 'Type non supporté.', 'timestamp' => now()->toISOString()], 422);
            }
            $maxId = $this->getMaxIdForType($type);
            $comparisonType = $this->entityTypeForComparison($type);
            $options = ['convert' => true, 'validate' => true, 'integrate' => false, 'dry_run' => true, 'force_update' => false, 'lang' => 'fr'];
            $orchestrator = Orchestrator::default();
            $items = [];
            foreach ($ids as $id) {
                if ($id < 1 || $id > $maxId) {
                    $items[] = ['id' => $id, 'converted' => null, 'existing' => null, 'error' => "ID hors plage 1-{$maxId}"];
                    continue;
                }
                try {
                    $result = $orchestrator->runOne($resolved['source'], $resolved['entity'], $id, $options);
                    $converted = $result->getConverted();
                    $existingRecord = $comparisonType !== null
                        ? $this->integrationService->getExistingAttributesForComparison($comparisonType, $converted)
                        : null;
                    $items[] = [
                        'id' => $id,
                        'raw' => $result->getRaw(),
                        'converted' => $converted,
                        'existing' => $existingRecord !== null ? ['record' => $existingRecord] : null,
                        'error' => $result->isSuccess() ? null : $result->getMessage(),
                    ];
                } catch (\Throwable $e) {
                    $items[] = ['id' => $id, 'converted' => null, 'existing' => null, 'error' => $e->getMessage()];
                }
            }
            return response()->json([
                'success' => true,
                'data' => ['items' => $items],
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Erreur de validation', 'errors' => $e->errors(), 'timestamp' => now()->toISOString()], 422);
        } catch (\Throwable $e) {
            Log::error('Erreur prévisualisation batch', ['type' => $type ?? null, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de la prévisualisation en lot', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }
}

