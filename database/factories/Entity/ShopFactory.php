<?php

namespace Database\Factories\Entity;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true) . ' Shop',
            'description' => fake()->optional()->sentence(),
            'location' => fake()->optional()->city(),
            'price' => fake()->numberBetween(0, 100),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master', 'admin', 'super_admin']),
            'image' => fake()->optional()->imageUrl(128, 128, 'abstract', true),
            'created_by' => null,
            'npc_id' => null,
        ];
    }
}
