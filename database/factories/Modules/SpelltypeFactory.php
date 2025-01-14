<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Spelltype;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SpelltypeFactory extends Factory
{
    protected $model = Spelltype::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'color' => $this->faker->hexColor,
            'icon' => $this->faker->imageUrl(),
            'is_visible' => $this->faker->boolean,
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
        ];
    }
}
