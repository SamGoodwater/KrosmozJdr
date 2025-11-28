<?php

namespace Database\Seeders\Type;

use Illuminate\Database\Seeder;
use App\Models\Type\ConsumableType;
use App\Models\User;

/**
 * Seeder pour les types de consommables
 * 
 * Initialise les types de consommables de base
 */
class ConsumableTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemUser = User::getSystemUser();
        $createdBy = $systemUser ? $systemUser->id : null;

        $consumableTypes = [
            ['name' => 'Potion', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Parchemin d\'expérience', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Objet de dons', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Pain', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Viande', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Poisson', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Fruit', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Légume', 'usable' => 1, 'is_visible' => 'guest'],
            ['name' => 'Boisson', 'usable' => 1, 'is_visible' => 'guest'],
        ];

        foreach ($consumableTypes as $consumableType) {
            ConsumableType::firstOrCreate(
                ['name' => $consumableType['name']],
                array_merge($consumableType, ['created_by' => $createdBy])
            );
        }

        $this->command->info('✅ Types de consommables créés : ' . count($consumableTypes));
    }
}

