<?php

namespace App\Services\Scrapping\Constants;

/**
 * Limites "métier" des entités scrappables.
 *
 * @description
 * Sert de garde-fou pour les modes "tout récupérer" afin d'éviter des imports
 * gigantesques par erreur. Ce n'est pas une vérité absolue DofusDB, mais une
 * limite raisonnable côté KrosmozJDR.
 */
final class EntityLimits
{
    /**
     * @var array<string,int>
     */
    public const LIMITS = [
        'class' => 19,
        'monster' => 5000,
        'item' => 30000,
        'spell' => 20000,
        'panoply' => 1000,
        // Aliases (items DofusDB)
        'resource' => 30000,
        'consumable' => 30000,
        'equipment' => 30000,
    ];

    public static function capFor(string $entity, int $fallback = 20000): int
    {
        $key = strtolower(trim($entity));
        return self::LIMITS[$key] ?? $fallback;
    }
}

