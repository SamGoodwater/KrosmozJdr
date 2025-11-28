<?php

namespace Database\Factories\Type;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsumableType>
 */
class ConsumableTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $consumableTypes = ['Potion', 'Parchemin', 'Pain', 'Viande', 'Poisson', 'Fruit', 'Légume', 'Boisson'];
        return [
            'name' => fake()->unique()->randomElement($consumableTypes),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master', 'admin', 'super_admin']),
            'created_by' => null,
        ];
    }
}
