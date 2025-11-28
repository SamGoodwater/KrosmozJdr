<?php

namespace Database\Seeders\Type;

use Illuminate\Database\Seeder;
use App\Models\Type\MonsterRace;
use App\Models\User;

/**
 * Seeder pour les races de monstres
 * 
 * Initialise les races de monstres de base avec leur hiérarchie
 */
class MonsterRaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemUser = User::getSystemUser();
        $createdBy = $systemUser ? $systemUser->id : null;

        // Races principales (sans super-race)
        $mainRaces = [
            ['name' => 'Bouftou', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Tofu', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Gobelin', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Bwork', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Champ Champ', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Piou', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Arakne', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Cochon de Lait', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Sanglier', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Craqueleur', 'usable' => 1, 'is_visible' => 'guest'],
        ];

        $createdRaces = [];
        foreach ($mainRaces as $race) {
            $createdRace = MonsterRace::firstOrCreate(
                ['name' => $race['name']],
                array_merge($race, ['created_by' => $createdBy, 'id_super_race' => null])
            );
            $createdRaces[$race['name']] = $createdRace;
        }

        // Sous-races (avec super-race)
        $subRaces = [
            ['name' => 'Bouftou Royal', 'super_race' => 'Bouftou'],
            ['name' => 'Tofu Royal', 'super_race' => 'Tofu'],
            ['name' => 'Gobelin Royal', 'super_race' => 'Gobelin'],
            ['name' => 'Bwork Royal', 'super_race' => 'Bwork'],
        ];

        foreach ($subRaces as $subRace) {
            $superRace = $createdRaces[$subRace['super_race']] ?? null;
            if ($superRace) {
                MonsterRace::firstOrCreate(
                    ['name' => $subRace['name']],
                    [
                        'name' => $subRace['name'],
                        'usable' => 1,
                        'is_visible' => 'guest',
                        'created_by' => $createdBy,
                        'id_super_race' => $superRace->id,
                    ]
                );
            }
        }

        $totalRaces = count($mainRaces) + count($subRaces);
        $this->command->info('✅ Races de monstres créées : ' . $totalRaces);
    }
}

