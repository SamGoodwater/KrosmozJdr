<?php

namespace Database\Factories\Modules;

use App\Models\Modules\Resourcetype;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResourcetypeFactory extends Factory
{
    protected $model = Resourcetype::class;

    public function definition(): array
    {
        return [
            'uniqid' => Str::uuid()->toString(),
            'name' => $this->faker->word,
        ];
    }
}
