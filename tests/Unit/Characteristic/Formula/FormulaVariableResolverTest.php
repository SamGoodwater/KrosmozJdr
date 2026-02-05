<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic\Formula;

use App\Services\Characteristic\Formula\FormulaVariableResolver;
use Tests\TestCase;

/**
 * Tests pour FormulaVariableResolver (alias noms courts dans les formules).
 *
 * @see App\Services\Characteristic\Formula\FormulaVariableResolver
 */
class FormulaVariableResolverTest extends TestCase
{
    public function test_with_short_names_adds_short_aliases_for_creature(): void
    {
        $full = ['level_creature' => 5, 'life_creature' => 100];
        $result = FormulaVariableResolver::withShortNames('creature', $full);
        $this->assertSame(5, $result['level_creature']);
        $this->assertSame(100, $result['life_creature']);
        $this->assertSame(5, $result['level']);
        $this->assertSame(100, $result['life']);
    }

    public function test_with_short_names_does_not_overwrite_existing_short_key(): void
    {
        $full = ['level_creature' => 5, 'level' => 99];
        $result = FormulaVariableResolver::withShortNames('creature', $full);
        $this->assertSame(99, $result['level']);
    }

    public function test_with_short_names_object(): void
    {
        $full = ['level_object' => 10, 'rarity_object' => 2];
        $result = FormulaVariableResolver::withShortNames('object', $full);
        $this->assertSame(10, $result['level']);
        $this->assertSame(2, $result['rarity']);
    }

    public function test_key_to_short_name_strips_suffix(): void
    {
        $this->assertSame('level', FormulaVariableResolver::keyToShortName('level_creature'));
        $this->assertSame('life', FormulaVariableResolver::keyToShortName('life_object'));
        $this->assertSame('pa', FormulaVariableResolver::keyToShortName('pa_spell'));
    }

    public function test_key_to_short_name_returns_key_when_no_suffix(): void
    {
        $this->assertSame('custom_key', FormulaVariableResolver::keyToShortName('custom_key'));
    }
}
