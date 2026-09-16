<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\GenerativeAi;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerativeAi\ConvertEntityRequest;
use App\Jobs\GenerativeAi\ConvertPacketJob;
use App\Models\AiGenerationRun;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\CostEstimator;
use App\Services\GenerativeAi\Specializations\SpecializationRegistry;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

/**
 * Déclenche une conversion IA (admin). 1 paquet = 1 job = 1 requête LLM.
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

        ConvertPacketJob::dispatch($conversion);

        $estimate = $estimator->forAction($action);
        $run->refresh();

        return response()->json([
            'success' => $run->status !== AiGenerationRun::STATUS_FAILED,
            'queued' => $run->status === AiGenerationRun::STATUS_QUEUED,
            'status' => $run->status,
            'run_id' => $run->id,
            'entity_id' => $run->entity_id,
            'related_ids' => $run->related_ids ?? [],
            'estimate' => $estimate,
            'message' => $run->status === AiGenerationRun::STATUS_SUCCESS
                ? 'Conversion IA enregistrée en auto (à relire).'
                : ($run->status === AiGenerationRun::STATUS_FAILED
                    ? ($run->error ?: 'La conversion IA a échoué.')
                    : 'Conversion IA lancée.'),
        ], $run->status === AiGenerationRun::STATUS_FAILED ? 422 : 200);
    }
}
