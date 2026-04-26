<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Services\Characteristic\CharacteristicMetaByDbColumnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Les krefs « characteristic » passent la clé métier (ex. {@code strength_creature}) ;
 * le pivot créature indexe surtout la colonne SQL ({@code strong}). Le meta doit exposer les deux.
 */
class CharacteristicMetaCreatureByDbColumnAliasTest extends TestCase
{
    use RefreshDatabase;

    public function test_creature_meta_indexes_by_canonical_characteristic_key(): void
    {
        $characteristic = Characteristic::create([
            'key' => 'strength_creature',
            'name' => 'Force',
            'short_name' => 'For',
            'type' => 'int',
            'status' => Characteristic::STATUS_A_VALIDER,
            'sort_order' => 1,
            'group' => 'creature',
            'icon' => 'earth.webp',
            'color' => 'brown',
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $characteristic->id,
            'entity' => CharacteristicCreature::ENTITY_ALL,
            'db_column' => 'strong',
            'default_value' => '8',
        ]);

        $service = new CharacteristicMetaByDbColumnService;
        $byDb = $service->buildCreatureByDbColumn();

        $this->assertArrayHasKey('strong', $byDb);
        $this->assertArrayHasKey('strength_creature', $byDb);
        $this->assertSame($byDb['strong'], $byDb['strength_creature']);
        $this->assertSame('strength_creature', $byDb['strength_creature']['key']);
    }
}
