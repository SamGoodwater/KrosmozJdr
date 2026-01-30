<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Item;
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
        $levels = [
            User::ROLE_GUEST,
            User::ROLE_USER,
            User::ROLE_PLAYER,
            User::ROLE_GAME_MASTER,
            User::ROLE_ADMIN,
            User::ROLE_SUPER_ADMIN,
        ];
        $readLevel = fake()->randomElement($levels);
        $writeLevel = fake()->randomElement(array_values(array_filter($levels, fn (int $lvl) => $lvl >= $readLevel)));

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
            'state' => fake()->randomElement([Item::STATE_DRAFT, Item::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => fake()->optional()->imageUrl(),
            'auto_update' => fake()->boolean(80),
            'item_type_id' => null, // Peut être défini dans le seeder
            'created_by' => User::factory(),
        ];
    }
}
