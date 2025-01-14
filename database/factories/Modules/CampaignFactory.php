<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'slug' => $this->faker->slug,
            'keyword' => $this->faker->word,
            'is_public' => $this->faker->boolean,
            'state' => $this->faker->randomElement(array_values(Campaign::STATE)),
            'is_visible' => $this->faker->boolean,
            'image' => $this->faker->imageUrl(),
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
        ];
    }
}
