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
