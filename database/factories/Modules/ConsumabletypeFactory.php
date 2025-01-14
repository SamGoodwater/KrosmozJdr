<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Consumabletype;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConsumabletypeFactory extends Factory
{
    protected $model = Consumabletype::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
        ];
    }
}
