<?php

declare(strict_types=1);

namespace App\Services\Jdr;

/**
 * Résultat d’analyse d’une formule de dés (min / max / moyenne, sans lancer).
 */
final readonly class DiceFormulaAnalysis
{
    /**
     * @param  list<string>  $rangeEquivalents  Équivalents « [2-6] = 1d5+1 ».
     */
    public function __construct(
        public bool $isValid,
        public bool $isRecognized,
        public int|float|null $min,
        public int|float|null $max,
        public int|float|null $average,
        public array $rangeEquivalents,
        public ?string $error,
    ) {}

    public static function empty(): self
    {
        return new self(false, false, null, null, null, [], null);
    }

    public static function invalid(?string $error = null): self
    {
        return new self(false, false, null, null, null, [], $error);
    }
}
