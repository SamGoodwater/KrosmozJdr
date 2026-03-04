<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Scrapping\Concerns\RespondsWithOrchestratorResult;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Config\EntityMetaService;
use App\Services\Scrapping\Core\Conversion\UnknownCharacteristicRunTracker;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use App\Services\Scrapping\Core\Orchestrator\OrchestratorResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

/**
 * Contrôleur principal pour le système de scrapping.
 *
 * Tous les imports passent par l'Orchestrator Core (collecte → conversion → validation → intégration).
 */
class ScrappingController extends Controller
{
    use RespondsWithOrchestratorResult;

    public function __construct(
        private ConfigLoader $configLoader,
        private CollectAliasResolver $aliasResolver,
        private EntityMetaService $entityMeta,
        private IntegrationService $integrationService,
        private Orchestrator $orchestrator,
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
        foreach ($this->entityMeta->allowedTypes() as $type) {
            if (!isset($metaByType[$type])) {
                $metaByType[$type] = [
                    'type' => $type,
                    'maxId' => $this->entityMeta->getMaxIdForType($type),
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

    /**
     * Retourne le mapping id caractéristique DofusDB → libellé (keyword) pour l'affichage des effets bruts.
     * Source : dofusdb_characteristic_to_krosmoz.json (keywords_by_id).
     */
    public function dofusdbCharacteristicLabels(): JsonResponse
    {
        $path = resource_path('scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json');
        if (! is_file($path)) {
            return response()->json(['success' => true, 'data' => []]);
        }
        try {
            $config = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return response()->json(['success' => true, 'data' => []]);
        }
        $keywordsById = $config['keywords_by_id'] ?? [];
        $labels = is_array($keywordsById) ? $keywordsById : [];

        return response()->json([
            'success' => true,
            'data' => $labels,
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
     * Retourne la limite maxId pour un type d'entité (config meta.maxId en priorité, fallback sinon).
     */
    private function getMaxIdForType(string $type): int
    {
        return $this->entityMeta->getMaxIdForType($type);
    }

    /** @return array{convert: bool, validate: bool, integrate: bool, dry_run: bool, force_update: bool, replace_mode?: string, include_relations: bool, exclude_from_update: list<string>, property_whitelist: list<string>, download_images: bool, lang: string, run_id: string} */
    private function optionsFromRequest(Request $request, ?string $runId = null): array
    {
        $effectiveRunId = $runId ?? $this->runIdFromRequest($request);
        UnknownCharacteristicRunTracker::reset($effectiveRunId);
        $replaceMode = $request->input('replace_mode');
        $replaceMode = is_string($replaceMode) && in_array($replaceMode, ['never', 'draft_raw_only', 'always'], true) ? $replaceMode : null;

        $excludeFromUpdate = $request->input('exclude_from_update');
        if (! is_array($excludeFromUpdate)) {
            $excludeFromUpdate = [];
        }
        $excludeFromUpdate = array_values(array_filter(array_map('strval', $excludeFromUpdate)));

        $withImages = $request->boolean('with_images', true);
        $downloadImages = $request->boolean('download_images', $withImages);
        if (! $withImages) {
            $excludeFromUpdate = array_unique(array_merge($excludeFromUpdate, ['image']));
        }

        $propertyWhitelist = $request->input('property_whitelist');
        if (is_array($propertyWhitelist)) {
            $propertyWhitelist = array_values(array_filter(array_map('strval', $propertyWhitelist)));
        } else {
            $propertyWhitelist = is_string($propertyWhitelist)
                ? array_values(array_filter(array_map('trim', explode(',', $propertyWhitelist))))
                : [];
        }

        $forceUpdate = $request->boolean('force_update', false);
        if ($replaceMode === 'always') {
            $forceUpdate = true;
        } elseif ($replaceMode === 'never') {
            $forceUpdate = false;
        }

        return [
            'convert' => true,
            'validate' => $request->boolean('validate', true),
            'integrate' => ! $request->boolean('validate_only', false) && ! $request->boolean('dry_run', false),
            'dry_run' => $request->boolean('dry_run', false),
            'force_update' => $forceUpdate,
            'replace_mode' => $replaceMode,
            'include_relations' => $request->boolean('include_relations', true),
            'exclude_from_update' => $excludeFromUpdate,
            'property_whitelist' => $propertyWhitelist,
            'download_images' => $downloadImages,
            'lang' => (string) $request->input('lang', 'fr'),
            'run_id' => $effectiveRunId,
        ];
    }

    private function runIdFromRequest(Request $request): string
    {
        $headerRunId = trim((string) $request->header('X-Scrapping-Run-Id', ''));
        if ($headerRunId !== '') {
            return $headerRunId;
        }

        return (string) Str::uuid();
    }

    /**
     * @return array{unknown_characteristics: array<string, mixed>|null}
     */
    private function debugPayload(string $runId): array
    {
        return [
            'unknown_characteristics' => UnknownCharacteristicRunTracker::summary($runId),
        ];
    }

    /**
     * @param array<string, mixed> $context
     */
    private function logScrapping(string $level, string $message, array $context = []): void
    {
        $logger = Log::channel('scrapping');
        if ($level === 'warning') {
            $logger->warning($message, $context);
            return;
        }
        if ($level === 'error') {
            $logger->error($message, $context);
            return;
        }
        if ($level === 'debug') {
            $logger->debug($message, $context);
            return;
        }
        $logger->info($message, $context);
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
        $runId = $this->runIdFromRequest($request);
        try {
            $options = $this->optionsFromRequest($request, $runId);
            $this->logScrapping('info', 'scrapping.import.start', ['run_id' => $runId, 'type' => 'class', 'id' => $id]);
            $result = $this->orchestrator->runOne('dofusdb', 'breed', $id, $options);
            $this->logScrapping('info', 'scrapping.import.done', ['run_id' => $runId, 'type' => 'class', 'id' => $id, 'success' => $result->isSuccess()]);
            return $this->resultToJson($result, 201, $runId);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.import.error', ['run_id' => $runId, 'type' => 'class', 'id' => $id, 'error' => $e->getMessage()]);
            Log::error('Erreur lors de l\'import de classe via API', ['id' => $id, 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import de la classe',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
            ], 500);
        }
    }

    /**
     * Import d'un monstre depuis DofusDB (pipeline Core).
     */
    public function importMonster(Request $request, int $id): JsonResponse
    {
        $runId = $this->runIdFromRequest($request);
        try {
            $options = $this->optionsFromRequest($request, $runId);
            $this->logScrapping('info', 'scrapping.import.start', ['run_id' => $runId, 'type' => 'monster', 'id' => $id]);
            $result = $this->orchestrator->runOne('dofusdb', 'monster', $id, $options);
            $this->logScrapping('info', 'scrapping.import.done', ['run_id' => $runId, 'type' => 'monster', 'id' => $id, 'success' => $result->isSuccess()]);
            return $this->resultToJson($result, 201, $runId);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.import.error', ['run_id' => $runId, 'type' => 'monster', 'id' => $id, 'error' => $e->getMessage()]);
            Log::error('Erreur lors de l\'import de monstre via API', ['id' => $id, 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import du monstre',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
            ], 500);
        }
    }

    /**
     * Import d'un objet depuis DofusDB (pipeline Core).
     */
    public function importItem(Request $request, int $id): JsonResponse
    {
        $runId = $this->runIdFromRequest($request);
        try {
            $options = $this->optionsFromRequest($request, $runId);
            $this->logScrapping('info', 'scrapping.import.start', ['run_id' => $runId, 'type' => 'item', 'id' => $id]);
            $result = $this->orchestrator->runOne('dofusdb', 'item', $id, $options);
            $this->logScrapping('info', 'scrapping.import.done', ['run_id' => $runId, 'type' => 'item', 'id' => $id, 'success' => $result->isSuccess()]);
            return $this->resultToJson($result, 201, $runId);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.import.error', ['run_id' => $runId, 'type' => 'item', 'id' => $id, 'error' => $e->getMessage()]);
            Log::error('Erreur lors de l\'import d\'objet via API', ['id' => $id, 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import de l\'objet',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
            ], 500);
        }
    }

    /**
     * Import d'une ressource (item DofusDB, pipeline Core).
     */
    public function importResource(Request $request, int $id): JsonResponse
    {
        $runId = $this->runIdFromRequest($request);
        $resolved = $this->resolveEntityForImport('resource');
        try {
            $options = $this->optionsFromRequest($request, $runId);
            $this->logScrapping('info', 'scrapping.import.start', ['run_id' => $runId, 'type' => 'resource', 'id' => $id]);
            $result = $this->orchestrator->runOne($resolved['source'], $resolved['entity'], $id, $options);
            $this->logScrapping('info', 'scrapping.import.done', ['run_id' => $runId, 'type' => 'resource', 'id' => $id, 'success' => $result->isSuccess()]);
            return $this->resultToJson($result, 201, $runId);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.import.error', ['run_id' => $runId, 'type' => 'resource', 'id' => $id, 'error' => $e->getMessage()]);
            Log::error('Erreur import ressource via API', ['id' => $id, 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import de la ressource', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString(), 'run_id' => $runId], 500);
        }
    }

    /**
     * Import d'un consommable (item DofusDB, pipeline Core).
     */
    public function importConsumable(Request $request, int $id): JsonResponse
    {
        $runId = $this->runIdFromRequest($request);
        $resolved = $this->resolveEntityForImport('consumable');
        try {
            $options = $this->optionsFromRequest($request, $runId);
            $this->logScrapping('info', 'scrapping.import.start', ['run_id' => $runId, 'type' => 'consumable', 'id' => $id]);
            $result = $this->orchestrator->runOne($resolved['source'], $resolved['entity'], $id, $options);
            $this->logScrapping('info', 'scrapping.import.done', ['run_id' => $runId, 'type' => 'consumable', 'id' => $id, 'success' => $result->isSuccess()]);
            return $this->resultToJson($result, 201, $runId);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.import.error', ['run_id' => $runId, 'type' => 'consumable', 'id' => $id, 'error' => $e->getMessage()]);
            Log::error('Erreur import consommable via API', ['id' => $id, 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import du consommable', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString(), 'run_id' => $runId], 500);
        }
    }

    /**
     * Import d'un sort depuis DofusDB (pipeline Core).
     */
    public function importSpell(Request $request, int $id): JsonResponse
    {
        $runId = $this->runIdFromRequest($request);
        try {
            $options = $this->optionsFromRequest($request, $runId);
            $this->logScrapping('info', 'scrapping.import.start', ['run_id' => $runId, 'type' => 'spell', 'id' => $id]);
            $result = $this->orchestrator->runOne('dofusdb', 'spell', $id, $options);
            $this->logScrapping('info', 'scrapping.import.done', ['run_id' => $runId, 'type' => 'spell', 'id' => $id, 'success' => $result->isSuccess()]);
            return $this->resultToJson($result, 201, $runId);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.import.error', ['run_id' => $runId, 'type' => 'spell', 'id' => $id, 'error' => $e->getMessage()]);
            Log::error('Erreur import sort via API', ['id' => $id, 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import du sort', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString(), 'run_id' => $runId], 500);
        }
    }

    /**
     * Import d'une panoplie depuis DofusDB (pipeline Core, si l'entité est configurée).
     */
    public function importPanoply(Request $request, int $id): JsonResponse
    {
        $runId = $this->runIdFromRequest($request);
        try {
            $options = $this->optionsFromRequest($request, $runId);
            $this->logScrapping('info', 'scrapping.import.start', ['run_id' => $runId, 'type' => 'panoply', 'id' => $id]);
            $result = $this->orchestrator->runOne('dofusdb', 'panoply', $id, $options);
            $this->logScrapping('info', 'scrapping.import.done', ['run_id' => $runId, 'type' => 'panoply', 'id' => $id, 'success' => $result->isSuccess()]);
            return $this->resultToJson($result, 201, $runId);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.import.error', ['run_id' => $runId, 'type' => 'panoply', 'id' => $id, 'error' => $e->getMessage()]);
            Log::error('Erreur import panoplie via API', ['id' => $id, 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import de la panoplie', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString(), 'run_id' => $runId], 500);
        }
    }

    /**
     * Import en lot : boucle runOne pour chaque entité (pipeline Core).
     */
    public function importBatch(Request $request): JsonResponse
    {
        $runId = $this->runIdFromRequest($request);
        try {
            $request->validate([
                'entities' => ['required', 'array', 'min:1'],
                'entities.*.type' => ['required', 'string', 'in:class,monster,item,spell,panoply,resource,consumable,equipment'],
                'entities.*.id' => ['required', 'integer', 'min:1'],
            ]);
            $options = $this->optionsFromRequest($request, $runId);
            $entities = $request->input('entities');
            $this->logScrapping('info', 'scrapping.batch.start', ['run_id' => $runId, 'count' => count($entities)]);
            $orchestrator = $this->orchestrator;
            $results = [];
            $successCount = 0;
            $errorCount = 0;
            foreach ($entities as $item) {
                $type = (string) ($item['type'] ?? '');
                $id = (int) ($item['id'] ?? 0);
                $resolved = $this->resolveEntityForImport($type);
                if ($id <= 0) {
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
                        'relations' => $result->isSuccess() ? ($result->getRelations() ?? []) : [],
                    ];
                    $result->isSuccess() ? $successCount++ : $errorCount++;
                } catch (\Throwable $e) {
                    $results[] = ['type' => $type, 'id' => $id, 'success' => false, 'error' => $e->getMessage()];
                    $errorCount++;
                }
            }
            $statusCode = $errorCount === 0 ? 201 : 207;
            $this->logScrapping('info', 'scrapping.batch.done', [
                'run_id' => $runId,
                'count' => count($entities),
                'success' => $successCount,
                'errors' => $errorCount,
            ]);
            return response()->json([
                'success' => $errorCount === 0,
                'message' => $errorCount === 0 ? 'Tous les imports ont réussi' : 'Certains imports ont échoué',
                'summary' => ['total' => count($entities), 'success' => $successCount, 'errors' => $errorCount],
                'results' => $results,
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], $statusCode);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], 422);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.batch.error', ['run_id' => $runId, 'count' => count($entities ?? []), 'error' => $e->getMessage()]);
            Log::error('Erreur import en lot via API', ['count' => count($entities ?? []), 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import en lot',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], 500);
        }
    }

    /**
     * Import avec fusion : import avec force_update (choix par propriété non implémenté dans le pipeline Core).
     */
    public function importWithMerge(Request $request): JsonResponse
    {
        $runId = $this->runIdFromRequest($request);
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
            $options = $this->optionsFromRequest($request, $runId);
            $options['force_update'] = true;
            $result = $this->orchestrator->runOne($resolved['source'], $resolved['entity'], $dofusdbId, $options);
            return $this->resultToJson($result, 201, $runId);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Erreur de validation', 'errors' => $e->errors(), 'timestamp' => now()->toISOString(), 'run_id' => $runId], 422);
        } catch (\Throwable $e) {
            Log::error('Erreur import avec fusion', ['error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'import avec fusion', 'error' => $e->getMessage(), 'timestamp' => now()->toISOString(), 'run_id' => $runId], 500);
        }
    }

    /**
     * Import d'une plage d'ID (boucle runOne, pipeline Core).
     */
    public function importRange(Request $request): JsonResponse
    {
        $runId = $this->runIdFromRequest($request);
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in($this->entityMeta->allowedTypes())],
            'start_id' => ['required', 'integer', 'min:1'],
            'end_id' => ['required', 'integer', 'min:1'],
        ]);
        $type = (string) $validated['type'];
        $startId = (int) $validated['start_id'];
        $endId = (int) $validated['end_id'];
        if ($startId > $endId) {
            return response()->json([
                'success' => false,
                'message' => 'La valeur de début doit être inférieure ou égale à la valeur de fin',
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], 422);
        }
        $maxId = $this->getMaxIdForType($type);
        if ($startId < 1 || $endId > $maxId) {
            return response()->json([
                'success' => false,
                'message' => "La plage doit être comprise entre 1 et {$maxId}",
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], 422);
        }
        $resolved = $this->resolveEntityForImport($type);
        try {
            $options = $this->optionsFromRequest($request, $runId);
            $this->logScrapping('info', 'scrapping.range.start', ['run_id' => $runId, 'type' => $type, 'start_id' => $startId, 'end_id' => $endId]);
            $options['limit'] = $endId - $startId + 1;
            $options['offset'] = 0;
            $filters = ['idMin' => $startId, 'idMax' => $endId];
            $orchestrator = $this->orchestrator;
            $result = $orchestrator->runMany($resolved['source'], $resolved['entity'], $filters, $options);

            if (!$result->isSuccess()) {
                return response()->json([
                    'success' => false,
                    'message' => $result->getMessage(),
                    'errors' => $result->getValidationErrors(),
                    'range' => ['type' => $type, 'start' => $startId, 'end' => $endId],
                    'timestamp' => now()->toISOString(),
                    'run_id' => $runId,
                    'debug' => $this->debugPayload($runId),
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
            $this->logScrapping('info', 'scrapping.range.done', [
                'run_id' => $runId,
                'type' => $type,
                'start_id' => $startId,
                'end_id' => $endId,
                'success' => $successCount,
                'errors' => $errorCount,
            ]);
            return response()->json([
                'success' => $errorCount === 0,
                'message' => $errorCount === 0 ? 'Import de plage terminé' : 'Import de plage avec erreurs',
                'summary' => ['total' => count($results), 'success' => $successCount, 'errors' => $errorCount],
                'results' => $results,
                'range' => ['type' => $type, 'start' => $startId, 'end' => $endId],
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], $statusCode);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.range.error', ['run_id' => $runId, 'type' => $type, 'start_id' => $startId, 'end_id' => $endId, 'error' => $e->getMessage()]);
            Log::error('Erreur import de plage', ['type' => $type, 'start_id' => $startId, 'end_id' => $endId, 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import de la plage',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], 500);
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
     * Enrichit les données converties du sort pour la prévisualisation : ajoute "po" (affichage) à partir de po_min/po_max.
     *
     * @param array<string, mixed> $converted Structure convertie (modifiée en place)
     */
    private function enrichSpellConvertedForPreview(array &$converted): void
    {
        $spells = &$converted['spells'];
        if (! is_array($spells)) {
            return;
        }
        $min = $spells['spell_po_min'] ?? $spells['po_min'] ?? $spells['po'] ?? null;
        $max = $spells['spell_po_max'] ?? $spells['po_max'] ?? $spells['po'] ?? null;
        $min = $min !== null ? (string) $min : '1';
        $max = $max !== null ? (string) $max : $min;
        $spells['po'] = $min === $max ? $min : $min . '-' . $max;
    }

    /**
     * Import complet d'un type d'entité (pipeline Core).
     */
    public function importAll(Request $request): JsonResponse
    {
        $validated = $request->validate(['type' => ['required', 'string', Rule::in($this->entityMeta->allowedTypes())]]);
        $type = (string) $validated['type'];
        $maxId = $this->getMaxIdForType($type);
        $request->merge(['start_id' => 1, 'end_id' => $maxId]);
        return $this->importRange($request);
    }

    /**
     * Prévisualisation d'une entité avant import (runOne avec dry_run, pipeline Core).
     * Retourne raw (données brutes DofusDB) pour alimenter le bloc « Relations détectées » (spells, drops, recipe, summon) côté front.
     */
    public function preview(string $type, int $id): JsonResponse
    {
        $runId = (string) Str::uuid();
        UnknownCharacteristicRunTracker::reset($runId);
        $normalizedType = strtolower($type);
        if (!$this->entityMeta->isAllowedType($normalizedType)) {
            return response()->json([
                'success' => false,
                'message' => 'Type d\'entité non supporté',
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], 422);
        }
        $maxId = $this->getMaxIdForType($normalizedType);
        if ($id < 1 || $id > $maxId) {
            return response()->json([
                'success' => false,
                'message' => "L'identifiant doit être compris entre 1 et {$maxId}",
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], 422);
        }
        $resolved = $this->resolveEntityForImport($normalizedType);
        try {
            $options = ['convert' => true, 'validate' => true, 'integrate' => false, 'dry_run' => true, 'force_update' => false, 'lang' => 'fr', 'run_id' => $runId];
            $this->logScrapping('info', 'scrapping.preview.start', ['run_id' => $runId, 'type' => $normalizedType, 'id' => $id]);
            $result = $this->orchestrator->runOne($resolved['source'], $resolved['entity'], $id, $options);
            $converted = $result->getConverted();
            if ($normalizedType === 'spell' && is_array($converted)) {
                $this->enrichSpellConvertedForPreview($converted);
            }
            $comparisonType = $this->entityTypeForComparison($normalizedType);
            $existingRecord = $comparisonType !== null && is_array($converted)
                ? $this->integrationService->getExistingAttributesForComparison($comparisonType, $converted)
                : null;

            $spellEffectsSimulation = null;
            if ($normalizedType === 'spell' && is_array($converted) && is_array($converted['spell_effects'] ?? null)) {
                $spellEffectsSimulation = $this->integrationService->simulateSpellEffects($converted['spell_effects']);
            }

            $data = [
                'success' => $result->isSuccess(),
                'raw' => $result->getRaw(),
                'converted' => $converted,
                'validation_errors' => $result->getValidationErrors(),
                'existing' => $existingRecord !== null ? ['record' => $existingRecord] : null,
                'spell_effects_simulation' => $spellEffectsSimulation,
            ];
            $this->logScrapping('info', 'scrapping.preview.done', ['run_id' => $runId, 'type' => $normalizedType, 'id' => $id, 'success' => $result->isSuccess()]);
            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ]);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.preview.error', ['run_id' => $runId, 'type' => $type, 'id' => $id, 'error' => $e->getMessage()]);
            Log::error('Erreur prévisualisation', ['type' => $type, 'id' => $id, 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la prévisualisation',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], 500);
        }
    }

    /**
     * Prévisualisation en lot : pour chaque ID, runOne en dry_run et retourne les données converties.
     * Limite : 100 IDs par requête. Utilisé par l'UI pour afficher valeur convertie + brute par ligne.
     */
    public function previewBatch(Request $request): JsonResponse
    {
        $runId = $this->runIdFromRequest($request);
        UnknownCharacteristicRunTracker::reset($runId);
        try {
            $validated = $request->validate([
                'type' => ['required', 'string', 'in:class,monster,item,spell,panoply,resource,consumable,equipment'],
                'ids' => ['required', 'array', 'min:1', 'max:100'],
                'ids.*' => ['integer', 'min:1'],
            ]);
            $type = (string) $validated['type'];
            $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
            $resolved = $this->resolveEntityForImport($type);
            $this->logScrapping('info', 'scrapping.preview_batch.start', ['run_id' => $runId, 'type' => $type, 'count' => count($ids)]);
            $maxId = $this->getMaxIdForType($type);
            $comparisonType = $this->entityTypeForComparison($type);
            $options = ['convert' => true, 'validate' => true, 'integrate' => false, 'dry_run' => true, 'force_update' => false, 'lang' => 'fr', 'run_id' => $runId];
            $orchestrator = $this->orchestrator;
            $items = [];
            foreach ($ids as $id) {
                if ($id < 1 || $id > $maxId) {
                    $items[] = ['id' => $id, 'converted' => null, 'existing' => null, 'error' => "ID hors plage 1-{$maxId}"];
                    continue;
                }
                try {
                    $result = $orchestrator->runOne($resolved['source'], $resolved['entity'], $id, $options);
                    $converted = $result->getConverted();
                    if ($type === 'spell' && is_array($converted)) {
                        $this->enrichSpellConvertedForPreview($converted);
                    }
                    $existingRecord = $comparisonType !== null && is_array($converted)
                        ? $this->integrationService->getExistingAttributesForComparison($comparisonType, $converted)
                        : null;
                    $spellEffectsSimulation = null;
                    if ($type === 'spell' && is_array($converted) && is_array($converted['spell_effects'] ?? null)) {
                        $spellEffectsSimulation = $this->integrationService->simulateSpellEffects($converted['spell_effects']);
                    }
                    $itemPayload = [
                        'id' => $id,
                        'raw' => $result->getRaw(),
                        'converted' => $converted,
                        'existing' => $existingRecord !== null ? ['record' => $existingRecord] : null,
                        'spell_effects_simulation' => $spellEffectsSimulation,
                        'error' => $result->isSuccess() ? null : $result->getMessage(),
                    ];
                    if ($type === 'item') {
                        $itemData = $converted['items'] ?? $converted['resources'] ?? $converted['consumables'] ?? [];
                        $typeId = isset($itemData['type_id']) ? (int) $itemData['type_id'] : null;
                        $itemPayload['resolved_entity_type'] = $this->integrationService->resolveItemEntityType($typeId);
                    }
                    $items[] = $itemPayload;
                } catch (\Throwable $e) {
                    $items[] = ['id' => $id, 'converted' => null, 'existing' => null, 'error' => $e->getMessage()];
                }
            }
            $this->logScrapping('info', 'scrapping.preview_batch.done', [
                'run_id' => $runId,
                'type' => $type,
                'count' => count($ids),
            ]);
            return response()->json([
                'success' => true,
                'data' => ['items' => $items],
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], 422);
        } catch (\Throwable $e) {
            $this->logScrapping('error', 'scrapping.preview_batch.error', ['run_id' => $runId, 'type' => $type ?? null, 'error' => $e->getMessage()]);
            Log::error('Erreur prévisualisation batch', ['type' => $type ?? null, 'error' => $e->getMessage(), 'run_id' => $runId]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la prévisualisation en lot',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
                'run_id' => $runId,
                'debug' => $this->debugPayload($runId),
            ], 500);
        }
    }
}

