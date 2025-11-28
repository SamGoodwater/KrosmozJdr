<?php

namespace Database\Factories\Type;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemType>
 */
class ItemTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $itemTypes = ['Épée', 'Bouclier', 'Arc', 'Dague', 'Bâton', 'Hache', 'Marteau', 'Pelle', 'Pioche', 'Baguette'];
        return [
            'name' => fake()->unique()->randomElement($itemTypes),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master', 'admin', 'super_admin']),
            'created_by' => null,
        ];
    }
}
