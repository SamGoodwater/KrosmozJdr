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
            'reuse' => $this->faker->randomElement([
                'Illimitée',
                'Après un repos court',
                'Après un repos long',
                '1 fois par scène',
            ]),
            'duration' => $this->faker->randomElement([
                'Permanente',
                'Instantanée',
                'Tant qu’il y a une foule',
                '1 minute',
            ]),
            'range' => $this->faker->randomElement([
                'Soi',
                '9 m',
                'À portée d’ouïe',
                'Créatures qui te voient',
            ]),
            'state' => $this->faker->randomElement([Aptitude::STATE_DRAFT, Aptitude::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => null,
            'created_by' => null,
        ];
    }
}
