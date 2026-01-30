<?php

namespace Database\Seeders\Type;

use Illuminate\Database\Seeder;
use App\Models\Type\ResourceType;
use App\Models\User;

/**
 * Seeder pour les types de ressources
 * 
 * Initialise les types de ressources de base
 */
class ResourceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemUser = User::getSystemUser();
        $createdBy = $systemUser ? $systemUser->id : null;

        $resourceTypes = [
            // Matériaux de base
            ['name' => 'Bois'],
            ['name' => 'Pierre'],
            ['name' => 'Métal'],
            ['name' => 'Cuir'],
            ['name' => 'Laine'],
            
            // Minerais
            ['name' => 'Minerai'],
            ['name' => 'Fragment'],
            ['name' => 'Gemme'],
            
            // Végétaux
            ['name' => 'Plante'],
            ['name' => 'Fleur'],
            ['name' => 'Graine'],
            
            // Autres
            ['name' => 'Peau'],
            ['name' => 'Plume'],
            ['name' => 'Oeuf'],
        ];

        foreach ($resourceTypes as $resourceType) {
            ResourceType::firstOrCreate(
                ['name' => $resourceType['name']],
                array_merge($resourceType, [
                    'state' => 'playable',
                    'read_level' => User::ROLE_GUEST,
                    'write_level' => User::ROLE_ADMIN,
                    'created_by' => $createdBy,
                ])
            );
        }

        $this->command->info('✅ Types de ressources créés : ' . count($resourceTypes));
    }
}

