<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Language>
 */
class LanguageFactory extends Factory
{
    protected $model = Language::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'color' => '#'.sprintf('%06x', fake()->numberBetween(0, 0xFFFFFF)),
        ];
    }
}
