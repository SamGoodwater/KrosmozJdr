<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SpellDegree;
use App\Models\SpellDegreeEffect;
use App\Models\SubEffect;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpellDegreeEffect>
 */
class SpellDegreeEffectFactory extends Factory
{
    protected $model = SpellDegreeEffect::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'spell_degree_id' => SpellDegree::factory(),
            'sub_effect_id' => SubEffect::query()->value('id') ?? SubEffect::query()->create([
                'slug' => 'frapper-factory-'.fake()->unique()->numerify('####'),
                'type_slug' => 'frapper',
                'template_text' => 'Dégâts [value].',
                'variables_allowed' => ['value'],
                'param_schema' => [
                    'action' => 'frapper',
                    'params' => [
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur'],
                    ],
                ],
            ])->id,
            'order' => 0,
            'scope' => 'general',
            'value_min' => null,
            'value_max' => null,
            'dice_num' => null,
            'dice_side' => null,
            'params' => [
                'characteristic' => 'fire',
                'value_formula' => '2d6',
            ],
            'crit_only' => false,
            'duration_formula' => null,
            'logic_group' => null,
            'logic_operator' => null,
            'logic_condition' => null,
        ];
    }
}
