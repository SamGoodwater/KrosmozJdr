<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Specialization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specialization>
 */
class SpecializationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specializations = ['Guerrier', 'Mage', 'Archer', 'Voleur', 'Soigneur', 'Tank', 'DPS', 'Support'];

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
            'name' => fake()->unique()->randomElement($specializations),
            'description' => fake()->optional()->sentence(),
            'state' => fake()->randomElement([Specialization::STATE_DRAFT, Specialization::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => fake()->optional()->imageUrl(128, 128, 'abstract', true),
            'created_by' => null,
        ];
    }
}
