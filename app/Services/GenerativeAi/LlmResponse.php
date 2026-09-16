<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

/**
 * Réponse structurée d’un appel LLM (JSON + usage).
 *
 * @example $json = $response->json;
 */
final readonly class LlmResponse
{
    /**
     * @param  array<string, mixed>  $json
     */
    public function __construct(
        public array $json,
        public string $model,
        public int $inputTokens,
        public int $outputTokens,
        public int $cacheReadTokens = 0,
    ) {}
}
