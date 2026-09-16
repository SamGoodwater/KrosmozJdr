<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

/**
 * Prompt assemblé (superviseur cacheable + tâche + schéma writable-only).
 *
 * @example $assembled->supervisor
 */
final readonly class AssembledPrompt
{
    /**
     * @param  array<string, mixed>  $schema
     * @param  list<array<string, mixed>>  $examples
     */
    public function __construct(
        public string $supervisor,
        public string $userMessage,
        public array $schema,
        public EntityGenerationProfile $profile,
        public array $examples,
    ) {}
}
