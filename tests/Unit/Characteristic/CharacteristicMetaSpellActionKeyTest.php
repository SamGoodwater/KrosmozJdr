<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Enums\CharacteristicNature;
use App\Models\Characteristic;
use App\Models\CharacteristicSpell;
use App\Services\Characteristic\CharacteristicMetaByDbColumnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La caractéristique Actions n’a pas de colonne SQL : le frontend la lit par clé,
 * avec une surcharge visuelle par slug d’action.
 */
class CharacteristicMetaSpellActionKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_spell_meta_indexes_actions_characteristic_with_overrides(): void
    {
        $characteristic = Characteristic::create([
            'key' => 'actions_spell',
            'name' => 'Actions',
            'short_name' => 'Action',
            'type' => 'string',
            'status' => Characteristic::STATUS_VALIDEE,
            'sort_order' => 218,
            'group' => 'spell',
            'nature' => CharacteristicNature::Descriptif,
            'icon' => 'fa-solid fa-wand-magic-sparkles',
            'color' => 'zinc',
            'value_overrides' => [
                [
                    'value' => 'frapper',
                    'icon' => 'fa-solid fa-burst',
                    'color' => 'pink',
                    'subtitle' => 'Attaque',
                ],
            ],
        ]);

        CharacteristicSpell::create([
            'characteristic_id' => $characteristic->id,
            'entity' => CharacteristicSpell::ENTITY_ALL,
            'db_column' => null,
        ]);

        $service = new CharacteristicMetaByDbColumnService;
        $byKey = $service->buildSpellByCharacteristicKey();

        $this->assertArrayHasKey('actions_spell', $byKey);
        $this->assertSame('Actions', $byKey['actions_spell']['name']);
        $this->assertSame('pink', $byKey['actions_spell']['value_overrides'][0]['color']);
        $this->assertSame('Attaque', $byKey['actions_spell']['value_overrides'][0]['subtitle']);
        $this->assertSame('fa-solid fa-burst', $byKey['actions_spell']['value_overrides'][0]['icon']);
        $this->assertArrayNotHasKey('actions_spell', $service->buildSpellByDbColumn());
    }
}
