<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition()
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'is_visible' => $this->faker->boolean,
            'image' => $this->faker->imageUrl(),
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
        ];
    }
}
