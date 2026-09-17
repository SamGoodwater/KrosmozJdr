<?php

namespace Tests\Unit\Models\Entity;

use App\Models\Entity\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemPriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_price_floor_and_syncs_price_column(): void
    {
        $item = Item::factory()->create([
            'price_calculated' => 100,
            'price_custom' => -40,
        ]);

        $item->refresh();
        $this->assertSame(60, $item->totalPriceKamas());
        $this->assertSame('60', $item->price);
        $this->assertSame(60, $item->displayPriceKamas());
    }

    public function test_create_without_calculated_price_uses_formula_and_syncs_price(): void
    {
        $item = Item::factory()->create([
            'level' => '8',
            'rarity' => 2,
            'bonus' => '{"strength":3}',
            'price_calculated' => null,
            'price_custom' => 0,
        ]);

        $expected = 150 * 8 + 200 * 2;
        $this->assertSame($expected, $item->price_calculated);
        $this->assertSame((string) $expected, $item->price);
    }

    public function test_updating_bonus_recalculates_price_column(): void
    {
        $item = Item::factory()->create([
            'level' => '8',
            'rarity' => 0,
            'bonus' => null,
            'price_calculated' => 10,
            'price_custom' => 5,
        ]);

        $item->bonus = '{"strength":1}';
        $item->save();

        $this->assertSame(150 * 8, $item->price_calculated);
        $this->assertSame(5, $item->price_custom);
        $this->assertSame((string) (150 * 8 + 5), $item->price);
    }

    public function test_display_price_is_null_when_total_is_zero(): void
    {
        $item = Item::factory()->create([
            'price_calculated' => 10,
            'price_custom' => -10,
        ]);

        $item->refresh();
        $this->assertSame(0, $item->totalPriceKamas());
        $this->assertNull($item->displayPriceKamas());
    }
}
