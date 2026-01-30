<?php

namespace Database\Seeders\Type;

use Illuminate\Database\Seeder;
use App\Models\Type\SpellType;
use App\Models\User;

/**
 * Seeder pour les types de sorts
 * 
 * Initialise les types de sorts de base avec leurs couleurs
 */
class SpellTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemUser = User::getSystemUser();
        $createdBy = $systemUser ? $systemUser->id : null;

        $spellTypes = [
            [
                'name' => 'Offensif',
                'description' => 'Sorts qui infligent des dégâts',
                'color' => '#FF0000',
                'icon' => 'sword',
            ],
            [
                'name' => 'Défensif',
                'description' => 'Sorts qui protègent ou renforcent la défense',
                'color' => '#0000FF',
                'icon' => 'shield',
            ],
            [
                'name' => 'Soin',
                'description' => 'Sorts qui restaurent les points de vie',
                'color' => '#00FF00',
                'icon' => 'heart',
            ],
            [
                'name' => 'Buff',
                'description' => 'Sorts qui améliorent les capacités',
                'color' => '#FFFF00',
                'icon' => 'arrow-up',
            ],
            [
                'name' => 'Debuff',
                'description' => 'Sorts qui affaiblissent l\'ennemi',
                'color' => '#800080',
                'icon' => 'arrow-down',
            ],
            [
                'name' => 'Invocation',
                'description' => 'Sorts qui invoquent des créatures',
                'color' => '#FFA500',
                'icon' => 'magic',
            ],
            [
                'name' => 'Téléportation',
                'description' => 'Sorts de déplacement',
                'color' => '#00FFFF',
                'icon' => 'location-arrow',
            ],
            [
                'name' => 'Transformation',
                'description' => 'Sorts qui transforment la cible',
                'color' => '#FF00FF',
                'icon' => 'exchange-alt',
            ],
        ];

        foreach ($spellTypes as $spellType) {
            SpellType::firstOrCreate(
                ['name' => $spellType['name']],
                array_merge($spellType, [
                    'state' => 'playable',
                    'read_level' => User::ROLE_GUEST,
                    'write_level' => User::ROLE_ADMIN,
                    'created_by' => $createdBy,
                ])
            );
        }

        $this->command->info('✅ Types de sorts créés : ' . count($spellTypes));
    }
}

