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
            ['name' => 'Bois', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Pierre', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Métal', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Cuir', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Laine', 'usable' => 1, 'is_visible' => 'guest'],
            
            // Minerais
            ['name' => 'Minerai', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Fragment', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Gemme', 'usable' => 1, 'is_visible' => 'guest'],
            
            // Végétaux
            ['name' => 'Plante', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Fleur', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Graine', 'usable' => 1, 'is_visible' => 'guest'],
            
            // Autres
            ['name' => 'Peau', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Plume', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Oeuf', 'usable' => 1, 'is_visible' => 'guest'],
        ];

        foreach ($resourceTypes as $resourceType) {
            ResourceType::firstOrCreate(
                ['name' => $resourceType['name']],
                array_merge($resourceType, ['created_by' => $createdBy])
            );
        }

        $this->command->info('✅ Types de ressources créés : ' . count($resourceTypes));
    }
}

