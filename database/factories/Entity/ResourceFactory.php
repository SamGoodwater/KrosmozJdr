<?php

namespace Database\Factories\Entity;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dofusdb_id' => fake()->optional()->numerify('####'),
            'official_id' => fake()->optional()->numberBetween(1, 10000),
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'level' => (string) fake()->numberBetween(1, 200),
            'price' => (string) fake()->numberBetween(1, 10000),
            'weight' => (string) fake()->numberBetween(1, 100),
            'rarity' => fake()->numberBetween(0, 5),
            'dofus_version' => '3',
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master']),
            'image' => fake()->optional()->imageUrl(),
            'auto_update' => fake()->boolean(80),
            'resource_type_id' => null, // Peut être défini dans le seeder
            'created_by' => User::factory(),
        ];
    }
}
