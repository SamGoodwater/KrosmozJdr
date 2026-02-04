<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Database\Seeders\EquipmentCharacteristicConfigSeeder;
use Database\Seeders\SpellEffectTypeSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PageSeeder::class,
            SectionSeeder::class,
            \Database\Seeders\Type\TypeSeeder::class,
            CharacteristicSeeder::class,
            CreatureCharacteristicSeeder::class,
            ObjectCharacteristicSeeder::class,
            SpellCharacteristicSeeder::class,
            EquipmentCharacteristicConfigSeeder::class,
            SpellEffectTypeSeeder::class,
        ]);
    }
}
