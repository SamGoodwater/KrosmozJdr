<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Breed;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Breed>
 */
class BreedFactory extends Factory
{
    protected $model = Breed::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = ['Iop', 'Cra', 'Féca', 'Ecaflip', 'Eniripsa', 'Sram', 'Xélor', 'Pandawa', 'Roublard', 'Zobal', 'Steamer', 'Osamodas', 'Sacrieur', 'Ouginak', 'Foggernaut', 'Eliotrope', 'Huppermage', 'Osa', 'Sram', 'Enutrof'];

        $levels = [
            User::ROLE_GUEST,
            User::ROLE_USER,
            User::ROLE_PLAYER,
            User::ROLE_GAME_MASTER,
            User::ROLE_ADMIN,
            User::ROLE_SUPER_ADMIN,
        ];
        $readLevel = fake()->randomElement($levels);
        $writeLevel = fake()->randomElement(array_values(array_filter($levels, fn (int $lvl) => $lvl >= $readLevel)));

        return [
            'official_id' => fake()->optional()->numerify('####'),
            'dofusdb_id' => fake()->optional()->numerify('####'),
            'name' => fake()->unique()->randomElement($names),
            'description_fast' => fake()->optional()->sentence(),
            'description' => fake()->optional()->text(200),
            'life' => (string) fake()->numberBetween(30, 100),
            'life_dice' => fake()->randomElement(['1d6', '1d8', '1d10', '1d12']),
            'specificity' => fake()->randomElement(['Force', 'Intelligence', 'Agilité', 'Chance', 'Sagesse']),
            'dofus_version' => '3',
            'state' => fake()->randomElement([Breed::STATE_DRAFT, Breed::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => fake()->optional()->imageUrl(),
            'icon' => fake()->optional()->imageUrl(),
            'auto_update' => fake()->boolean(80),
            'created_by' => User::factory(),
        ];
    }
}
