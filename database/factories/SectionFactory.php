<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\PageState;
use App\Enums\Visibility;
use App\Enums\SectionType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Section>
 */
class SectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $template = $this->faker->randomElement([
            SectionType::TEXT->value,
            SectionType::IMAGE->value,
            SectionType::GALLERY->value,
            SectionType::VIDEO->value,
            SectionType::ENTITY_TABLE->value,
        ]);

        // Générer des settings et data selon le template
        [$settings, $data] = match($template) {
            SectionType::TEXT->value => [
                ['align' => $this->faker->randomElement(['left', 'center', 'right']), 'size' => $this->faker->randomElement(['sm', 'md', 'lg', 'xl'])],
                ['content' => $this->faker->paragraph()],
            ],
            SectionType::IMAGE->value => [
                ['align' => $this->faker->randomElement(['left', 'center', 'right']), 'size' => $this->faker->randomElement(['sm', 'md', 'lg', 'xl', 'full'])],
                ['src' => $this->faker->imageUrl(), 'alt' => $this->faker->sentence(3), 'caption' => $this->faker->optional()->sentence()],
            ],
            SectionType::GALLERY->value => [
                ['columns' => $this->faker->randomElement([2, 3, 4]), 'gap' => $this->faker->randomElement(['sm', 'md', 'lg'])],
                ['images' => []],
            ],
            SectionType::VIDEO->value => [
                ['autoplay' => $this->faker->boolean(20), 'controls' => $this->faker->boolean(80)],
                ['src' => $this->faker->url(), 'type' => $this->faker->randomElement(['youtube', 'vimeo', 'direct'])],
            ],
            SectionType::ENTITY_TABLE->value => [
                [],
                ['entity' => $this->faker->randomElement(['item', 'creature', 'spell']), 'filters' => [], 'columns' => []],
            ],
            default => [[], ['content' => $this->faker->paragraph()]],
        };

        return [
            'page_id' => null, // Géré dans le seeder
            'title' => $this->faker->optional()->sentence(3),
            'slug' => $this->faker->optional()->slug(),
            'order' => $this->faker->numberBetween(1, 10),
            'template' => $template,
            'settings' => $settings,
            'data' => $data,
            'is_visible' => $this->faker->randomElement([
                Visibility::GUEST->value,
                Visibility::USER->value,
                Visibility::GAME_MASTER->value,
                Visibility::ADMIN->value,
            ]),
            'can_edit_role' => $this->faker->randomElement([
                Visibility::GUEST->value,
                Visibility::USER->value,
                Visibility::GAME_MASTER->value,
                Visibility::ADMIN->value,
            ]),
            'state' => $this->faker->randomElement([
                PageState::DRAFT->value,
                PageState::PREVIEW->value,
                PageState::PUBLISHED->value,
                PageState::ARCHIVED->value,
            ]),
            'created_by' => null, // Géré dans le seeder
        ];
    }
}
