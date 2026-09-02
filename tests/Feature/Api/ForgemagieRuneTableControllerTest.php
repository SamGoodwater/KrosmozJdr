<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Characteristic;
use App\Models\CharacteristicObject;
use App\Models\Type\ItemType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForgemagieRuneTableControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_receives_rune_table(): void
    {
        $boots = ItemType::factory()->create(['name' => 'Bottes']);

        $characteristic = Characteristic::create([
            'key' => 'movement_points_object',
            'name' => 'Bonus de points de mouvement',
            'type' => 'int',
            'sort_order' => 0,
            'group' => 'object',
        ]);
        $row = CharacteristicObject::create([
            'characteristic_id' => $characteristic->id,
            'entity' => CharacteristicObject::ENTITY_ALL,
            'forgemagie_max' => 1,
            'base_price_per_unit' => 1000,
            'rune_price_per_unit' => 2000,
        ]);
        $row->allowedItemTypes()->attach($boots->id);

        $response = $this->getJson('/api/characteristics/forgemagie-rune-table');

        $response->assertOk();
        $response->assertJsonStructure([
            'rows' => [
                ['key', 'name', 'max_bonus', 'base_price', 'rune_price', 'item_types', 'restricted'],
            ],
            'meta' => ['total', 'sort_by', 'sort_dir', 'price_notice'],
        ]);
        $response->assertJsonPath('rows.0.key', 'movement_points_object');
        $response->assertJsonPath('rows.0.rune_price', 2000);
        $response->assertJsonPath('rows.0.item_types', ['Bottes']);
    }

    public function test_sort_parameters_are_normalized(): void
    {
        $response = $this->getJson('/api/characteristics/forgemagie-rune-table?sort_by=bogus&sort_dir=sideways');

        $response->assertOk();
        $response->assertJsonPath('meta.sort_by', 'name');
        $response->assertJsonPath('meta.sort_dir', 'asc');
    }
}
