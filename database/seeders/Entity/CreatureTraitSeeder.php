<?php

namespace Database\Seeders\Entity;

use App\Models\Entity\CreatureTrait;
use App\Models\User;
use Illuminate\Database\Seeder;

class CreatureTraitSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = ['state' => CreatureTrait::STATE_PLAYABLE, 'read_level' => User::ROLE_GUEST, 'write_level' => User::ROLE_ADMIN];
        $traits = [
            ['name' => 'Malade', 'description' => 'Désavantage sur les tests de Force et d’Agilité selon les règles.'],
            ['name' => 'Lourd', 'description' => 'Difficile à déplacer et incapable de se déplacer sur des sols fragiles.'],
            ['name' => 'Petite taille', 'description' => 'Avantage en discrétion, mais déplacement réduit.'],
            ['name' => 'Grande taille', 'description' => 'Avantage en intimidation, désavantage en discrétion.'],
            ['name' => 'Gigantesque', 'description' => 'Grande taille extrême avec avantages de Force et perception.'],
            ['name' => 'Insensible aux poisons', 'description' => 'Ne peut pas être empoisonné.'],
            ['name' => 'Métaboliseur rapide', 'description' => 'Ne peut pas être empoisonné par ingestion.'],
            ['name' => 'Vif / Vive', 'description' => 'Commence le combat en premier sans jet d’initiative.'],
            ['name' => 'Agile', 'description' => 'Réussit automatiquement ses jets de fuite et ne peut pas être taclé.'],
        ];
        foreach ($traits as $trait) { CreatureTrait::query()->updateOrCreate(['name' => $trait['name']], array_merge($defaults, $trait)); }
    }
}
