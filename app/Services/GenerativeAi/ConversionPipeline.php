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
 * Import manuel : `runFromPayload` saute le LLM et réutilise validate + persist.
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

        $run = $this->resolveRun($request);

        try {
            $spec = app(SpecializationRegistry::class)->forAction($request->action);
            app(ConversionSafety::class)->assertRequest($request, $spec);
            $assembled = app(ContextAssembler::class)->assemble($request);
            $preflight = $spec->preflight($request, $assembled->profile);
            if ($preflight !== []) {
                throw new RuntimeException('Validateur IA : '.implode(' ', $preflight));
            }
            $maxRetries = (int) (GenerationConfigLoader::default()->get('generation.max_retries', 2) ?: 2);
            $maxRetries = max(0, min(5, $maxRetries));

            $errors = [];
            $llm = null;
            $payload = [];
            $attempts = $maxRetries + 1;

            for ($i = 0; $i < $attempts; $i++) {
                $dynamic = $assembled->dynamicUserMessage;
                if ($errors !== []) {
                    $dynamic .= ($dynamic !== '' ? "\n\n" : '')
                        .'Le JSON précédent a été refusé :'."\n- ".implode("\n- ", $errors)
                        ."\nCorrige uniquement ces points, même schéma.";
                }
                $llm = app(GenerativeAiClient::class)->complete(
                    $assembled->supervisor,
                    $dynamic,
                    $assembled->schema,
                    $assembled->cachedUserPrefix,
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

            return $this->persistAndComplete(
                $run,
                $spec->persist($payload, $request, $assembled->profile),
                model: (string) ($llm?->model ?? ''),
                inputTokens: (int) ($llm?->inputTokens ?? 0),
                outputTokens: (int) ($llm?->outputTokens ?? 0),
                cacheReadTokens: (int) ($llm?->cacheReadTokens ?? 0),
                promptVersion: 'v1',
            );
        } catch (\Throwable $exception) {
            $this->failRun($run, $exception);

            throw $exception;
        }
    }

    /**
     * Injecte un JSON manuel (même validate + persist que le LLM, sans appel API).
     *
     * @param  array<string, mixed>  $payload
     * @return array{run_id: int, entity_id: int, related_ids: list<int>, model: string, input_tokens: int, output_tokens: int}
     */
    public function runFromPayload(ConversionRequest $request, array $payload): array
    {
        $user = $request->userId !== null ? User::query()->find($request->userId) : null;
        ConversionRequest::assertUserMayGenerate($user);

        $run = $this->resolveRun($request);

        try {
            $spec = app(SpecializationRegistry::class)->forAction($request->action);
            app(ConversionSafety::class)->assertRequest($request, $spec);
            $profile = GenerationConfigLoader::default()->forEntity($spec->entityType());
            $preflight = $spec->preflight($request, $profile);
            if ($preflight !== []) {
                throw new RuntimeException('Validateur IA : '.implode(' ', $preflight));
            }

            $errors = $spec->validate($payload, $request, $profile);
            if ($errors !== []) {
                throw new RuntimeException('Validateur IA : '.implode(' ', $errors));
            }

            return $this->persistAndComplete(
                $run,
                $spec->persist($payload, $request, $profile),
                model: 'manual-json',
                inputTokens: 0,
                outputTokens: 0,
                cacheReadTokens: 0,
                promptVersion: 'manual-v1',
            );
        } catch (\Throwable $exception) {
            $this->failRun($run, $exception);

            throw $exception;
        }
    }

    private function resolveRun(ConversionRequest $request): AiGenerationRun
    {
        if ($request->runId !== null) {
            return AiGenerationRun::query()->findOrFail($request->runId);
        }

        return AiGenerationRun::query()->create([
            'user_id' => $request->userId,
            'action' => $request->action,
            'entity_type' => $request->entityType,
            'entity_id' => $request->entityId,
            'status' => AiGenerationRun::STATUS_QUEUED,
        ]);
    }

    /**
     * @param  array{entity_id: int, related_ids: list<int>}  $persisted
     * @return array{run_id: int, entity_id: int, related_ids: list<int>, model: string, input_tokens: int, output_tokens: int}
     */
    private function persistAndComplete(
        AiGenerationRun $run,
        array $persisted,
        string $model,
        int $inputTokens,
        int $outputTokens,
        int $cacheReadTokens,
        string $promptVersion,
    ): array {
        $run->fill([
            'entity_id' => $persisted['entity_id'],
            'related_ids' => $persisted['related_ids'],
            'model' => $model !== '' ? $model : null,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'cache_read_tokens' => $cacheReadTokens,
            'status' => AiGenerationRun::STATUS_SUCCESS,
            'error' => null,
            'prompt_version' => $promptVersion,
            'ai_generated_at' => now(),
        ]);
        $run->save();

        return [
            'run_id' => (int) $run->id,
            'entity_id' => $persisted['entity_id'],
            'related_ids' => $persisted['related_ids'],
            'model' => $model,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
        ];
    }

    private function failRun(AiGenerationRun $run, \Throwable $exception): void
    {
        $run->fill([
            'status' => AiGenerationRun::STATUS_FAILED,
            'error' => $exception->getMessage(),
            'ai_generated_at' => now(),
        ]);
        $run->save();
    }
}
