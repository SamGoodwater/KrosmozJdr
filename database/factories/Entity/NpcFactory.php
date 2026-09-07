<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Creature;
use App\Models\Entity\Npc;
use App\Support\Creature\CreatureSize;
use App\Support\Npc\NpcRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Npc>
 */
class NpcFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'creature_id' => Creature::factory(),
            'story' => fake()->optional()->text(200),
            'historical' => fake()->optional()->text(200),
            'age' => fake()->optional()->numerify('## ans'),
            'size' => fake()->numberBetween(CreatureSize::MINUSCULE, CreatureSize::GIGANTESQUE),
            'npc_role' => fake()->optional()->randomElement(NpcRole::values()),
            'breed_id' => null,
            'specialization_id' => null,
            'state' => Npc::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
        ];
    }
}
