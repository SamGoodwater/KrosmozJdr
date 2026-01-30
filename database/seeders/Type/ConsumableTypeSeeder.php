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
            ['name' => 'Potion'],
            ['name' => 'Parchemin d\'expérience'],
            ['name' => 'Objet de dons'],
            ['name' => 'Pain'],
            ['name' => 'Viande'],
            ['name' => 'Poisson'],
            ['name' => 'Fruit'],
            ['name' => 'Légume'],
            ['name' => 'Boisson'],
        ];

        foreach ($consumableTypes as $consumableType) {
            ConsumableType::firstOrCreate(
                ['name' => $consumableType['name']],
                array_merge($consumableType, [
                    'state' => 'playable',
                    'read_level' => User::ROLE_GUEST,
                    'write_level' => User::ROLE_ADMIN,
                    'created_by' => $createdBy,
                ])
            );
        }

        $this->command->info('✅ Types de consommables créés : ' . count($consumableTypes));
    }
}

