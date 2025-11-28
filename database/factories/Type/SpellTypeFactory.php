<?php

namespace Database\Factories\Type;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpellType>
 */
class SpellTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $spellTypes = ['Offensif', 'Défensif', 'Soin', 'Buff', 'Debuff', 'Invocation', 'Téléportation', 'Transformation'];
        return [
            'name' => fake()->unique()->randomElement($spellTypes),
            'description' => fake()->optional()->sentence(),
            'color' => fake()->hexColor(),
            'icon' => fake()->optional()->word(),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master', 'admin', 'super_admin']),
            'created_by' => null,
        ];
    }
}
