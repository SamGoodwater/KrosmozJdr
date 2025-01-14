<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Mobrace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MobraceFactory extends Factory
{
    protected $model = Mobrace::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
            'super_race' => $this->faker->word,
            'is_visible' => $this->faker->boolean,
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
        ];
    }
}
