<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

/**
 * Modèles Claude autorisés pour la conversion IA métier (allowlist, pas de saisie libre).
 *
 * @example AnthropicModelCatalog::resolve('claude-haiku-4-5');
 */
final class AnthropicModelCatalog
{
    public const DEFAULT = 'claude-haiku-4-5';

    /**
     * @var array<string, array{
     *     id: string,
     *     label: string,
     *     hint: string,
     *     input_usd: float,
     *     output_usd: float,
     *     cache_hit_usd: float,
     *     cache_min_tokens: int
     * }>
     */
    public const MODELS = [
        'claude-haiku-4-5' => [
            'id' => 'claude-haiku-4-5',
            'label' => 'Haiku 4.5 (moins cher)',
            'hint' => 'Défaut. Environ deux fois moins cher que Sonnet. JSON outillé.',
            'input_usd' => 1.0,
            'output_usd' => 5.0,
            'cache_hit_usd' => 0.10,
            'cache_min_tokens' => 4096,
        ],
        'claude-sonnet-5' => [
            'id' => 'claude-sonnet-5',
            'label' => 'Sonnet 5',
            'hint' => 'Meilleur équilibre qualité / prix pour le design JDR.',
            'input_usd' => 2.0,
            'output_usd' => 10.0,
            'cache_hit_usd' => 0.20,
            'cache_min_tokens' => 1024,
        ],
        'claude-opus-5' => [
            'id' => 'claude-opus-5',
            'label' => 'Opus 5',
            'hint' => 'Plus capable, plus cher. Échecs du validateur, premiers étalons.',
            'input_usd' => 5.0,
            'output_usd' => 25.0,
            'cache_hit_usd' => 0.50,
            'cache_min_tokens' => 512,
        ],
    ];

    /**
     * @return list<string>
     */
    public static function ids(): array
    {
        return array_keys(self::MODELS);
    }

    public static function isAllowed(string $id): bool
    {
        return isset(self::MODELS[$id]);
    }

    public static function resolve(?string $id = null): string
    {
        if (is_string($id) && self::isAllowed($id)) {
            return $id;
        }

        return self::DEFAULT;
    }

    /**
     * @return list<array{
     *     id: string,
     *     label: string,
     *     hint: string,
     *     input_usd: float,
     *     output_usd: float,
     *     cache_hit_usd: float,
     *     cache_min_tokens: int
     * }>
     */
    public static function choices(): array
    {
        return array_values(self::MODELS);
    }

    /**
     * Rapport entrée vs Sonnet 5 (estimés historiques de CostEstimator).
     */
    public static function costFactorVersusSonnet(string $id): float
    {
        $sonnet = self::MODELS['claude-sonnet-5']['input_usd'];
        $input = self::MODELS[self::resolve($id)]['input_usd'];

        return $sonnet > 0.0 ? $input / $sonnet : 1.0;
    }
}
