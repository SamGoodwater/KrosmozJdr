<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Itemtype;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItemtypeFactory extends Factory
{
    protected $model = Itemtype::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
        ];
    }
}
