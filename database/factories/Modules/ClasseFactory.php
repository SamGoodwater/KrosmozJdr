<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Classe;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClasseFactory extends Factory
{
    protected $model = Classe::class;

    public function definition(): array
    {
        return [
            'official_id' => $this->faker->uuid,
            'dofusdb_id' => $this->faker->uuid,
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
            'description_fast' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'life' => $this->faker->numberBetween(1, 100),
            'life_dice' => $this->faker->numberBetween(1, 10),
            'specificity' => $this->faker->sentence,
            'usable' => $this->faker->boolean,
            'dofus_version' => $this->faker->randomElement(['1', '2', '3']),
            'is_visible' => $this->faker->boolean,
            'image' => $this->faker->imageUrl(),
            'icon' => $this->faker->imageUrl(),
            'auto_update' => $this->faker->boolean,
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
        ];
    }
}
