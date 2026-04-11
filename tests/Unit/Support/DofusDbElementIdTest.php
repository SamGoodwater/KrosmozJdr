<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\DofusDbElementId;
use App\Support\ElementBitmask;
use PHPUnit\Framework\TestCase;

final class DofusDbElementIdTest extends TestCase
{
    public function test_dofus_element_ids_map_to_krosmoz_primaries_not_legacy_spell_codes(): void
    {
        $this->assertSame(0, DofusDbElementId::toKrosmozElementPrimaryIndex(0));
        $this->assertSame(2, DofusDbElementId::toKrosmozElementPrimaryIndex(1));
        $this->assertSame(4, DofusDbElementId::toKrosmozElementPrimaryIndex(2));
        $this->assertSame(1, DofusDbElementId::toKrosmozElementPrimaryIndex(3));
        $this->assertSame(3, DofusDbElementId::toKrosmozElementPrimaryIndex(4));
    }

    public function test_spell_global_negative_or_null_yields_null_mask(): void
    {
        $this->assertNull(DofusDbElementId::spellGlobalElementIdToMask(null));
        $this->assertNull(DofusDbElementId::spellGlobalElementIdToMask(-1));
    }

    public function test_fire_dofus_id_produces_fire_bit_not_water(): void
    {
        $m = DofusDbElementId::spellGlobalElementIdToMask(1);
        $this->assertNotNull($m);
        $this->assertSame([2], ElementBitmask::toPrimaries($m));
    }
}
