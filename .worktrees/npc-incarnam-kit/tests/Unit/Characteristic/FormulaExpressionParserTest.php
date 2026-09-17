<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Formula\FormulaExpressionParser;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use PHPUnit\Framework\TestCase;

/**
 * Grammaire des valeurs saisies : nombre, formule {..}, arrondis, domaines.
 */
class FormulaExpressionParserTest extends TestCase
{
    private FormulaExpressionParser $parser;

    protected function setUp(): void
    {
        $this->parser = new FormulaExpressionParser(new SafeExpressionEvaluator);
    }

    public function test_it_reads_plain_numbers(): void
    {
        $this->assertSame(12.0, $this->parser->evaluate('12'));
        $this->assertSame(-3.0, $this->parser->evaluate('-3'));
        $this->assertSame(2.5, $this->parser->evaluate('2.5'));
        $this->assertNull($this->parser->evaluate(''));
        $this->assertNull($this->parser->evaluate(null));
    }

    public function test_rounding_suffix_drives_the_result(): void
    {
        $this->assertSame(3.0, $this->parser->evaluate('{[niveau] / 3}+', ['level' => 7]));
        $this->assertSame(2.0, $this->parser->evaluate('{[niveau] / 3}-', ['level' => 7]));
        $this->assertSame(2.0, $this->parser->evaluate('{[niveau] / 3}', ['level' => 7]));
    }

    public function test_unknown_identifiers_resolve_to_zero(): void
    {
        $this->assertSame(4.0, $this->parser->evaluate('{[unknown_key] + 4}'));
    }

    public function test_french_aliases_resolve_to_canonical_keys(): void
    {
        $this->assertSame(8.0, $this->parser->evaluate('{[niveau] * 2}', ['level' => 4]));
        $this->assertSame(['level', 'vitality'], $this->parser->canonicalIdentifiers('{[niveau] + [vitalite]}'));
    }

    public function test_real_variable_wins_over_alias(): void
    {
        $this->assertSame(
            5.0,
            $this->parser->evaluate('{[niveau]}', ['niveau' => 5, 'level' => 99])
        );
    }

    public function test_it_enumerates_range_and_dice_domains(): void
    {
        $this->assertSame([9.0, 10.0, 11.0, 12.0], $this->parser->enumerateOutcomes('{8 + [1d4]}'));
        $this->assertSame([9.0, 10.0, 11.0, 12.0], $this->parser->enumerateOutcomes('{1d4 + 8}'));
        $this->assertSame([5.0, 6.0, 7.0, 8.0], $this->parser->enumerateOutcomes('{[5-8]}'));
        $this->assertSame([10.0, 12.0, 14.0, 16.0], $this->parser->enumerateOutcomes('{[5-8] * 2}'));
    }

    public function test_enumeration_without_domain_returns_single_value(): void
    {
        $this->assertSame([3.0], $this->parser->enumerateOutcomes('{[niveau]}', ['level' => 3]));
        $this->assertSame([12.0], $this->parser->enumerateOutcomes('12'));
        $this->assertSame([], $this->parser->enumerateOutcomes(''));
    }

    public function test_enumeration_is_capped(): void
    {
        $outcomes = $this->parser->enumerateOutcomes('{[1-100]}', [], 20);

        $this->assertCount(20, $outcomes);
        $this->assertSame(1.0, $outcomes[0]);
        $this->assertSame(100.0, $outcomes[19]);
    }

    public function test_validation_requires_braces_and_known_suffix(): void
    {
        $this->assertSame([], $this->parser->validate('{[niveau] / 3}+'));
        $this->assertSame([], $this->parser->validate('12'));
        $this->assertNotSame([], $this->parser->validate('[niveau] / 3'));
        $this->assertNotSame([], $this->parser->validate('{[niveau] / 3}*'));
        $this->assertNotSame([], $this->parser->validate('{[niveau] / 3'));
        $this->assertNotSame([], $this->parser->validate('{foo(1)}'));
    }

    public function test_domains_are_rejected_unless_explicitly_allowed(): void
    {
        $this->assertNotSame([], $this->parser->validate('{[5-8]}'));
        $this->assertSame([], $this->parser->validate('{[5-8]}', true));
    }

    public function test_validation_can_reject_unknown_identifiers(): void
    {
        $this->assertSame([], $this->parser->validate('{[niveau]}', false, ['level']));
        $this->assertNotSame([], $this->parser->validate('{[nope]}', false, ['level']));
    }

    public function test_display_substitution_keeps_the_shape_of_the_formula(): void
    {
        $this->assertSame('4 * 2', $this->parser->substituteForDisplay('{[niveau] * 2}', ['level' => 4]));
        $this->assertNull($this->parser->substituteForDisplay('12'));
    }

    public function test_dice_modes_are_deterministic(): void
    {
        $evaluator = new SafeExpressionEvaluator;

        $this->assertSame(2.0, $evaluator->evaluate('2d6', SafeExpressionEvaluator::DICE_MODE_MIN));
        $this->assertSame(12.0, $evaluator->evaluate('2d6', SafeExpressionEvaluator::DICE_MODE_MAX));
        $this->assertSame(7.0, $evaluator->evaluate('2d6', SafeExpressionEvaluator::DICE_MODE_AVERAGE));
    }
}
