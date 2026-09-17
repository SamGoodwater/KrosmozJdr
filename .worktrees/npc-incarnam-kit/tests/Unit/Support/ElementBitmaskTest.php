<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\ElementBitmask;
use PHPUnit\Framework\TestCase;

final class ElementBitmaskTest extends TestCase
{
    public function test_normalize_keeps_air_bitmask_not_legacy_neutre_eau(): void
    {
        $this->assertSame(8, ElementBitmask::normalize(8));
        $this->assertSame(['Air'], array_map(
            static fn (int $i): string => ElementBitmask::PRIMARY_LABELS[$i],
            ElementBitmask::toPrimaries(8)
        ));
        $this->assertSame('Air', ElementBitmask::label(8));
    }

    public function test_from_slug_air_is_bit_three(): void
    {
        $this->assertSame(1 << 3, ElementBitmask::fromSlug('air'));
        $this->assertSame(1 << 4, ElementBitmask::fromSlug('water'));
        $this->assertSame(1 << 0, ElementBitmask::fromSlug('neutral'));
    }
}
