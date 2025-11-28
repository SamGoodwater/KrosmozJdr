<?php

namespace Database\Factories\Entity;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Capability>
 */
class CapabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'effect' => fake()->optional()->sentence(),
            'level' => (string) fake()->numberBetween(1, 200),
            'pa' => (string) fake()->numberBetween(1, 10),
            'po' => (string) fake()->numberBetween(1, 20),
            'po_editable' => fake()->boolean(),
            'time_before_use_again' => (string) fake()->numberBetween(0, 10),
            'casting_time' => (string) fake()->numberBetween(0, 5),
            'duration' => (string) fake()->numberBetween(1, 10),
            'element' => fake()->randomElement(['Neutre', 'Terre', 'Feu', 'Air', 'Eau']),
            'is_magic' => fake()->boolean(),
            'ritual_available' => fake()->boolean(),
            'powerful' => fake()->optional()->sentence(),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master', 'admin', 'super_admin']),
            'image' => fake()->optional()->imageUrl(128, 128, 'abstract', true),
            'created_by' => null,
        ];
    }
}
