<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Entity\CapabilitySeeder;
use Database\Seeders\Entity\ClassBreedSeeder;
use Database\Seeders\Entity\ConditionSeeder;
use Database\Seeders\Entity\ConsumableSeeder;
use Database\Seeders\Entity\CreatureTraitSeeder;
use Database\Seeders\Entity\ItemSeeder;
use Database\Seeders\Entity\LanguageSeeder;
use Database\Seeders\Entity\NpcSeeder;
use Database\Seeders\Entity\PanoplySeeder;
use Database\Seeders\Entity\ResourceSeeder;
use Database\Seeders\Entity\SpellSeeder;
use Database\Seeders\Type\TypeSeeder;
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
            TypeSeeder::class,
            LanguageSeeder::class,
            ConditionSeeder::class,
            CreatureTraitSeeder::class,
            NpcSeeder::class,
            ItemSeeder::class,
            ResourceSeeder::class,
            ConsumableSeeder::class,
            ClassBreedSeeder::class,
            CapabilitySeeder::class,
            SpellSeeder::class,
            PanoplySeeder::class,
            CharacteristicSeeder::class,
            CreatureCharacteristicSeeder::class,
            ObjectCharacteristicSeeder::class,
            DofusdbCharacteristicIdSeeder::class,
            SpellCharacteristicSeeder::class,
            SubEffectSeeder::class,
            ScrappingEntityMappingSeeder::class,
            ScrappingEntityMappingCharacteristicSeeder::class,
            CreationPagesSeeder::class,
            BibliothequeEntityPagesSeeder::class,
        ]);
    }
}
