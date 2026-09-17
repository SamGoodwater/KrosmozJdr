<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\GenerativeAi;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerativeAi\ConvertEntityRequest;
use App\Jobs\GenerativeAi\ConvertPacketJob;
use App\Models\AiGenerationRun;
use App\Services\Entity\EntityUpdateDiffService;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\CostEstimator;
use App\Services\GenerativeAi\GenerativeAiClient;
use App\Services\GenerativeAi\Specializations\SpecializationRegistry;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

/**
 * Déclenche une conversion IA (admin). 1 paquet = 1 job synchrone = 1 requête LLM.
 *
 * Le job n’est plus poussé en file `database` : un toast « lancé » sans worker
 * laissait croire à un succès (0 token Anthropic, fiche inchangée).
 *
 * @example POST /api/entities/monsters/12/ia-convert {"action":"encounter"}
 */
class IaConvertController extends Controller
{
    public function __invoke(ConvertEntityRequest $request, string $entityType, int $id): JsonResponse
    {
        $entity = EntityModelRegistry::resolveModel($entityType, $id);
        if (! $entity instanceof Model) {
            return response()->json([
                'success' => false,
                'message' => 'Entité introuvable.',
            ], 404);
        }

        $this->authorize('generate', $entity);

        $client = app(GenerativeAiClient::class);
        if (! $client->hasApiKey()) {
            return response()->json([
                'success' => false,
                'queued' => false,
                'status' => AiGenerationRun::STATUS_FAILED,
                'message' => 'Clé Anthropic absente : aucun appel n’a été lancé.',
            ], 422);
        }

        $estimator = app(CostEstimator::class);
        $fallbackAction = $estimator->actionForEntityType($entityType);
        $action = $request->action($fallbackAction);

        try {
            app(SpecializationRegistry::class)->forAction($action);
        } catch (\InvalidArgumentException) {
            return response()->json([
                'success' => false,
                'message' => 'Action IA inconnue.',
            ], 422);
        }

        $run = AiGenerationRun::query()->create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'entity_type' => EntityModelRegistry::normalizeType($entityType),
            'entity_id' => $id,
            'status' => AiGenerationRun::STATUS_QUEUED,
        ]);

        $conversion = new ConversionRequest(
            action: $action,
            entityType: match (EntityModelRegistry::normalizeType($entityType)) {
                'monsters' => 'monster',
                'spells' => 'spell',
                'npcs' => 'npc',
                'items' => 'item',
                'consumables' => 'consumable',
                default => rtrim(EntityModelRegistry::normalizeType($entityType), 's'),
            },
            entityId: $id,
            brief: $request->brief(),
            force: $request->force(),
            userId: $request->user()?->id,
            runId: (int) $run->id,
        );

        $diffService = app(EntityUpdateDiffService::class);
        $before = $diffService->capture($entity);
        set_time_limit(200);

        try {
            ConvertPacketJob::dispatchSync($conversion);
        } catch (\Throwable $exception) {
            $run->refresh();
            $message = $this->publicError($run, $exception, $client);

            return response()->json([
                'success' => false,
                'queued' => false,
                'status' => $run->status,
                'run_id' => $run->id,
                'entity_id' => $run->entity_id,
                'related_ids' => $run->related_ids ?? [],
                'estimate' => $estimator->forAction($action),
                'message' => $message,
            ], 422);
        }

        $estimate = $estimator->forAction($action);
        $run->refresh();

        if ($run->status === AiGenerationRun::STATUS_QUEUED) {
            return response()->json([
                'success' => false,
                'queued' => true,
                'status' => $run->status,
                'run_id' => $run->id,
                'message' => 'La conversion IA n’a pas abouti (toujours en file). Aucun appel n’est garanti.',
            ], 503);
        }

        if ($run->status !== AiGenerationRun::STATUS_SUCCESS) {
            return response()->json([
                'success' => false,
                'queued' => false,
                'status' => $run->status,
                'run_id' => $run->id,
                'entity_id' => $run->entity_id,
                'related_ids' => $run->related_ids ?? [],
                'estimate' => $estimate,
                'message' => $run->error ?: 'La conversion IA a échoué.',
            ], 422);
        }

        $fresh = $entity->fresh() ?? $entity;
        $user = $request->user();
        $diff = $user !== null
            ? $diffService->remember($user, $entityType, $id, 'ia', $before, $fresh)
            : null;

        $changed = (int) ($diff['changed_count'] ?? 0);
        $message = $changed === 0
            ? 'Conversion IA terminée : aucun champ modifié (gel ou allowlist).'
            : 'Conversion IA enregistrée en auto (à relire).';

        return response()->json([
            'success' => true,
            'queued' => false,
            'status' => $run->status,
            'run_id' => $run->id,
            'entity_id' => $run->entity_id,
            'related_ids' => $run->related_ids ?? [],
            'estimate' => $estimate,
            'diff' => $diff,
            'message' => $message,
        ]);
    }

    private function publicError(AiGenerationRun $run, \Throwable $exception, GenerativeAiClient $client): string
    {
        $message = is_string($run->error) && $run->error !== ''
            ? $run->error
            : $exception->getMessage();
        $key = $client->apiKey();
        if ($key !== '') {
            $message = str_replace($key, '[redacted]', $message);
        }

        return $message !== '' ? $message : 'La conversion IA a échoué.';
    }
}
