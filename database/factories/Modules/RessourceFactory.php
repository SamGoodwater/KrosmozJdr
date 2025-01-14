<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Panoply;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PanoplyFactory extends Factory
{
    protected $model = Panoply::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'bonus' => $this->faker->sentence,
            'usable' => $this->faker->boolean,
            'is_visible' => $this->faker->boolean,
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
        ];
    }
}
