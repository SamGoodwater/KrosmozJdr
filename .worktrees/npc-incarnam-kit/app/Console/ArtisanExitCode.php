<?php

declare(strict_types=1);

namespace App\Console;

/**
 * Codes de sortie des commandes Artisan (alignés sur Symfony Console).
 *
 * @description
 * Utiliser ces constantes à la place de `self::SUCCESS` / `self::FAILURE` sur les classes
 * qui étendent `Illuminate\Console\Command`, pour que l’IDE (Intelephense) résolve les types
 * sans ambiguïté sur l’héritage Symfony.
 *
 * @example
 * return ArtisanExitCode::SUCCESS;
 */
final class ArtisanExitCode
{
    public const SUCCESS = 0;

    public const FAILURE = 1;

    private function __construct() {}
}
