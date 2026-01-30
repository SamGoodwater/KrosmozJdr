<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Scenario;
use App\Models\User;
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
            'name' => $name,
            'description' => fake()->optional()->text(200), // Limité à 200 caractères pour éviter les erreurs de troncature
            'slug' => $slug,
            'keyword' => fake()->optional()->word(),
            'is_public' => fake()->boolean(),
            'progress_state' => fake()->numberBetween(0, 3),
            'state' => fake()->randomElement([Scenario::STATE_DRAFT, Scenario::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => fake()->optional()->imageUrl(128, 128, 'abstract', true, $name),
            'created_by' => null,
        ];
    }
}
