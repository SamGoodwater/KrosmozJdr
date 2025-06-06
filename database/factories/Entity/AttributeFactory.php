<?php

namespace Database\Factories\Entity;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = ['Force', 'Intelligence', 'Agilité', 'Chance', 'Sagesse', 'Vitalité'];
        $name = $this->faker->unique()->randomElement($names);
        return [
            'name' => $name,
            'description' => $this->faker->optional()->realText(60),
            'usable' => $this->faker->numberBetween(0, 1),
            'is_visible' => $this->faker->randomElement(['guest', 'user', 'player', 'game_master', 'admin', 'super_admin']),
            'image' => $this->faker->optional()->imageUrl(128, 128, 'abstract', true, $name),
            'created_by' => null,
        ];
    }
}
