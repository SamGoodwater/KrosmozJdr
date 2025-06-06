<?php

namespace Database\Factories\Type;

use App\Models\Type\ScenarioLink;
use App\Models\Entity\Scenario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioLinkFactory extends Factory
{
    protected $model = ScenarioLink::class;

    public function definition(): array
    {
        return [
            'scenario_id' => Scenario::factory(),
            'next_scenario_id' => Scenario::factory(),
            'condition' => $this->faker->optional()->sentence(),
        ];
    }
}
