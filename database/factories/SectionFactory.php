<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\SectionType;
use App\Models\Section;
use App\Models\User;

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

        $levels = [
            User::ROLE_GUEST,
            User::ROLE_USER,
            User::ROLE_PLAYER,
            User::ROLE_GAME_MASTER,
            User::ROLE_ADMIN,
            User::ROLE_SUPER_ADMIN,
        ];
        $readLevel = $this->faker->randomElement($levels);
        $writeLevel = $this->faker->randomElement(array_values(array_filter($levels, fn (int $lvl) => $lvl >= $readLevel)));

        return [
            'page_id' => null, // Géré dans le seeder
            'title' => $this->faker->optional()->sentence(3),
            'slug' => $this->faker->optional()->slug(),
            'order' => $this->faker->numberBetween(1, 10),
            'template' => $template,
            'settings' => $settings,
            'data' => $data,
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'state' => $this->faker->randomElement([
                Section::STATE_RAW,
                Section::STATE_DRAFT,
                Section::STATE_PLAYABLE,
                Section::STATE_ARCHIVED,
            ]),
            'created_by' => null, // Géré dans le seeder
        ];
    }
}
