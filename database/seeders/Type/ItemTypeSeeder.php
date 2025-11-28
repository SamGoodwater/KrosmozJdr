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
            ['name' => 'Arc', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Bouclier', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Bâton', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Dague', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Épée', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Marteau', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Pelle', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Hache', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Outil', 'usable' => 1, 'is_visible' => 'guest'],
            
            // Accessoires
            ['name' => 'Anneau', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Amulette', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Ceinture', 'usable' => 1, 'is_visible' => 'guest'],
            
            // Équipements
            ['name' => 'Chapeau', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Cape', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Bottes', 'usable' => 1, 'is_visible' => 'guest'],
            
            // Autres
            ['name' => 'Familier', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Monture', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Certificat', 'usable' => 1, 'is_visible' => 'guest'],
        ];

        foreach ($itemTypes as $itemType) {
            ItemType::firstOrCreate(
                ['name' => $itemType['name']],
                array_merge($itemType, ['created_by' => $createdBy])
            );
        }

        $this->command->info('✅ Types d\'items créés : ' . count($itemTypes));
    }
}

