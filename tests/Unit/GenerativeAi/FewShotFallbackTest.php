<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Models\Entity\Spell;
use App\Services\GenerativeAi\FewShotFallback;
use Tests\TestCase;

final class FewShotFallbackTest extends TestCase
{
    public function test_spell_falls_back_to_official_id_when_names_missing(): void
    {
        $spell = Spell::factory()->create([
            'name' => 'Glyphe maison',
            'official_id' => 'jdr:feca-immunite',
            'state' => EntityState::Playable->value,
        ]);

        $ids = app(FewShotFallback::class)->idsFor('spell');

        $this->assertSame([(int) $spell->id], $ids);
    }

    public function test_item_falls_back_to_official_id_prefix(): void
    {
        $item = Item::factory()->create([
            'name' => 'Unique test',
            'official_id' => 'jdr:item:unique-test',
            'state' => EntityState::Playable->value,
        ]);

        $ids = app(FewShotFallback::class)->idsFor('item');

        $this->assertSame([(int) $item->id], $ids);
    }
}
