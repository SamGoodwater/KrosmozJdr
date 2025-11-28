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
                'usable' => 1,
                'is_visible' => 'guest',
            ],
            [
                'name' => 'Défensif',
                'description' => 'Sorts qui protègent ou renforcent la défense',
                'color' => '#0000FF',
                'icon' => 'shield',
                'usable' => 1,
                'is_visible' => 'guest',
            ],
            [
                'name' => 'Soin',
                'description' => 'Sorts qui restaurent les points de vie',
                'color' => '#00FF00',
                'icon' => 'heart',
                'usable' => 1,
                'is_visible' => 'guest',
            ],
            [
                'name' => 'Buff',
                'description' => 'Sorts qui améliorent les capacités',
                'color' => '#FFFF00',
                'icon' => 'arrow-up',
                'usable' => 1,
                'is_visible' => 'guest',
            ],
            [
                'name' => 'Debuff',
                'description' => 'Sorts qui affaiblissent l\'ennemi',
                'color' => '#800080',
                'icon' => 'arrow-down',
                'usable' => 1,
                'is_visible' => 'guest',
            ],
            [
                'name' => 'Invocation',
                'description' => 'Sorts qui invoquent des créatures',
                'color' => '#FFA500',
                'icon' => 'magic',
                'usable' => 1,
                'is_visible' => 'guest',
            ],
            [
                'name' => 'Téléportation',
                'description' => 'Sorts de déplacement',
                'color' => '#00FFFF',
                'icon' => 'location-arrow',
                'usable' => 1,
                'is_visible' => 'guest',
            ],
            [
                'name' => 'Transformation',
                'description' => 'Sorts qui transforment la cible',
                'color' => '#FF00FF',
                'icon' => 'exchange-alt',
                'usable' => 1,
                'is_visible' => 'guest',
            ],
        ];

        foreach ($spellTypes as $spellType) {
            SpellType::firstOrCreate(
                ['name' => $spellType['name']],
                array_merge($spellType, ['created_by' => $createdBy])
            );
        }

        $this->command->info('✅ Types de sorts créés : ' . count($spellTypes));
    }
}

