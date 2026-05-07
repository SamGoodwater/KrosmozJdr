<?php

namespace Database\Seeders\Entity;

use App\Models\Entity\Condition;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = ['state' => Condition::STATE_PLAYABLE, 'read_level' => User::ROLE_GUEST, 'write_level' => User::ROLE_ADMIN];
        $conditions = [
            ['name' => 'Pesanteur', 'description' => 'Empêche certains déplacements forcés ou effets de placement.'],
            ['name' => 'Empoisonné', 'description' => 'Subit des dégâts ou effets de poison selon la source.'],
            ['name' => 'Étourdi', 'description' => 'Réduit ou empêche temporairement les actions.'],
            ['name' => 'Ralenti', 'description' => 'Réduit temporairement les points de mouvement ou la mobilité.'],
            ['name' => 'Affaibli', 'description' => 'Réduit temporairement la puissance ou certaines caractéristiques.'],
        ];
        foreach ($conditions as $condition) {
            Condition::query()->updateOrCreate(['name' => $condition['name']], array_merge($defaults, $condition));
        }
    }
}
