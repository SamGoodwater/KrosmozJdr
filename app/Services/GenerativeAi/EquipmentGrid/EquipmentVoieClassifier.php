<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\EquipmentGrid;

/**
 * Choisit la voie (Terre / Feu / Eau / Air, éventuellement Neutre) d’un dictionnaire de bonus.
 *
 * @example
 * $classifier->classify(['strength' => 40, 'vitality' => 10], $definition); // 'terre'
 */
final class EquipmentVoieClassifier
{
    public function __construct(
        private readonly EquipmentGridDefinition $definition,
    ) {}

    /**
     * @param  array<string, float|int>  $bonuses
     */
    public function classify(array $bonuses): ?string
    {
        $scores = [];
        foreach ($this->definition->voies() as $voieKey => $voie) {
            $score = 0.0;
            foreach ($voie['keys'] as $key) {
                $score += abs((float) ($bonuses[$key] ?? 0));
            }
            $scores[$voieKey] = $score;
        }

        $active = $this->definition->activeVoieKeys();
        $bestKey = null;
        $bestScore = 0.0;
        foreach ($active as $voieKey) {
            $score = $scores[$voieKey] ?? 0.0;
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestKey = $voieKey;
            }
        }

        if ($bestKey !== null && $bestScore > 0.0) {
            return $bestKey;
        }

        if ($this->definition->includeNeutral && ($scores['neutre'] ?? 0.0) > 0.0) {
            return 'neutre';
        }

        return null;
    }

    /**
     * Score de la voie sur les bonus (pour départager des représentants).
     *
     * @param  array<string, float|int>  $bonuses
     */
    public function score(array $bonuses, string $voieKey): float
    {
        $voie = $this->definition->voie($voieKey);
        if ($voie === null) {
            return 0.0;
        }
        $score = 0.0;
        foreach ($voie['keys'] as $key) {
            $score += abs((float) ($bonuses[$key] ?? 0));
        }

        return $score;
    }
}
