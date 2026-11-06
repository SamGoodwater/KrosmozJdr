<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Rules\EffectConditionSideFormula;
use App\Support\Spell\EffectConditionParams;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EffectConditionParamsTest extends TestCase
{
    #[Test]
    public function normalize_conditions_keeps_valid_rows_and_drops_empty(): void
    {
        $out = EffectConditionParams::normalizeConditions([
            ['left' => '[agi]', 'op' => '>=', 'right' => '3'],
            ['left' => '', 'op' => '>', 'right' => ''],
            ['join' => 'OR', 'left' => '[intel]', 'op' => '>', 'right' => '[chance:target]'],
            ['left' => '1', 'op' => '??', 'right' => '2'],
        ]);

        $this->assertCount(2, $out);
        $this->assertSame('>=', $out[0]['op']);
        $this->assertSame('OR', $out[1]['join']);
    }

    #[Test]
    public function sanitize_params_defaults_applies_to_and_clears_empty_lists(): void
    {
        $params = EffectConditionParams::sanitizeParams([
            'applies_to' => 'nope',
            'conditions' => [],
            'value_up_to' => true,
            'value_rules' => [
                ['action' => 'add', 'formula' => '1', 'conditions' => [
                    ['left' => '[agi]', 'op' => '>=', 'right' => '3'],
                ]],
            ],
            'value_formula' => '2',
        ]);

        $this->assertSame('targets', $params['applies_to']);
        $this->assertTrue($params['value_up_to']);
        $this->assertArrayNotHasKey('conditions', $params);
        $this->assertCount(1, $params['value_rules']);
        $this->assertSame('add', $params['value_rules'][0]['action']);
    }

    #[Test]
    public function sanitize_params_drops_false_value_up_to(): void
    {
        $params = EffectConditionParams::sanitizeParams([
            'value_up_to' => false,
        ]);
        $this->assertArrayNotHasKey('value_up_to', $params);
    }

    #[Test]
    public function side_formula_accepts_bare_and_suffix_actor(): void
    {
        $v = Validator::make(
            ['left' => '[agi] + 1'],
            ['left' => [new EffectConditionSideFormula]],
        );
        $this->assertTrue($v->passes(), (string) $v->errors());

        $v2 = Validator::make(
            ['left' => '[chance:target]'],
            ['left' => [new EffectConditionSideFormula]],
        );
        $this->assertTrue($v2->passes(), (string) $v2->errors());
    }

    #[Test]
    public function side_formula_accepts_legacy_prefix_actor(): void
    {
        $v = Validator::make(
            ['left' => '[caster:agi] + 1'],
            ['left' => [new EffectConditionSideFormula]],
        );
        $this->assertTrue($v->passes(), (string) $v->errors());
    }

    #[Test]
    public function side_formula_rejects_injection_and_unknown_function(): void
    {
        $v = Validator::make(
            ['left' => 'system("rm")'],
            ['left' => [new EffectConditionSideFormula]],
        );
        $this->assertTrue($v->fails());

        $v2 = Validator::make(
            ['left' => 'eval(1)'],
            ['left' => [new EffectConditionSideFormula]],
        );
        $this->assertTrue($v2->fails());
    }

    #[Test]
    public function side_formula_allows_min_max_with_strong(): void
    {
        $v = Validator::make(
            ['formula' => 'min(3 + [strong], 6)'],
            ['formula' => [new EffectConditionSideFormula]],
        );
        $this->assertTrue($v->passes(), (string) $v->errors());
    }
}
