<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\GenerativeAi;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerativeAi\InjectEntityJsonRequest;
use App\Models\AiGenerationRun;
use App\Services\Entity\EntityUpdateDiffService;
use App\Services\GenerativeAi\ConversionPipeline;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\ConversionSafety;
use App\Services\GenerativeAi\Specializations\SpecializationRegistry;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\Model; // pragma: allowlist secret
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Injecte un JSON manuel comme paquet IA (admin) — sans appel LLM.
 *
 * @example POST /api/entities/spells/12/ia-inject {"action":"spell","payload":{"effect":"1d6"}}
 */
class IaInjectController extends Controller
{
    public function __invoke(InjectEntityJsonRequest $request, string $entityType, int $id): JsonResponse
    {
        $entity = EntityModelRegistry::resolveModel($entityType, $id);
        if (! $entity instanceof Model) {
            return response()->json([
                'success' => false,
                'message' => 'Entité introuvable.',
            ], 404);
        }

        $this->authorize('generate', $entity);

        $safety = app(ConversionSafety::class);
        $fallbackAction = $safety->expectedAction($entityType);
        $action = $request->action($fallbackAction);

        try {
            app(SpecializationRegistry::class)->forAction($action);
            $safety->assertActionMatches($action, $entityType);
            $safety->assertMayOverwrite($entity, $request->force());
            $payload = $request->payload();
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'queued' => false,
                'message' => 'Action IA inconnue.',
            ], 422);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'queued' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        $normalizedPlural = EntityModelRegistry::normalizeType($entityType);
        $run = AiGenerationRun::query()->create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'entity_type' => $normalizedPlural,
            'entity_id' => $id,
            'status' => AiGenerationRun::STATUS_QUEUED,
        ]);

        $conversion = new ConversionRequest(
            action: $action,
            entityType: match ($normalizedPlural) {
                'monsters' => 'monster',
                'spells' => 'spell',
                'npcs' => 'npc',
                'items' => 'item',
                'consumables' => 'consumable',
                default => rtrim($normalizedPlural, 's'),
            },
            entityId: $id,
            brief: null,
            force: $request->force(),
            userId: $request->user()?->id,
            runId: (int) $run->id,
        );

        $diffService = app(EntityUpdateDiffService::class);
        $before = $diffService->capture($entity);

        try {
            app(ConversionPipeline::class)->runFromPayload($conversion, $payload);
        } catch (\Throwable $exception) {
            $run->refresh();
            $message = is_string($run->error) && $run->error !== ''
                ? $run->error
                : $exception->getMessage();

            return response()->json([
                'success' => false,
                'queued' => false,
                'status' => $run->status,
                'run_id' => $run->id,
                'entity_id' => $run->entity_id,
                'related_ids' => $run->related_ids ?? [],
                'message' => $message !== '' ? $message : 'L’injection JSON a échoué.',
            ], 422);
        }

        $run->refresh();
        if ($run->status !== AiGenerationRun::STATUS_SUCCESS) {
            return response()->json([
                'success' => false,
                'queued' => false,
                'status' => $run->status,
                'run_id' => $run->id,
                'entity_id' => $run->entity_id,
                'related_ids' => $run->related_ids ?? [],
                'message' => $run->error ?: 'L’injection JSON a échoué.',
            ], 422);
        }

        $fresh = $entity->fresh() ?? $entity;
        $user = $request->user();
        $diff = $user !== null
            ? $diffService->remember($user, $entityType, $id, 'ia', $before, $fresh)
            : null;

        $changed = (int) ($diff['changed_count'] ?? 0);
        $message = $changed === 0
            ? 'JSON injecté : aucun champ modifié (gel ou allowlist).'
            : 'JSON injecté en auto (à relire).';

        return response()->json([
            'success' => true,
            'queued' => false,
            'status' => $run->status,
            'run_id' => $run->id,
            'entity_id' => $run->entity_id,
            'related_ids' => $run->related_ids ?? [],
            'diff' => $diff,
            'message' => $message,
        ]);
    }
}
