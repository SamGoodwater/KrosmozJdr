<?php

declare(strict_types=1);

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\V2\Config\CollectAliasResolver;
use App\Services\Scrapping\V2\Config\ConfigLoader;
use App\Services\Scrapping\V2\Orchestrator\Orchestrator;
use App\Services\Scrapping\V2\Orchestrator\OrchestratorResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * API V2 du scrapping : pipeline Collect → Conversion → Validation → Intégration.
 *
 * Route dédiée pour tester le pipeline V2 en parallèle du legacy.
 * Ex. POST /api/scrapping/v2/import/monster/31
 *
 * @see docs/50-Fonctionnalités/Scrapping/Refonte/TODOLIST_REFONTE_V2.md
 */
class ScrappingV2Controller extends Controller
{
    /**
     * Import d'un seul objet via le pipeline V2.
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
                    'message' => "Entité V2 inconnue : {$entity}. Valeurs : " . implode(', ', $aliasResolver->listAliases()),
                    'timestamp' => now()->toISOString(),
                ], 422);
            }
        } catch (\Throwable $e) {
            Log::warning('Config V2 introuvable ou invalide', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Configuration V2 indisponible.',
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
            Log::error('Erreur import V2', [
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
