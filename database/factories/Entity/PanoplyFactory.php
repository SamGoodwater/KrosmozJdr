<?php

namespace Database\Factories\Entity;

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
        
        return [
            'dofusdb_id' => fake()->optional()->numerify('####'),
            'name' => fake()->unique()->randomElement($panoplyNames),
            'description' => fake()->optional()->text(200), // Limité à 200 caractères pour éviter les erreurs de troncature
            'bonus' => fake()->optional()->sentence(),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master']),
            'created_by' => User::factory(),
        ];
    }
}
