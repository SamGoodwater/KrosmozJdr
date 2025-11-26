<?php

namespace Database\Factories\Entity;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity\Creature>
 */
class CreatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->paragraph(),
            'hostility' => fake()->numberBetween(0, 4),
            'location' => fake()->optional()->city(),
            'level' => (string) fake()->numberBetween(1, 200),
            'other_info' => fake()->optional()->sentence(),
            'life' => (string) fake()->numberBetween(30, 5000),
            'pa' => (string) fake()->numberBetween(3, 12),
            'pm' => (string) fake()->numberBetween(3, 12),
            'po' => (string) fake()->numberBetween(0, 20),
            'ini' => (string) fake()->numberBetween(0, 100),
            'invocation' => (string) fake()->numberBetween(0, 10),
            'touch' => (string) fake()->numberBetween(0, 20),
            'ca' => (string) fake()->numberBetween(0, 50),
            'dodge_pa' => (string) fake()->numberBetween(0, 50),
            'dodge_pm' => (string) fake()->numberBetween(0, 50),
            'fuite' => (string) fake()->numberBetween(0, 50),
            'tacle' => (string) fake()->numberBetween(0, 50),
            'vitality' => (string) fake()->numberBetween(0, 50),
            'sagesse' => (string) fake()->numberBetween(0, 50),
            'strong' => (string) fake()->numberBetween(0, 50),
            'intel' => (string) fake()->numberBetween(0, 50),
            'agi' => (string) fake()->numberBetween(0, 50),
            'chance' => (string) fake()->numberBetween(0, 50),
            'do_fixe_neutre' => (string) fake()->numberBetween(0, 100),
            'do_fixe_terre' => (string) fake()->numberBetween(0, 100),
            'do_fixe_feu' => (string) fake()->numberBetween(0, 100),
            'do_fixe_air' => (string) fake()->numberBetween(0, 100),
            'do_fixe_eau' => (string) fake()->numberBetween(0, 100),
            'res_fixe_neutre' => (string) fake()->numberBetween(0, 100),
            'res_fixe_terre' => (string) fake()->numberBetween(0, 100),
            'res_fixe_feu' => (string) fake()->numberBetween(0, 100),
            'res_fixe_air' => (string) fake()->numberBetween(0, 100),
            'res_fixe_eau' => (string) fake()->numberBetween(0, 100),
            'res_neutre' => (string) fake()->numberBetween(0, 50),
            'res_terre' => (string) fake()->numberBetween(0, 50),
            'res_feu' => (string) fake()->numberBetween(0, 50),
            'res_air' => (string) fake()->numberBetween(0, 50),
            'res_eau' => (string) fake()->numberBetween(0, 50),
            'acrobatie_bonus' => (string) fake()->numberBetween(0, 10),
            'discretion_bonus' => (string) fake()->numberBetween(0, 10),
            'escamotage_bonus' => (string) fake()->numberBetween(0, 10),
            'athletisme_bonus' => (string) fake()->numberBetween(0, 10),
            'intimidation_bonus' => (string) fake()->numberBetween(0, 10),
            'arcane_bonus' => (string) fake()->numberBetween(0, 10),
            'histoire_bonus' => (string) fake()->numberBetween(0, 10),
            'investigation_bonus' => (string) fake()->numberBetween(0, 10),
            'nature_bonus' => (string) fake()->numberBetween(0, 10),
            'religion_bonus' => (string) fake()->numberBetween(0, 10),
            'dressage_bonus' => (string) fake()->numberBetween(0, 10),
            'medecine_bonus' => (string) fake()->numberBetween(0, 10),
            'perception_bonus' => (string) fake()->numberBetween(0, 10),
            'perspicacite_bonus' => (string) fake()->numberBetween(0, 10),
            'survie_bonus' => (string) fake()->numberBetween(0, 10),
            'persuasion_bonus' => (string) fake()->numberBetween(0, 10),
            'representation_bonus' => (string) fake()->numberBetween(0, 10),
            'supercherie_bonus' => (string) fake()->numberBetween(0, 10),
            'acrobatie_mastery' => fake()->numberBetween(0, 1),
            'discretion_mastery' => fake()->numberBetween(0, 1),
            'escamotage_mastery' => fake()->numberBetween(0, 1),
            'athletisme_mastery' => fake()->numberBetween(0, 1),
            'intimidation_mastery' => fake()->numberBetween(0, 1),
            'arcane_mastery' => fake()->numberBetween(0, 1),
            'histoire_mastery' => fake()->numberBetween(0, 1),
            'investigation_mastery' => fake()->numberBetween(0, 1),
            'nature_mastery' => fake()->numberBetween(0, 1),
            'religion_mastery' => fake()->numberBetween(0, 1),
            'dressage_mastery' => fake()->numberBetween(0, 1),
            'medecine_mastery' => fake()->numberBetween(0, 1),
            'perception_mastery' => fake()->numberBetween(0, 1),
            'perspicacite_mastery' => fake()->numberBetween(0, 1),
            'survie_mastery' => fake()->numberBetween(0, 1),
            'persuasion_mastery' => fake()->numberBetween(0, 1),
            'representation_mastery' => fake()->numberBetween(0, 1),
            'supercherie_mastery' => fake()->numberBetween(0, 1),
            'kamas' => fake()->optional()->numerify('####'),
            'drop_' => fake()->optional()->sentence(),
            'other_item' => fake()->optional()->sentence(),
            'other_consumable' => fake()->optional()->sentence(),
            'other_resource' => fake()->optional()->sentence(),
            'other_spell' => fake()->optional()->sentence(),
            'usable' => fake()->numberBetween(0, 1),
            'is_visible' => fake()->randomElement(['guest', 'user', 'player', 'game_master']),
            'image' => fake()->optional()->imageUrl(),
            'created_by' => User::factory(),
        ];
    }
}
