<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Entity\Spell;
use App\Models\SpellDegree;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpellDegree>
 */
class SpellDegreeFactory extends Factory
{
    protected $model = SpellDegree::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'spell_id' => Spell::factory(),
            'position' => 1,
            'required_level' => 1,
            'inherits_effects' => false,
            'properties_source' => SpellDegree::PROPERTIES_SOURCE_OWN,
            'pa' => '3',
            'po_min' => '1',
            'po_max' => '6',
            'po_editable' => true,
            'sight_line' => true,
            'cast_in_line' => false,
            'cast_in_diagonal' => false,
            'area' => 'point',
            'cast_per_turn' => '1',
            'cast_per_target' => '0',
            'number_between_two_cast' => '0',
            'global_cooldown' => 0,
            'max_stack' => 0,
            'duration' => null,
            'allows_reaction' => false,
            'casting_time' => null,
            'resolution_mode' => null,
            'attack_characteristic_key' => null,
            'save_characteristic_key' => null,
            'save_dc_formula' => null,
            'save_success_note' => null,
            'auto_success_if_willing_target' => null,
        ];
    }

    public function inheriting(): static
    {
        return $this->state(fn () => ['inherits_effects' => true]);
    }
}
