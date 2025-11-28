<?php

namespace Database\Factories\Entity;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Scenario>
 */
class ScenarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $slug = \Illuminate\Support\Str::slug($name . '-' . fake()->unique()->randomNumber(3));
        return [
            'name' => $name,
            'description' => fake()->optional()->paragraph(),
            'slug' => $slug,
            'keyword' => fake()->optional()->word(),
            'is_public' => fake()->boolean(),
            'state' => fake()->numberBetween(0, 3),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master', 'admin', 'super_admin']),
            'image' => fake()->optional()->imageUrl(128, 128, 'abstract', true, $name),
            'created_by' => null,
        ];
    }
}
