<?php

declare(strict_types=1);

namespace App\Services\Jdr;

/**
 * Résultat d’un lancer simulé à partir d’une formule de dés.
 */
final readonly class DiceRollResult
{
    /**
     * @param  list<string>  $breakdown  Détail des termes (dés, constantes, tranches).
     */
    public function __construct(
        public bool $isValid,
        public int|float $result,
        public array $breakdown,
        public ?string $error,
    ) {}

    public static function invalid(?string $error = null): self
    {
        return new self(false, 0, [], $error);
    }
}
