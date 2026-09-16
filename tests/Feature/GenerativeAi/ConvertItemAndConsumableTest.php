<?php

declare(strict_types=1);

namespace Tests\Feature\GenerativeAi;

use App\Enums\EntityState;
use App\Models\AiGenerationRun;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class ConvertItemAndConsumableTest extends TestCase
{
    use FakesAnthropicJson;

    public function test_admin_converts_unique_item_to_auto(): void
    {
        $this->fakeAnthropicJson([
            'name' => 'Anneau du scénario',
            'description' => 'Unique de quête.',
            'bonus' => '+2 Force',
            'effect' => '{"strength":2}',
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableItemEtalon();
        $item = Item::factory()->create([
            'name' => 'Anneau brut',
            'description' => 'brouillon',
            'bonus' => null,
            'effect' => null,
            'dofusdb_id' => null,
            'official_id' => 'jdr:item:unique-source',
            'state' => EntityState::Draft->value,
            'auto_update' => true,
        ]);

        $this->actingAs($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'items', 'id' => $item->id]), [
                'action' => 'item',
            ])
            ->assertOk()
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $item->refresh();
        $this->assertSame(EntityState::Auto->value, $item->state);
        $this->assertFalse((bool) $item->auto_update);
        $this->assertSame('Anneau du scénario', $item->name);
        $this->assertSame('+2 Force', $item->bonus);
        Http::assertSentCount(1);
    }

    public function test_sourced_item_without_writable_fields_fails_before_llm(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableItemEtalon();
        $item = Item::factory()->create([
            'name' => 'Cape Dofus',
            'dofusdb_id' => '8236',
            'state' => EntityState::Raw->value,
        ]);

        $this->actingAs($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'items', 'id' => $item->id]), [
                'action' => 'item',
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertSame(EntityState::Raw->value, $item->fresh()->state);
        Http::assertSentCount(0);
    }

    public function test_admin_converts_consumable_effect_only(): void
    {
        $this->fakeAnthropicJson(['effect' => 'Soigne 5 PV hors combat.']);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableConsumableEtalon();
        $consumable = Consumable::factory()->create([
            'name' => 'Pain brut',
            'effect' => 'soin dofus opaque',
            'dofusdb_id' => '468',
            'state' => EntityState::Raw->value,
            'auto_update' => true,
        ]);

        $this->actingAs($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'consumables', 'id' => $consumable->id]), [
                'action' => 'consumable',
            ])
            ->assertOk()
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $consumable->refresh();
        $this->assertSame(EntityState::Auto->value, $consumable->state);
        $this->assertFalse((bool) $consumable->auto_update);
        $this->assertSame('Pain brut', $consumable->name);
        $this->assertSame('Soigne 5 PV hors combat.', $consumable->effect);
        Http::assertSentCount(1);
    }

    public function test_artisan_convert_consumable_by_official_id(): void
    {
        $this->fakeAnthropicJson(['effect' => 'Antidote : retire Poison.']);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableConsumableEtalon();
        $consumable = Consumable::factory()->create([
            'name' => 'Antidote brut',
            'official_id' => 'jdr:antidote-source',
            'effect' => 'ancien',
            'state' => EntityState::Draft->value,
            'dofusdb_id' => null,
        ]);

        $this->artisan('ia:convert', [
            'type' => 'consumable',
            '--official-id' => 'jdr:antidote-source',
            '--user' => $admin->id,
        ])->assertSuccessful();

        $this->assertSame('Antidote : retire Poison.', $consumable->fresh()->effect);
        $this->assertSame(EntityState::Auto->value, $consumable->fresh()->state);
    }

    private function seedPlayableItemEtalon(): Item
    {
        Panoply::factory()->create([
            'name' => 'Panoplie du Piou Vert',
            'state' => EntityState::Playable->value,
        ]);

        return Item::factory()->create([
            'name' => 'Cape du Piou Vert',
            'official_id' => 'jdr:item:cape-piou-vert',
            'state' => EntityState::Playable->value,
            'auto_update' => false,
        ]);
    }

    private function seedPlayableConsumableEtalon(): Consumable
    {
        return Consumable::factory()->create([
            'name' => 'Pain d\'Incarnam',
            'official_id' => 'jdr:heal:potion:5',
            'effect' => 'Soigne 5 PV.',
            'state' => EntityState::Playable->value,
            'auto_update' => false,
        ]);
    }
}
