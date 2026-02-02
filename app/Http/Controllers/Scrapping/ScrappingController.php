<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Constants\EntityLimits;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Config\ConfigLoader;
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
    private const ENTITY_ALIASES = ['class' => 'breed'];

    public function __construct(
        private ConfigLoader $configLoader,
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
                $configEntity = self::ENTITY_ALIASES[$entity] ?? $entity;
                $cfg = $this->configLoader->loadEntity('dofusdb', $configEntity);
                $maxId = (int) (($cfg['meta']['maxId'] ?? 0) ?: 0);
                if ($maxId > 0) {
                    $metaByType[$entity] = [
                        'type' => $entity,
                        'maxId' => $maxId,
                        'label' => $cfg['label'] ?? $this->getEntityLabel($entity),
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

    /** @return array{convert: bool, validate: bool, integrate: bool, dry_run: bool, force_update: bool, lang: string} */
    private function optionsFromRequest(Request $request): array
    {
        return [
            'convert' => true,
            'validate' => $request->boolean('validate', true),
            'integrate' => !$request->boolean('validate_only', false) && !$request->boolean('dry_run', false),
            'dry_run' => $request->boolean('dry_run', false),
            'force_update' => $request->boolean('force_update', false),
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
        $aliasResolver = CollectAliasResolver::default();
        $cfg = $aliasResolver->resolve($type);
        if ($cfg !== null) {
            return ['source' => (string) ($cfg['source'] ?? 'dofusdb'), 'entity' => (string) ($cfg['entity'] ?? $type)];
        }
        $entity = $type === 'class' ? 'breed' : $type;
        return ['source' => 'dofusdb', 'entity' => $entity];
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
            Log::error('Erreur import en lot via API', ['error' => $e->getMessage()]);
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
        $maxId = EntityLimits::LIMITS[$type] ?? 10000;
        if ($startId < 1 || $endId > $maxId) {
            return response()->json(['success' => false, 'message' => "La plage doit être comprise entre 1 et {$maxId}"], 422);
        }
        $resolved = $this->resolveEntityForImport($type);
        if ($resolved === null) {
            return response()->json(['success' => false, 'message' => 'Type non supporté.'], 422);
        }
        try {
            $options = $this->optionsFromRequest($request);
            $orchestrator = Orchestrator::default();
            $results = [];
            $successCount = 0;
            $errorCount = 0;
            for ($id = $startId; $id <= $endId; $id++) {
                try {
                    $result = $orchestrator->runOne($resolved['source'], $resolved['entity'], $id, $options);
                    $results[] = ['id' => $id, 'success' => $result->isSuccess(), 'error' => $result->isSuccess() ? null : $result->getMessage()];
                    $result->isSuccess() ? $successCount++ : $errorCount++;
                } catch (\Throwable $e) {
                    $results[] = ['id' => $id, 'success' => false, 'error' => $e->getMessage()];
                    $errorCount++;
                }
            }
            $statusCode = $errorCount === 0 ? 201 : 207;
            return response()->json([
                'success' => $errorCount === 0,
                'message' => $errorCount === 0 ? 'Import de plage terminé' : 'Import de plage avec erreurs',
                'summary' => ['total' => $endId - $startId + 1, 'success' => $successCount, 'errors' => $errorCount],
                'results' => $results,
                'range' => ['type' => $type, 'start' => $startId, 'end' => $endId],
                'timestamp' => now()->toISOString(),
            ], $statusCode);
        } catch (\Throwable $e) {
            Log::error('Erreur import de plage', ['type' => $type, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import de la plage', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }

    /**
     * Import complet d'un type d'entité (pipeline Core).
     */
    public function importAll(Request $request): JsonResponse
    {
        $validated = $request->validate(['type' => ['required', 'string', Rule::in(array_keys(EntityLimits::LIMITS))]]);
        $type = (string) $validated['type'];
        $maxId = EntityLimits::LIMITS[$type] ?? 10000;
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
        $maxId = EntityLimits::LIMITS[$normalizedType];
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
            $data = [
                'success' => $result->isSuccess(),
                'raw' => null,
                'converted' => $result->getConverted(),
                'validation_errors' => $result->getValidationErrors(),
            ];
            return response()->json(['success' => true, 'data' => $data, 'timestamp' => now()->toISOString()]);
        } catch (\Throwable $e) {
            Log::error('Erreur prévisualisation', ['type' => $type, 'id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de la prévisualisation', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString()], 500);
        }
    }
}

