<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'component' => $this->faker->word,
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'order_num' => $this->faker->numberBetween(1, 100),
            'is_visible' => $this->faker->boolean,
            'page_id' => null, // ou vous pouvez utiliser un ID de page existant
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
        ];
    }
}
