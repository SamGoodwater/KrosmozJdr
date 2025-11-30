<?php

namespace Database\Factories\Entity;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Classe>
 */
class ClasseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classes = ['Iop', 'Cra', 'Féca', 'Ecaflip', 'Eniripsa', 'Sram', 'Xélor', 'Pandawa', 'Roublard', 'Zobal', 'Steamer', 'Osamodas', 'Sacrieur', 'Ouginak', 'Foggernaut', 'Eliotrope', 'Huppermage', 'Osa', 'Sram', 'Enutrof'];
        
        return [
            'official_id' => fake()->optional()->numerify('####'),
            'dofusdb_id' => fake()->optional()->numerify('####'),
            'name' => fake()->unique()->randomElement($classes),
            'description_fast' => fake()->optional()->sentence(),
            'description' => fake()->optional()->text(200), // Limité à 200 caractères pour éviter les erreurs de troncature
            'life' => (string) fake()->numberBetween(30, 100),
            'life_dice' => fake()->randomElement(['1d6', '1d8', '1d10', '1d12']),
            'specificity' => fake()->randomElement(['Force', 'Intelligence', 'Agilité', 'Chance', 'Sagesse']),
            'dofus_version' => '3',
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master']),
            'image' => fake()->optional()->imageUrl(),
            'icon' => fake()->optional()->imageUrl(),
            'auto_update' => fake()->boolean(80),
            'created_by' => User::factory(),
        ];
    }
}
