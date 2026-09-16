<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Models\AiGenerationRun;
use App\Models\User;
use App\Services\GenerativeAi\Specializations\SpecializationRegistry;
use RuntimeException;

/**
 * Pipeline : assembleur → LLM JSON → validateurs (+ retries) → writer `auto`.
 *
 * 1 paquet = 1 requête. Retries = `generation.max_retries`.
 *
 * @example $result = app(ConversionPipeline::class)->run($request);
 */
final class ConversionPipeline
{
    /**
     * @return array{run_id: int, entity_id: int, related_ids: list<int>, model: string, input_tokens: int, output_tokens: int}
     */
    public function run(ConversionRequest $request): array
    {
        $user = $request->userId !== null ? User::query()->find($request->userId) : null;
        ConversionRequest::assertUserMayGenerate($user);

        $run = $request->runId !== null
            ? AiGenerationRun::query()->findOrFail($request->runId)
            : AiGenerationRun::query()->create([
                'user_id' => $request->userId,
                'action' => $request->action,
                'entity_type' => $request->entityType,
                'entity_id' => $request->entityId,
                'status' => AiGenerationRun::STATUS_QUEUED,
            ]);

        try {
            $assembled = app(ContextAssembler::class)->assemble($request);
            $spec = app(SpecializationRegistry::class)->forAction($request->action);
            $maxRetries = (int) (GenerationConfigLoader::default()->get('generation.max_retries', 2) ?: 2);
            $maxRetries = max(0, min(5, $maxRetries));

            $errors = [];
            $llm = null;
            $payload = [];
            $attempts = $maxRetries + 1;

            for ($i = 0; $i < $attempts; $i++) {
                $userMessage = $assembled->userMessage;
                if ($errors !== []) {
                    $userMessage .= "\n\nLe JSON précédent a été refusé :\n- ".implode("\n- ", $errors)
                        ."\nCorrige uniquement ces points, même schéma.";
                }
                $llm = app(GenerativeAiClient::class)->complete(
                    $assembled->supervisor,
                    $userMessage,
                    $assembled->schema
                );
                $payload = $llm->json;
                $errors = $spec->validate($payload, $request, $assembled->profile);
                if ($errors === []) {
                    break;
                }
            }

            if ($errors !== []) {
                throw new RuntimeException('Validateur IA : '.implode(' ', $errors));
            }

            $persisted = $spec->persist($payload, $request, $assembled->profile);

            $run->fill([
                'entity_id' => $persisted['entity_id'],
                'related_ids' => $persisted['related_ids'],
                'model' => $llm?->model,
                'input_tokens' => $llm?->inputTokens ?? 0,
                'output_tokens' => $llm?->outputTokens ?? 0,
                'cache_read_tokens' => $llm?->cacheReadTokens ?? 0,
                'status' => AiGenerationRun::STATUS_SUCCESS,
                'error' => null,
                'prompt_version' => 'v1',
                'ai_generated_at' => now(),
            ]);
            $run->save();

            return [
                'run_id' => (int) $run->id,
                'entity_id' => $persisted['entity_id'],
                'related_ids' => $persisted['related_ids'],
                'model' => (string) $llm?->model,
                'input_tokens' => (int) ($llm?->inputTokens ?? 0),
                'output_tokens' => (int) ($llm?->outputTokens ?? 0),
            ];
        } catch (\Throwable $exception) {
            $run->fill([
                'status' => AiGenerationRun::STATUS_FAILED,
                'error' => $exception->getMessage(),
                'ai_generated_at' => now(),
            ]);
            $run->save();

            throw $exception;
        }
    }
}
