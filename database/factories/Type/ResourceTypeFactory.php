<?php

namespace Database\Factories\Type;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResourceType>
 */
class ResourceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resourceTypes = ['Bois', 'Pierre', 'Métal', 'Cuir', 'Laine', 'Plante', 'Minerai', 'Fragment'];
        return [
            'name' => fake()->unique()->randomElement($resourceTypes),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master', 'admin', 'super_admin']),
            'created_by' => null,
        ];
    }
}
