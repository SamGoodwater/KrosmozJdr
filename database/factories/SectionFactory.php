<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        $states = array_keys(\App\Models\Section::STATES);
        $types = ['text', 'image', 'gallery', 'video', 'quote', 'custom'];
        return [
            'page_id' => null, // Géré dans le seeder
            'order' => $this->faker->numberBetween(1, 10),
            'type' => $this->faker->randomElement($types),
            'params' => [
                'content' => $this->faker->paragraph(),
                'extra' => $this->faker->optional()->word(),
            ],
            'is_visible' => $this->faker->boolean(90),
            'state' => $this->faker->randomElement($states),
            'created_by' => null, // Géré dans le seeder
        ];
    }
}
