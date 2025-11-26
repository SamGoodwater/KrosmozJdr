<?php

namespace Database\Factories\Entity;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Consumable>
 */
class ConsumableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'official_id' => fake()->optional()->numerify('####'),
            'dofusdb_id' => fake()->optional()->numerify('####'),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'effect' => fake()->optional()->sentence(),
            'level' => (string) fake()->numberBetween(1, 200),
            'recipe' => fake()->optional()->sentence(),
            'price' => (string) fake()->numberBetween(1, 10000),
            'rarity' => fake()->numberBetween(0, 5),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master']),
            'dofus_version' => '3',
            'image' => fake()->optional()->imageUrl(),
            'auto_update' => fake()->boolean(80),
            'consumable_type_id' => null, // Peut être défini dans le seeder
            'created_by' => User::factory(),
        ];
    }
}
