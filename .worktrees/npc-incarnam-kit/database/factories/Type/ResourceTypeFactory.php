<?php

namespace Database\Factories\Type;

use App\Models\Type\ResourceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ResourceType>
 */
class ResourceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resourceTypes = ['Bois', 'Pierre', 'Métal', 'Cuir', 'Laine', 'Plante', 'Minerai', 'Fragment'];

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
            'name' => fake()->unique()->randomElement($resourceTypes),
            'state' => fake()->randomElement([ResourceType::STATE_DRAFT, ResourceType::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'created_by' => null,
            'show_in_catalog' => false,
            'allow_scrap' => false,
        ];
    }
}
