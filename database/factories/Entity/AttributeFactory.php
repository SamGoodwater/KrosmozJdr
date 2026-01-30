<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Attribute;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = ['Force', 'Intelligence', 'Agilité', 'Chance', 'Sagesse', 'Vitalité'];
        $name = $this->faker->unique()->randomElement($names);

        $levels = [
            User::ROLE_GUEST,
            User::ROLE_USER,
            User::ROLE_PLAYER,
            User::ROLE_GAME_MASTER,
            User::ROLE_ADMIN,
            User::ROLE_SUPER_ADMIN,
        ];
        $readLevel = $this->faker->randomElement($levels);
        $writeLevel = $this->faker->randomElement(array_values(array_filter($levels, fn (int $lvl) => $lvl >= $readLevel)));

        return [
            'name' => $name,
            'description' => $this->faker->optional()->realText(60),
            'state' => $this->faker->randomElement([Attribute::STATE_DRAFT, Attribute::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => $this->faker->optional()->imageUrl(128, 128, 'abstract', true, $name),
            'created_by' => null,
        ];
    }
}
