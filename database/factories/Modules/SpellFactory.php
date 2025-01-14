<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Spell;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SpellFactory extends Factory
{
    protected $model = Spell::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'is_visible' => $this->faker->boolean,
            'page_id' => null, // ou vous pouvez utiliser un ID de page existant
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
            'image' => $this->faker->imageUrl(),
        ];
    }
}
