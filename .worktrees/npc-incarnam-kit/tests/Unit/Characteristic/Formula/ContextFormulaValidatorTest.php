<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic\Formula;

use App\Services\Characteristic\Formula\FormulaExpressionParser;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use PHPUnit\Framework\TestCase;

/**
 * Règles de validation contextuelle (domaines interdits, cycles) via le parser.
 * ContextFormulaValidator délègue surtout à FormulaExpressionParser + whitelist BDD.
 */
class ContextFormulaValidatorTest extends TestCase
{
    private FormulaExpressionParser $parser;

    protected function setUp(): void
    {
        $this->parser = new FormulaExpressionParser(new SafeExpressionEvaluator);
    }

    public function test_context_rejects_domains_unless_allowed(): void
    {
        $this->assertNotSame([], $this->parser->validate('{[5-8]}', false));
        $this->assertSame([], $this->parser->validate('{[niveau] / 3}+', false, ['level']));
    }

    public function test_self_reference_is_detectable_via_canonical_identifiers(): void
    {
        $ids = $this->parser->canonicalIdentifiers('{[armor_class_creature] + 1}');
        $this->assertContains('armor_class_creature', $ids);
    }
}
