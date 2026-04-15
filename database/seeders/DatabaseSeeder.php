<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CriticalPagesSeeder::class,
            NavMenuSeeder::class,
            PageSeeder::class,
            SectionSeeder::class,
            \Database\Seeders\Type\TypeSeeder::class,
            CharacteristicSeeder::class,
            CreatureCharacteristicSeeder::class,
            ObjectCharacteristicSeeder::class,
            DofusdbCharacteristicIdSeeder::class,
            SpellCharacteristicSeeder::class,
            SpellEffectTypeSeeder::class,
            SubEffectSeeder::class,
            ScrappingEntityMappingSeeder::class,
            ScrappingEntityMappingCharacteristicSeeder::class,
            CreationPagesSeeder::class,
        ]);
    }
}
