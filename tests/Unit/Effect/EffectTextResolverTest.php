<?php

declare(strict_types=1);

namespace Tests\Unit\Effect;

use App\Services\Effect\EffectTextResolver;
use PHPUnit\Framework\TestCase;

/**
 * @see App\Services\Effect\EffectTextResolver
 */
class EffectTextResolverTest extends TestCase
{
    private EffectTextResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new EffectTextResolver();
    }

    public function test_resolve_effect_text_replaces_variables(): void
    {
        $template = 'Inflige [value] dégâts [element].';
        $context = ['value' => 15, 'element' => 'Terre'];
        $this->assertSame('Inflige 15 dégâts Terre.', $this->resolver->resolveEffectText($template, $context));
    }

    public function test_resolve_effect_text_keeps_unknown_variables(): void
    {
        $template = '[value] et [missing]';
        $context = ['value' => 10];
        $this->assertSame('10 et [missing]', $this->resolver->resolveEffectText($template, $context));
    }

    public function test_format_dice_returns_notation_unchanged_when_not_human(): void
    {
        $this->assertSame('2d6', $this->resolver->formatDice('2d6', false));
    }

    public function test_format_dice_human_readable(): void
    {
        $this->assertSame('2 dés à 6 faces', $this->resolver->formatDice('2d6', true));
        $this->assertSame('1 dé à 20 faces', $this->resolver->formatDice('1d20', true));
    }

    public function test_format_dice_in_text(): void
    {
        $text = 'Jet 2d6 + [agi]';
        $this->assertSame('Jet 2d6 + [agi]', $this->resolver->formatDiceInText($text, false));
        $this->assertSame('Jet 2 dés à 6 faces + [agi]', $this->resolver->formatDiceInText($text, true));
    }
}
