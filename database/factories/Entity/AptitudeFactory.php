<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Aptitude;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Entity\Aptitude>
 */
class AptitudeFactory extends Factory
{
    protected $model = Aptitude::class;

    /**
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
        $readLevel = $this->faker->randomElement($levels);
        $writeLevel = $this->faker->randomElement(array_values(array_filter($levels, fn (int $lvl) => $lvl >= $readLevel)));

        return [
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'effect' => $this->faker->sentence(12),
            'reuse_type' => Aptitude::REUSE_UNLIMITED,
            'reuse_amount' => null,
            'reuse_unit' => null,
            'duration_type' => Aptitude::DURATION_WHILE_CONDITION,
            'duration_amount' => null,
            'duration_unit' => null,
            'duration_note' => $this->faker->optional()->sentence(4),
            'range_type' => Aptitude::RANGE_SENSE,
            'range_amount' => null,
            'range_unit' => null,
            'range_sense' => 'hearing',
            'range_note' => 'Tant qu’on t’entend',
            'state' => $this->faker->randomElement([Aptitude::STATE_DRAFT, Aptitude::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => null,
            'created_by' => null,
        ];
    }
}
