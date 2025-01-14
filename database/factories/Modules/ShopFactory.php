<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShopFactory extends Factory
{
    protected $model = Shop::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'slug' => $this->faker->slug,
            'keyword' => $this->faker->word,
            'is_public' => $this->faker->boolean,
            'state' => $this->faker->numberBetween(0, 1),
            'is_visible' => $this->faker->boolean,
            'image' => $this->faker->imageUrl(),
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
        ];
    }
}
