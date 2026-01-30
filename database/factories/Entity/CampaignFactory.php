<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Campaign;
use App\Models\User;
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
            'name' => $name,
            'description' => $this->faker->optional()->realText(100),
            'slug' => $slug,
            'keyword' => $this->faker->optional()->word(),
            'is_public' => $this->faker->boolean(),
            'progress_state' => $this->faker->numberBetween(0, 3),
            'state' => $this->faker->randomElement([Campaign::STATE_DRAFT, Campaign::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => $this->faker->optional()->imageUrl(128, 128, 'abstract', true, $name),
            'created_by' => null,
        ];
    }
}
