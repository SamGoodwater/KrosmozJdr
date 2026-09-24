<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\GenerativeAi;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerativeAi\InjectEntityJsonRequest;
use App\Models\AiGenerationRun;
use App\Services\Entity\EntityUpdateDiffService;
use App\Services\Entity\GenericEntityJsonInjector;
use App\Services\GenerativeAi\ConversionPipeline;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\ConversionSafety;
use App\Services\GenerativeAi\Specializations\SpecializationRegistry;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\Model; // pragma: allowlist secret
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

/**
 * Injecte un JSON manuel (admin) — spécialisation IA si convertible, sinon fillable générique.
 *
 * @example POST /api/entities/spells/12/ia-inject {"action":"spell","payload":{"effect":"1d6"}}
 * @example POST /api/entities/campaigns/3/ia-inject {"payload":{"name":"Incarnam","description":"…"}}
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
        $normalizedPlural = EntityModelRegistry::normalizeType($entityType);
        $spec = app(SpecializationRegistry::class)->tryForEntityType($normalizedPlural);

        try {
            $safety->assertMayOverwrite($entity, $request->force());
            $payload = $request->payload();
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'queued' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        if ($spec !== null) {
            return $this->injectViaSpecialization($request, $entity, $entityType, $normalizedPlural, $id, $payload, $spec->key());
        }

        return $this->injectViaGeneric($request, $entity, $entityType, $normalizedPlural, $id, $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function injectViaSpecialization(
        InjectEntityJsonRequest $request,
        Model $entity,
        string $entityType,
        string $normalizedPlural,
        int $id,
        array $payload,
        string $action,
    ): JsonResponse {
        $safety = app(ConversionSafety::class);
        $requested = $request->action($action);
        try {
            app(SpecializationRegistry::class)->forAction($requested);
            $safety->assertActionMatches($requested, $entityType);
        } catch (InvalidArgumentException) {
            return response()->json([
                'success' => false,
                'queued' => false,
                'message' => 'Action IA inconnue.',
            ], 422);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'queued' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        $run = AiGenerationRun::query()->create([
            'user_id' => $request->user()?->id,
            'action' => $requested,
            'entity_type' => $normalizedPlural,
            'entity_id' => $id,
            'status' => AiGenerationRun::STATUS_QUEUED,
        ]);

        $conversion = new ConversionRequest(
            action: $requested,
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
        } catch (Throwable $exception) {
            return $this->failedInjectResponse($run, $exception);
        }

        return $this->successInjectResponse($request, $entity, $entityType, $id, $run, $before, $diffService);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function injectViaGeneric(
        InjectEntityJsonRequest $request,
        Model $entity,
        string $entityType,
        string $normalizedPlural,
        int $id,
        array $payload,
    ): JsonResponse {
        $run = AiGenerationRun::query()->create([
            'user_id' => $request->user()?->id,
            'action' => 'inject',
            'entity_type' => $normalizedPlural,
            'entity_id' => $id,
            'status' => AiGenerationRun::STATUS_QUEUED,
        ]);

        $diffService = app(EntityUpdateDiffService::class);
        $before = $diffService->capture($entity);

        try {
            $persisted = app(GenericEntityJsonInjector::class)->persist($entity, $payload);
            $run->fill([
                'entity_id' => $persisted['entity_id'],
                'related_ids' => $persisted['related_ids'],
                'model' => 'manual-json',
                'input_tokens' => 0,
                'output_tokens' => 0,
                'cache_read_tokens' => 0,
                'status' => AiGenerationRun::STATUS_SUCCESS,
                'prompt_version' => 'manual-v1',
                'ai_generated_at' => now(),
                'error' => null,
            ])->save();
        } catch (Throwable $exception) {
            $run->fill([
                'status' => AiGenerationRun::STATUS_FAILED,
                'error' => $exception->getMessage(),
            ])->save();

            return $this->failedInjectResponse($run, $exception);
        }

        return $this->successInjectResponse($request, $entity, $entityType, $id, $run, $before, $diffService);
    }

    /**
     * @param  array<string, mixed>  $before
     */
    private function successInjectResponse(
        InjectEntityJsonRequest $request,
        Model $entity,
        string $entityType,
        int $id,
        AiGenerationRun $run,
        array $before,
        EntityUpdateDiffService $diffService,
    ): JsonResponse {
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

    private function failedInjectResponse(AiGenerationRun $run, Throwable $exception): JsonResponse
    {
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
}
