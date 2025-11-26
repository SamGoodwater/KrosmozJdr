<?php

namespace Database\Factories\Entity;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Item>
 */
class ItemFactory extends Factory
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
            'level' => (string) fake()->numberBetween(1, 200),
            'description' => fake()->optional()->sentence(),
            'effect' => fake()->optional()->sentence(),
            'bonus' => fake()->optional()->sentence(),
            'recipe' => fake()->optional()->sentence(),
            'price' => (string) fake()->numberBetween(1, 100000),
            'rarity' => fake()->numberBetween(0, 5),
            'dofus_version' => '3',
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master']),
            'image' => fake()->optional()->imageUrl(),
            'auto_update' => fake()->boolean(80),
            'item_type_id' => null, // Peut être défini dans le seeder
            'created_by' => User::factory(),
        ];
    }
}
