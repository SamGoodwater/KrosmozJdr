<?php

namespace Database\Seeders\Type;

use Illuminate\Database\Seeder;
use App\Models\Type\ItemType;
use App\Models\User;

/**
 * Seeder pour les types d'items
 * 
 * Initialise les types d'objets/équipements de base
 */
class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemUser = User::getSystemUser();
        $createdBy = $systemUser ? $systemUser->id : null;

        $itemTypes = [
            // Armes
            ['name' => 'Arc'],
            ['name' => 'Bouclier'],
            ['name' => 'Bâton'],
            ['name' => 'Dague'],
            ['name' => 'Épée'],
            ['name' => 'Marteau'],
            ['name' => 'Pelle'],
            ['name' => 'Hache'],
            ['name' => 'Outil'],
            
            // Accessoires
            ['name' => 'Anneau'],
            ['name' => 'Amulette'],
            ['name' => 'Ceinture'],
            
            // Équipements
            ['name' => 'Chapeau'],
            ['name' => 'Cape'],
            ['name' => 'Bottes'],
            
            // Autres
            ['name' => 'Familier'],
            ['name' => 'Monture'],
            ['name' => 'Certificat'],
        ];

        foreach ($itemTypes as $itemType) {
            ItemType::firstOrCreate(
                ['name' => $itemType['name']],
                array_merge($itemType, [
                    'state' => 'playable',
                    'read_level' => User::ROLE_GUEST,
                    'write_level' => User::ROLE_ADMIN,
                    'created_by' => $createdBy,
                ])
            );
        }

        $this->command->info('✅ Types d\'items créés : ' . count($itemTypes));
    }
}

