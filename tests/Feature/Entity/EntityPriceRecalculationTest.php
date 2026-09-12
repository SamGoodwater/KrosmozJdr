<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\User;
use App\Services\Characteristic\Pricing\EntityPriceRecalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntityPriceRecalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    public function test_store_item_proposes_calculated_price_from_level_and_rarity(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('entities.items.store'), [
                'name' => 'Cape neuve',
                'level' => 8,
                'rarity' => 1,
            ])
            ->assertRedirect();

        $item = Item::query()->where('name', 'Cape neuve')->first();
        $this->assertNotNull($item);
        $this->assertSame(150 * 8 + 200 * 1, $item->price_calculated);
        $this->assertNull($item->price_custom);
        $this->assertSame((string) (150 * 8 + 200 * 1), $item->price);
    }

    public function test_recalculate_item_price_resets_custom(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'created_by' => $admin->id,
            'level' => '10',
            'rarity' => 2,
            'bonus' => '{"strength":1}',
            'price_calculated' => 1,
            'price_custom' => 5000,
        ]);

        $this->actingAs($admin)
            ->post(route('entities.items.recalculatePrice', $item))
            ->assertRedirect();

        $item->refresh();
        $this->assertNull($item->price_custom);
        $this->assertSame(150 * 10 + 200 * 2, $item->price_calculated);
    }

    public function test_store_consumable_starts_at_zero_without_recipe(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('entities.consumables.store'), [
                'name' => 'Potion vide',
            ])
            ->assertRedirect();

        $consumable = Consumable::query()->where('name', 'Potion vide')->first();
        $this->assertNotNull($consumable);
        $this->assertSame(0, $consumable->price_calculated);
        $this->assertSame('0', $consumable->price);
    }

    public function test_recalculate_consumable_sums_recipe_resources(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $consumable = Consumable::factory()->create([
            'created_by' => $admin->id,
            'price_calculated' => 1,
            'price_custom' => 900,
        ]);
        $a = Resource::factory()->create(['price' => '100']);
        $b = Resource::factory()->create(['price' => '50 kamas']);
        $consumable->resources()->sync([
            $a->id => ['quantity' => 2],
            $b->id => ['quantity' => 1],
        ]);

        $this->actingAs($admin)
            ->post(route('entities.consumables.recalculatePrice', $consumable))
            ->assertRedirect();

        $consumable->refresh();
        $this->assertNull($consumable->price_custom);
        $this->assertSame(250, $consumable->price_calculated);
        $this->assertSame('250', $consumable->price);
    }

    public function test_command_recalculates_items_and_leaves_resources(): void
    {
        $item = Item::factory()->create([
            'level' => '4',
            'rarity' => 0,
            'bonus' => null,
            'price_calculated' => 0,
            'price_custom' => 777,
        ]);
        $resource = Resource::factory()->create(['price' => '42']);

        $this->artisan('entities:recalculate-prices', ['type' => 'items'])
            ->assertSuccessful();

        $item->refresh();
        $resource->refresh();
        $this->assertNull($item->price_custom);
        $this->assertSame(600, $item->price_calculated);
        $this->assertSame('42', $resource->price);
    }

    public function test_command_recalculates_consumables(): void
    {
        $consumable = Consumable::factory()->create([
            'price_calculated' => 0,
            'price_custom' => 333,
        ]);
        $resource = Resource::factory()->create(['price' => '40']);
        $consumable->resources()->attach($resource->id, ['quantity' => 2]);

        $this->artisan('entities:recalculate-prices', ['type' => 'consumables'])
            ->assertSuccessful();

        $consumable->refresh();
        $this->assertNull($consumable->price_custom);
        $this->assertSame(80, $consumable->price_calculated);
        $this->assertSame('80', $consumable->price);
    }

    public function test_recalculator_sums_consumable_recipe(): void
    {
        $consumable = Consumable::factory()->create(['price_custom' => 10]);
        $resource = Resource::factory()->create(['price' => '80']);
        $consumable->resources()->attach($resource->id, ['quantity' => 3]);

        $updated = app(EntityPriceRecalculator::class)->recalculateConsumable($consumable, true);
        $this->assertSame(240, $updated->price_calculated);
        $this->assertNull($updated->price_custom);
    }
}
