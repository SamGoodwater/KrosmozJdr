<?php

namespace Database\Factories\Entity;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specialization>
 */
class SpecializationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specializations = ['Guerrier', 'Mage', 'Archer', 'Voleur', 'Soigneur', 'Tank', 'DPS', 'Support'];
        return [
            'name' => fake()->unique()->randomElement($specializations),
            'description' => fake()->optional()->sentence(),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master', 'admin', 'super_admin']),
            'image' => fake()->optional()->imageUrl(128, 128, 'abstract', true),
            'created_by' => null,
        ];
    }
}
