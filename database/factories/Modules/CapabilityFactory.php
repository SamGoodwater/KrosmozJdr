<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Capability;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CapabilityFactory extends Factory
{
    protected $model = Capability::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'effect' => $this->faker->sentence,
            'level' => $this->faker->numberBetween(1, 100),
            'pa' => $this->faker->numberBetween(1, 10),
            'po' => $this->faker->numberBetween(1, 10),
            'po_editable' => $this->faker->boolean,
            'time_before_use_again' => $this->faker->numberBetween(1, 10),
            'casting_time' => $this->faker->numberBetween(1, 10),
            'duration' => $this->faker->numberBetween(1, 10),
            'element' => $this->faker->numberBetween(1, 5),
            'is_magic' => $this->faker->boolean,
            'ritual_available' => $this->faker->boolean,
            'powerful' => $this->faker->numberBetween(1, 10),
            'usable' => $this->faker->boolean,
            'is_visible' => $this->faker->boolean,
            'created_by' => null, // ou vous pouvez utiliser un ID d'utilisateur existant
            'image' => $this->faker->imageUrl(),
        ];
    }
}
