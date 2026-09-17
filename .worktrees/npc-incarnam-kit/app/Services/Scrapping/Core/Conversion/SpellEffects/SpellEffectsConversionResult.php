<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

/**
 * Résultat de la conversion des effets d'un sort DofusDB vers la structure KrosmozJDR.
 * Payloads prêts pour création en BDD : effect_group, effects (avec sub_effects).
 */
final class SpellEffectsConversionResult
{
    /**
     * @param  array{name: string, slug: string}  $effectGroup
     * @param  list<array>  $effects  Chaque entrée : degree, name, slug, description, sub_effects[]
     * @param  array<string, bool|string|null>  $spellResolution  Paramètres de résolution au niveau du sort
     * @param  list<array{level:string,code:string,message:string,context:array<string,mixed>}>  $diagnostics
     */
    public function __construct(
        private readonly array $effectGroup,
        private readonly array $effects,
        private readonly array $spellResolution = [],
        private readonly array $diagnostics = [],
    ) {}

    public function getEffectGroup(): array
    {
        return $this->effectGroup;
    }

    /** @return list<array> */
    public function getEffects(): array
    {
        return $this->effects;
    }

    public function hasEffects(): bool
    {
        return $this->effectGroup !== [] && $this->effects !== [];
    }

    public function getEffectsCount(): int
    {
        return count($this->effects);
    }

    /**
     * @return array<string, bool|string|null>
     */
    public function getSpellResolution(): array
    {
        return $this->spellResolution;
    }

    /** @return list<array{level:string,code:string,message:string,context:array<string,mixed>}> */
    public function getDiagnostics(): array
    {
        return $this->diagnostics;
    }
}
