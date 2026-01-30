<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Page;
use App\Models\User;

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
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            // Défaults déterministes pour éviter des tests non reproductibles.
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'in_menu' => true,
            'state' => Page::STATE_DRAFT,
            'parent_id' => null, // Géré dans le seeder pour la hiérarchie
            'menu_order' => 0,
            'created_by' => null, // Géré dans le seeder
        ];
    }
}
