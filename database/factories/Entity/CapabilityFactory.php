<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Capability;
use App\Models\User;
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
            'state' => fake()->randomElement([Capability::STATE_DRAFT, Capability::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => fake()->optional()->imageUrl(128, 128, 'abstract', true),
            'created_by' => null,
        ];
    }
}
