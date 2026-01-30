<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Resource;
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
            'dofusdb_id' => fake()->optional()->numerify('####'),
            'official_id' => fake()->optional()->numberBetween(1, 10000),
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'level' => (string) fake()->numberBetween(1, 200),
            'price' => (string) fake()->numberBetween(1, 10000),
            'weight' => (string) fake()->numberBetween(1, 100),
            'rarity' => fake()->numberBetween(0, 5),
            'dofus_version' => '3',
            'state' => fake()->randomElement([Resource::STATE_DRAFT, Resource::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => fake()->optional()->imageUrl(),
            'auto_update' => fake()->boolean(80),
            'resource_type_id' => null, // Peut être défini dans le seeder
            'created_by' => User::factory(),
        ];
    }
}
