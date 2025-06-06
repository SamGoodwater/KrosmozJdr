<?php

namespace Database\Factories\Entity;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $slug = Str::slug($name . '-' . $this->faker->unique()->randomNumber(3));
        return [
            'name' => $name,
            'description' => $this->faker->optional()->realText(100),
            'slug' => $slug,
            'keyword' => $this->faker->optional()->word(),
            'is_public' => $this->faker->boolean(),
            'state' => $this->faker->numberBetween(0, 3),
            'usable' => $this->faker->numberBetween(0, 1),
            'is_visible' => $this->faker->randomElement(['guest', 'user', 'player', 'game_master', 'admin', 'super_admin']),
            'image' => $this->faker->optional()->imageUrl(128, 128, 'abstract', true, $name),
            'created_by' => null,
        ];
    }
}
