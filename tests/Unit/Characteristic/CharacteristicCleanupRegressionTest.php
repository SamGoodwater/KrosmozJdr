<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\SeedsMinimalCharacteristics;
use Tests\TestCase;

/**
 * Garde-fous après le nettoyage des caractéristiques (compétences techniques, sag/vit, etc.).
 */
class CharacteristicCleanupRegressionTest extends TestCase
{
    use SeedsMinimalCharacteristics;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seedMinimalCharacteristicsIfEmpty();
    }

    public function test_removed_characteristic_keys_are_absent(): void
    {
        $removed = [
            'craftsmanship_passive_creature',
            'herbalism_object',
            'craftsmanship_object',
            'creature_lore_object',
            'failure_hit_object',
            'power_spell',
            'push_distance_spell',
            'push_damage_reduction_spell',
            'fixed_damage_sagesse_spell',
            'fixed_damage_vitalite_spell',
            'res_sagesse_spell',
            'res_vitalite_spell',
        ];

        foreach ($removed as $key) {
            $this->assertFalse(
                Characteristic::query()->where('key', $key)->exists(),
                "La caractéristique {$key} ne doit plus exister."
            );
        }
    }

    public function test_critical_hit_limits_are_symmetric(): void
    {
        $creatureStar = CharacteristicCreature::query()
            ->where('entity', CharacteristicCreature::ENTITY_ALL)
            ->whereHas('characteristic', fn ($q) => $q->where('key', 'critical_hit_creature'))
            ->first();
        $this->assertNotNull($creatureStar);
        $this->assertSame('-3', (string) $creatureStar->min);
        $this->assertSame('3', (string) $creatureStar->max);

        $creatureMonster = CharacteristicCreature::query()
            ->where('entity', CharacteristicCreature::ENTITY_MONSTER)
            ->whereHas('characteristic', fn ($q) => $q->where('key', 'critical_hit_creature'))
            ->first();
        $this->assertNotNull($creatureMonster);
        $this->assertSame('-6', (string) $creatureMonster->min);
        $this->assertSame('6', (string) $creatureMonster->max);

        $object = CharacteristicObject::query()
            ->whereHas('characteristic', fn ($q) => $q->where('key', 'critical_hit_object'))
            ->first();
        $this->assertNotNull($object);
        $this->assertSame('-3', (string) $object->min);
        $this->assertSame('3', (string) $object->max);
    }

    public function test_orphan_creature_columns_are_dropped(): void
    {
        foreach ([
            'do_sagesse',
            'do_vitalite',
            'res_sagesse',
            'res_vitalite',
            'artisanat_bonus',
            'herbaliste_bonus',
            'connaissance_creatures_bonus',
            'artisanat_mastery',
            'herbaliste_mastery',
            'connaissance_creatures_mastery',
        ] as $column) {
            $this->assertFalse(
                Schema::hasColumn('creatures', $column),
                "La colonne creatures.{$column} doit être droppée."
            );
        }
    }
}
