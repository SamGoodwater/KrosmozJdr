<?php

namespace Database\Seeders\Entity;

use App\Models\Entity\Condition;
use App\Models\User;
use App\Services\Condition\PlayableConditionCatalog;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = ['state' => Condition::STATE_PLAYABLE, 'read_level' => User::ROLE_GUEST, 'write_level' => User::ROLE_ADMIN];
        $conditions = [
            [
                'name' => 'Pesanteur',
                'description' => 'Tu ne peux pas te téléporter, ni être téléporté·e.',
                'image' => 'https://api.dofusdb.fr/img/states/stateImmobilization.png',
                'cant_be_moved' => true,
                'cant_be_pushed' => true,
                'cant_switch_position' => true,
            ],
            [
                'name' => 'Empoisonné',
                'description' => 'Tes jets d’attaque et tes jets de caractéristique ont un désavantage. Tu peux aussi subir des dégâts, selon la source du poison.',
                'image' => '/storage/images/icons/states/empoisonne.png',
            ],
            [
                'name' => 'Étourdi',
                'description' => "Tu es incapable d’agir : tes PA passent à 0, quels que soient tes bonus. Tu ne bouges plus : tes PM passent à 0, quels que soient tes bonus. Tu parles de façon hésitante.\nTu rates automatiquement tes jets de sauvegarde de Force et de Dextérité.\nLes jets d’attaque contre toi ont un avantage.",
                'image' => '/storage/images/icons/states/etourdi.png',
                'prevents_spell_cast' => true,
                'prevents_fight' => true,
            ],
            [
                'name' => 'Ralenti',
                'description' => 'Tes PM sont divisés par 2. Hors combat, tu ne peux pas courir.',
                'image' => '/storage/images/icons/states/ralenti.png',
            ],
            [
                'name' => 'Affaibli',
                'description' => 'Tes dégâts sont divisés par 2. Hors combat, tu te déplaces deux fois moins vite.',
                'image' => 'https://api.dofusdb.fr/img/states/stateWeakened.png',
            ],
            [
                'name' => 'Aveuglé',
                'description' => "Tu ne vois plus. Tu rates automatiquement tout jet de caractéristique qui a besoin de la vue.\nLes jets d’attaque contre toi ont un avantage. Les tiens ont un désavantage.",
                'image' => '/storage/images/icons/states/aveugle.png',
                'write_level' => User::ROLE_GAME_MASTER,
            ],
        ];
        foreach ($conditions as $condition) {
            Condition::query()->firstOrCreate(
                ['name' => $condition['name']],
                array_merge($defaults, $condition),
            );
        }

        app(PlayableConditionCatalog::class)->sync();
    }
}
