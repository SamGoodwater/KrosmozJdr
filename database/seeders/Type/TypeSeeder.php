<?php

namespace Database\Seeders\Type;

use Illuminate\Database\Seeder;

/**
 * Seeder principal pour tous les types
 * 
 * Appelle tous les seeders de types
 */
class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ItemTypeSeeder::class,
            ConsumableTypeSeeder::class,
            MonsterRaceSeeder::class,
            ResourceTypeSeeder::class,
            SpellTypeSeeder::class,
        ]);

        $this->command->info('✅ Tous les types ont été initialisés');
    }
}

