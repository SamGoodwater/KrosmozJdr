<?php

declare(strict_types=1);

namespace App\Http\Controllers\Scrapping\Concerns;

use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Orchestrator\OrchestratorResult;
use Illuminate\Http\JsonResponse;

/**
 * Trait partagé pour les contrôleurs scrapping : réponse JSON depuis OrchestratorResult
 * et résolution d'entité pour l'import (alias → source + entity).
 *
 * Requiert une propriété $aliasResolver (CollectAliasResolver) sur le contrôleur.
 */
trait RespondsWithOrchestratorResult
{
    /**
     * Convertit un OrchestratorResult en réponse JSON (succès ou erreur).
     */
    protected function resultToJson(OrchestratorResult $result, int $successStatus = 200, ?string $runId = null): JsonResponse
    {
        if ($result->isSuccess()) {
            $data = $result->getIntegrationResult()?->getData() ?? $result->getConverted();
            $payload = [
                'success' => true,
                'message' => $result->getMessage(),
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ];
            if ($runId !== null && $runId !== '') {
                $payload['run_id'] = $runId;
            }

            return response()->json($payload, $successStatus);
        }
        $payload = [
            'success' => false,
            'message' => $result->getMessage(),
            'error' => $result->getMessage(),
            'errors' => $result->getValidationErrors(),
            'timestamp' => now()->toISOString(),
        ];
        if ($runId !== null && $runId !== '') {
            $payload['run_id'] = $runId;
        }

        return response()->json($payload, 400);
    }

    /**
     * Résout un type d'entité (ex. class, resource) vers source + entity de config.
     *
     * @return array{source: string, entity: string}
     */
    protected function resolveEntityForImport(string $type): array
    {
        $cfg = $this->aliasResolver->resolve($type);
        if ($cfg !== null) {
            return [
                'source' => (string) ($cfg['source'] ?? 'dofusdb'),
                'entity' => (string) ($cfg['entity'] ?? $type),
            ];
        }

        return ['source' => 'dofusdb', 'entity' => $type === 'class' ? 'breed' : $type];
    }
}
