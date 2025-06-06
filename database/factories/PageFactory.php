<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $states = array_keys(\App\Models\Page::STATES);
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'is_visible' => $this->faker->boolean(90),
            'in_menu' => $this->faker->boolean(70),
            'state' => $this->faker->randomElement($states),
            'parent_id' => null, // Géré dans le seeder pour la hiérarchie
            'menu_order' => $this->faker->numberBetween(1, 20),
            'created_by' => null, // Géré dans le seeder
        ];
    }
}
