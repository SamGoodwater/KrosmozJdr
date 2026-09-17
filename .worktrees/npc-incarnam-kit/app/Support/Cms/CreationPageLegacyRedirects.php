<?php

declare(strict_types=1);

namespace App\Support\Cms;

/**
 * Anciens slugs de chartes « contribution-* » redirigés vers l’atelier Création.
 */
final class CreationPageLegacyRedirects
{
    /**
     * @var array<string, string>
     */
    public const MAP = [
        'contribution-creatures' => 'creation-monstres',
        'contribution-objets' => 'creation-equipements',
        'contribution-sorts' => 'creation-sorts',
    ];

    /**
     * Expression `where` pour la route d’alias.
     */
    public static function routePattern(): string
    {
        return implode('|', array_keys(self::MAP));
    }
}
