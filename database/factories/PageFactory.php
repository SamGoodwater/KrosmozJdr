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
            'is_visible' => $this->faker->randomElement([
                Visibility::GUEST->value,
                Visibility::USER->value,
                Visibility::GAME_MASTER->value,
                Visibility::ADMIN->value,
            ]),
            'can_edit_role' => $this->faker->randomElement([
                Visibility::ADMIN->value,
                Visibility::GAME_MASTER->value,
            ]),
            'in_menu' => $this->faker->boolean(70),
            'state' => $this->faker->randomElement([
                PageState::DRAFT->value,
                PageState::PREVIEW->value,
                PageState::PUBLISHED->value,
                PageState::ARCHIVED->value,
            ]),
            'parent_id' => null, // Géré dans le seeder pour la hiérarchie
            'menu_order' => $this->faker->numberBetween(1, 20),
            'created_by' => null, // Géré dans le seeder
        ];
    }
}
