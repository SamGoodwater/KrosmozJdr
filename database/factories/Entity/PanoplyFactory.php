<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Panoply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Panoply>
 */
class PanoplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $panoplyNames = [
            'Panoplie du Bouftou',
            'Panoplie du Tofu',
            'Panoplie du Gobelin',
            'Panoplie du Bwork',
            'Panoplie du Champ Champ',
            'Panoplie du Piou',
            'Panoplie du Bouftou Royal',
            'Panoplie du Tofu Royal',
            'Panoplie du Gobelin Royal',
            'Panoplie du Bwork Royal',
        ];
        
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
            'dofusdb_id' => fake()->optional()->numerify('####'),
            'name' => fake()->unique()->randomElement($panoplyNames),
            'description' => fake()->optional()->text(200), // Limité à 200 caractères pour éviter les erreurs de troncature
            'bonus' => fake()->optional()->sentence(),
            'state' => fake()->randomElement([Panoply::STATE_DRAFT, Panoply::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'created_by' => User::factory(),
        ];
    }
}
