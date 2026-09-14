<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Effect;

use App\Services\Effect\SpellElementInferenceService;
use App\Support\ElementBitmask;
use PHPUnit\Framework\TestCase;

final class SpellElementInferenceServiceTest extends TestCase
{
    private SpellElementInferenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SpellElementInferenceService;
    }

    public function test_characteristic_air_yields_air_mask(): void
    {
        $primaries = $this->service->primariesFromParams(['characteristic' => 'air']);

        $this->assertSame([3], $primaries);
        $this->assertSame(ElementBitmask::fromSlug('air'), ElementBitmask::fromPrimaries($primaries));
    }

    public function test_dofus_water_element_id_yields_water_mask(): void
    {
        $mask = $this->service->maskFromConversionPayload([
            'effects' => [
                [
                    'sub_effects' => [
                        ['params' => ['dofus_element_id' => 2, 'characteristic' => 'water']],
                    ],
                ],
            ],
        ]);

        $this->assertSame(1 << 4, $mask);
    }

    public function test_agility_stat_is_not_an_element(): void
    {
        $this->assertSame([], $this->service->primariesFromParams(['characteristic' => 'agi']));
    }
}
