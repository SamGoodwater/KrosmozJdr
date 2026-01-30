<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Consumable;
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
            'description' => fake()->optional()->sentence(),
            'effect' => fake()->optional()->sentence(),
            'level' => (string) fake()->numberBetween(1, 200),
            'recipe' => fake()->optional()->sentence(),
            'price' => (string) fake()->numberBetween(1, 10000),
            'rarity' => fake()->numberBetween(0, 5),
            'state' => fake()->randomElement([Consumable::STATE_DRAFT, Consumable::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'dofus_version' => '3',
            'image' => fake()->optional()->imageUrl(),
            'auto_update' => fake()->boolean(80),
            'consumable_type_id' => null, // Peut être défini dans le seeder
            'created_by' => User::factory(),
        ];
    }
}
