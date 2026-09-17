<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

/**
 * Prompt assemblé : préfixe cacheable (tâche + étalons) et suffixe dynamique (fiche, brief).
 *
 * @example $assembled->cachedUserPrefix
 */
final readonly class AssembledPrompt
{
    /**
     * @param  array<string, mixed>  $schema
     * @param  list<array<string, mixed>>  $examples
     */
    public function __construct(
        public string $supervisor,
        public string $cachedUserPrefix,
        public string $dynamicUserMessage,
        public array $schema,
        public EntityGenerationProfile $profile,
        public array $examples,
    ) {}
}
