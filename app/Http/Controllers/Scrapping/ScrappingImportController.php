<?php

declare(strict_types=1);

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use App\Services\Scrapping\Core\Orchestrator\OrchestratorResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * API import scrapping : pipeline Collect → Conversion → Validation → Intégration.
 *
 * Ex. POST /api/scrapping/import/monster/31
 */
class ScrappingImportController extends Controller
{
    /**
     * Import d'un seul objet via le pipeline.
     *
     * @param string $entity monster, breed, spell, item ou alias (class → breed)
     * @param int $id ID DofusDB
     */
    public function importOne(Request $request, string $entity, int $id): JsonResponse
    {
        $aliasResolver = CollectAliasResolver::default();
        $aliasConfig = $aliasResolver->resolve($entity);
        $source = 'dofusdb';
        $entityKey = $entity;

        if ($aliasConfig !== null) {
            $source = (string) ($aliasConfig['source'] ?? $source);
            $entityKey = (string) ($aliasConfig['entity'] ?? $entity);
        } else {
            $entityKey = $entity === 'class' ? 'breed' : $entity;
        }

        try {
            $configLoader = ConfigLoader::default();
            $entities = $configLoader->listEntities($source);
            if (!in_array($entityKey, $entities, true)) {
                return response()->json([
                    'success' => false,
                    'message' => "Entité inconnue : {$entity}. Valeurs : " . implode(', ', $aliasResolver->listAliases()),
                    'timestamp' => now()->toISOString(),
                ], 422);
            }
        } catch (\Throwable $e) {
            Log::warning('Config scrapping introuvable ou invalide', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Configuration scrapping indisponible.',
                'timestamp' => now()->toISOString(),
            ], 503);
        }

        $options = [
            'convert' => true,
            'validate' => $request->boolean('validate', true),
            'integrate' => $request->boolean('integrate', true),
            'dry_run' => $request->boolean('dry_run', false),
            'force_update' => $request->boolean('force_update', false),
            'lang' => (string) $request->input('lang', 'fr'),
        ];

        try {
            $orchestrator = Orchestrator::default();
            $result = $orchestrator->runOne($source, $entityKey, $id, $options);

            return $this->resultToJson($result, 201);
        } catch (\Throwable $e) {
            Log::error('Erreur import scrapping', [
                'entity' => $entityKey,
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    private function resultToJson(OrchestratorResult $result, int $successStatus = 200): JsonResponse
    {
        if ($result->isSuccess()) {
            $data = null;
            $integrationResult = $result->getIntegrationResult();
            if ($integrationResult !== null && $integrationResult->isSuccess()) {
                $data = $integrationResult->getData();
            }
            if ($data === null) {
                $data = $result->getConverted();
            }

            return response()->json([
                'success' => true,
                'message' => $result->getMessage(),
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ], $successStatus);
        }

        $errors = $result->getValidationErrors();

        return response()->json([
            'success' => false,
            'message' => $result->getMessage(),
            'errors' => $errors,
            'timestamp' => now()->toISOString(),
        ], 400);
    }
}
