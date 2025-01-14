<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'official_id' => $this->faker->uuid,
            'dofusdb_id' => $this->faker->uuid,
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
            'level' => $this->faker->numberBetween(1, 200),
            'description' => $this->faker->sentence,
            'effect' => $this->faker->sentence,
            'bonus' => $this->faker->sentence,
            'recepe' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'rarity' => $this->faker->randomElement(array_keys(Item::RARITIES)),
            'usable' => $this->faker->boolean,
            'dofus_version' => $this->faker->randomElement(['1', '2', '3']),
            'is_visible' => $this->faker->boolean,
            'image' => $this->faker->imageUrl(),
            'auto_update' => $this->faker->boolean,
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
            'itemtype_id' => null, // ou vous pouvez utiliser un ID de type d'item existant
        ];
    }
}
