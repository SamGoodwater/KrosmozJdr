<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\PageState;
use App\Enums\Visibility;

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
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'in_menu' => true,
            'state' => PageState::DRAFT->value,
            'parent_id' => null, // Géré dans le seeder pour la hiérarchie
            'menu_order' => 0,
            'created_by' => null, // Géré dans le seeder
        ];
    }
}
