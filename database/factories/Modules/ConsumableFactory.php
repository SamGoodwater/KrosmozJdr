<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Consumable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConsumableFactory extends Factory
{
    protected $model = Consumable::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'is_unbewitchable' => $this->faker->boolean,
            'is_malus' => $this->faker->boolean,
            'usable' => $this->faker->boolean,
            'is_visible' => $this->faker->boolean,
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
            'image' => $this->faker->imageUrl(),
        ];
    }
}
