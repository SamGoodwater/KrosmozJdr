<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Resource;
use App\Services\Seeder\Resource\EnsureMinimumPlayableResourcePrice;
use Tests\TestCase;

final class EnsureMinimumPlayableResourcePriceTest extends TestCase
{
    public function test_floors_playable_zero_prices_and_leaves_drafts(): void
    {
        $zero = Resource::factory()->create([
            'state' => Resource::STATE_DRAFT,
            'price' => '0',
            'created_by' => null,
        ]);
        $empty = Resource::factory()->create([
            'state' => Resource::STATE_DRAFT,
            'price' => '',
            'created_by' => null,
        ]);
        Resource::query()->whereKey($zero->id)->update([
            'state' => Resource::STATE_PLAYABLE,
            'price' => '0',
        ]);
        Resource::query()->whereKey($empty->id)->update([
            'state' => Resource::STATE_PLAYABLE,
            'price' => '',
        ]);
        $kept = Resource::factory()->create([
            'state' => Resource::STATE_PLAYABLE,
            'price' => '10',
            'created_by' => null,
        ]);
        $draftZero = Resource::factory()->create([
            'state' => Resource::STATE_DRAFT,
            'price' => '0',
            'created_by' => null,
        ]);

        $count = app(EnsureMinimumPlayableResourcePrice::class)->apply();

        $this->assertSame(2, $count);
        $this->assertSame('1', $zero->fresh()->price);
        $this->assertSame('1', $empty->fresh()->price);
        $this->assertSame('10', $kept->fresh()->price);
        $this->assertSame('0', $draftZero->fresh()->price);
        $this->assertSame(0, app(EnsureMinimumPlayableResourcePrice::class)->apply());
    }

    public function test_saving_a_playable_resource_clamps_zero_to_one(): void
    {
        $resource = Resource::factory()->create([
            'state' => Resource::STATE_DRAFT,
            'price' => '0',
            'created_by' => null,
        ]);
        $resource->state = Resource::STATE_PLAYABLE;
        $resource->save();

        $this->assertSame('1', $resource->fresh()->price);
    }
}
