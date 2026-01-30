<?php

namespace Database\Factories\Type;

use App\Models\Type\ConsumableType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsumableType>
 */
class ConsumableTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $consumableTypes = ['Potion', 'Parchemin', 'Pain', 'Viande', 'Poisson', 'Fruit', 'Légume', 'Boisson'];

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
            'name' => fake()->unique()->randomElement($consumableTypes),
            'state' => fake()->randomElement([ConsumableType::STATE_DRAFT, ConsumableType::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'created_by' => null,
        ];
    }
}
