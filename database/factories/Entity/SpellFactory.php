<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Spell>
 */
class SpellFactory extends Factory
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
            'description' => fake()->sentence(),
            'effect' => fake()->optional()->sentence(),
            'area' => fake()->numberBetween(0, 10),
            'level' => (string) fake()->numberBetween(1, 200),
            'po' => (string) fake()->numberBetween(1, 20),
            'po_editable' => fake()->boolean(),
            'pa' => (string) fake()->numberBetween(1, 12),
            'cast_per_turn' => (string) fake()->numberBetween(1, 5),
            'cast_per_target' => (string) fake()->numberBetween(0, 3),
            'sight_line' => fake()->boolean(70),
            'number_between_two_cast' => (string) fake()->numberBetween(0, 5),
            'number_between_two_cast_editable' => fake()->boolean(),
            'element' => fake()->numberBetween(0, 29),
            'category' => fake()->numberBetween(0, 10),
            'is_magic' => fake()->boolean(80),
            'powerful' => fake()->numberBetween(0, 10),
            'state' => fake()->randomElement([Spell::STATE_DRAFT, Spell::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => fake()->optional()->imageUrl(),
            'auto_update' => fake()->boolean(80),
            'created_by' => User::factory(),
        ];
    }
}
