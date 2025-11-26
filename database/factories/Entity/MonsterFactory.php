<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Creature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Monster>
 */
class MonsterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isBoss = fake()->boolean(10);
        
        return [
            'creature_id' => Creature::factory(),
            'official_id' => fake()->optional()->numerify('####'),
            'dofusdb_id' => fake()->optional()->numerify('####'),
            'dofus_version' => '3',
            'auto_update' => fake()->boolean(80),
            'size' => fake()->numberBetween(0, 5),
            'is_boss' => $isBoss ? 1 : 0,
            'boss_pa' => $isBoss ? fake()->numerify('##') : '',
            'monster_race_id' => null, // Peut être défini dans le seeder
        ];
    }
}
