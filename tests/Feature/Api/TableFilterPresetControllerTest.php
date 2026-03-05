<?php

namespace Tests\Feature\Api;

use App\Models\TableFilterPreset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableFilterPresetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_crud_table_filter_presets(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $create = $this->postJson(route('api.table-presets.store'), [
            'entity_type' => 'spells',
            'table_id' => 'spells.index',
            'name' => 'Sorts niveau 200',
            'search_text' => 'feu',
            'filters' => ['level' => '200', 'state' => 'playable'],
            'limit' => 250,
            'is_default' => true,
        ]);
        $create->assertCreated();
        $presetId = $create->json('preset.id');

        $this->assertDatabaseHas('table_filter_presets', [
            'id' => (int) $presetId,
            'user_id' => $user->id,
            'entity_type' => 'spells',
            'table_id' => 'spells.index',
            'name' => 'Sorts niveau 200',
            'is_default' => 1,
        ]);

        $index = $this->getJson(route('api.table-presets.index', [
            'entity_type' => 'spells',
            'table_id' => 'spells.index',
            'include_global' => 1,
        ]));
        $index->assertOk();
        $index->assertJsonCount(1, 'presets');

        $update = $this->patchJson(route('api.table-presets.update', ['tablePreset' => $presetId]), [
            'name' => 'Sorts feu 200',
            'filters' => ['level' => '200', 'element' => '2'],
            'is_default' => true,
        ]);
        $update->assertOk();
        $update->assertJsonPath('preset.name', 'Sorts feu 200');

        $delete = $this->deleteJson(route('api.table-presets.destroy', ['tablePreset' => $presetId]));
        $delete->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('table_filter_presets', [
            'id' => (int) $presetId,
        ]);
    }

    public function test_default_preset_is_unique_per_scope(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $p1 = TableFilterPreset::create([
            'user_id' => $user->id,
            'entity_type' => 'items',
            'table_id' => 'items.index',
            'name' => 'Preset 1',
            'filters' => ['state' => 'playable'],
            'is_default' => true,
        ]);

        $create = $this->postJson(route('api.table-presets.store'), [
            'entity_type' => 'items',
            'table_id' => 'items.index',
            'name' => 'Preset 2',
            'filters' => ['rarity' => '4'],
            'is_default' => true,
        ]);
        $create->assertCreated();

        $p1->refresh();
        $this->assertFalse((bool) $p1->is_default);
    }
}

