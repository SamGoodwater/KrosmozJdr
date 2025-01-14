<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Ressourcetype;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RessourcetypeFactory extends Factory
{
    protected $model = Ressourcetype::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
        ];
    }
}
