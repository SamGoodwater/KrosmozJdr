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
        $type = $this->faker->randomElement([
            SectionType::TEXT->value,
            SectionType::IMAGE->value,
            SectionType::GALLERY->value,
            SectionType::VIDEO->value,
            SectionType::ENTITY_TABLE->value,
        ]);

        // Générer des params selon le type
        $params = match($type) {
            SectionType::TEXT->value => [
                'content' => $this->faker->paragraph(),
                'align' => $this->faker->randomElement(['left', 'center', 'right']),
                'size' => $this->faker->randomElement(['sm', 'md', 'lg', 'xl']),
            ],
            SectionType::IMAGE->value => [
                'src' => $this->faker->imageUrl(),
                'alt' => $this->faker->sentence(3),
                'caption' => $this->faker->optional()->sentence(),
                'align' => $this->faker->randomElement(['left', 'center', 'right']),
                'size' => $this->faker->randomElement(['sm', 'md', 'lg', 'xl', 'full']),
            ],
            SectionType::GALLERY->value => [
                'images' => [],
                'columns' => $this->faker->randomElement([2, 3, 4]),
                'gap' => $this->faker->randomElement(['sm', 'md', 'lg']),
            ],
            SectionType::VIDEO->value => [
                'src' => $this->faker->url(),
                'type' => $this->faker->randomElement(['youtube', 'vimeo', 'direct']),
                'autoplay' => $this->faker->boolean(20),
                'controls' => $this->faker->boolean(80),
            ],
            SectionType::ENTITY_TABLE->value => [
                'entity' => $this->faker->randomElement(['item', 'creature', 'spell']),
                'filters' => [],
                'columns' => [],
            ],
            default => ['content' => $this->faker->paragraph()],
        };

        return [
            'page_id' => null, // Géré dans le seeder
            'order' => $this->faker->numberBetween(1, 10),
            'type' => $type,
            'params' => $params,
            'is_visible' => $this->faker->randomElement([
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
