<?php

namespace Database\Factories\Type;

use App\Models\Type\MonsterRace;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonsterRace>
 */
class MonsterRaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $monsterRaces = ['Bouftou', 'Tofu', 'Gobelin', 'Bwork', 'Champ Champ', 'Piou', 'Arakne', 'Cochon de Lait'];

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
            'name' => fake()->unique()->randomElement($monsterRaces),
            'state' => fake()->randomElement([MonsterRace::STATE_DRAFT, MonsterRace::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'created_by' => null,
            'id_super_race' => null,
        ];
    }
}
