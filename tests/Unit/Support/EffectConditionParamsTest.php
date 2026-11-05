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
            ['left' => '[caster:agi]', 'op' => '>=', 'right' => '3'],
            ['left' => '', 'op' => '>', 'right' => ''],
            ['join' => 'OR', 'left' => '[caster:intel]', 'op' => '>', 'right' => '[target:chance]'],
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
            'value_rules' => [
                ['action' => 'add', 'formula' => '1', 'conditions' => [
                    ['left' => '[caster:agi]', 'op' => '>=', 'right' => '3'],
                ]],
            ],
            'value_formula' => '2',
        ]);

        $this->assertSame('targets', $params['applies_to']);
        $this->assertArrayNotHasKey('conditions', $params);
        $this->assertCount(1, $params['value_rules']);
        $this->assertSame('add', $params['value_rules'][0]['action']);
    }

    #[Test]
    public function side_formula_accepts_prefixed_characteristic(): void
    {
        $v = Validator::make(
            ['left' => '[caster:agi] + 1'],
            ['left' => [new EffectConditionSideFormula(requireActorPrefix: true)]],
        );
        $this->assertTrue($v->passes(), (string) $v->errors());
    }

    #[Test]
    public function side_formula_rejects_missing_prefix_when_required(): void
    {
        $v = Validator::make(
            ['left' => '[agi] + 1'],
            ['left' => [new EffectConditionSideFormula(requireActorPrefix: true)]],
        );
        $this->assertTrue($v->fails());
    }

    #[Test]
    public function side_formula_rejects_injection_and_unknown_function(): void
    {
        $v = Validator::make(
            ['left' => 'system("rm")'],
            ['left' => [new EffectConditionSideFormula(requireActorPrefix: false)]],
        );
        $this->assertTrue($v->fails());

        $v2 = Validator::make(
            ['left' => 'eval(1)'],
            ['left' => [new EffectConditionSideFormula(requireActorPrefix: false)]],
        );
        $this->assertTrue($v2->fails());
    }

    #[Test]
    public function side_formula_allows_bare_keys_for_value_rules(): void
    {
        $v = Validator::make(
            ['formula' => 'min(3 + [strong], 6)'],
            ['formula' => [new EffectConditionSideFormula(requireActorPrefix: false)]],
        );
        $this->assertTrue($v->passes(), (string) $v->errors());
    }
}
