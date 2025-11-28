<?php

namespace Database\Factories\Entity;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Npc>
 */
class NpcFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'creature_id' => null, // Doit être fourni lors de la création
            'story' => fake()->optional()->paragraph(),
            'historical' => fake()->optional()->paragraph(),
            'age' => fake()->optional()->numberBetween(18, 200),
            'size' => fake()->optional()->numberBetween(100, 250),
            'classe_id' => null,
            'specialization_id' => null,
        ];
    }
}
