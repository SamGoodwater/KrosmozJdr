<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi\EquipmentGrid;

use App\Services\GenerativeAi\EquipmentGrid\EquipmentBonusDecoder;
use PHPUnit\Framework\TestCase;

final class EquipmentBonusDecoderTest extends TestCase
{
    private EquipmentBonusDecoder $decoder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoder = new EquipmentBonusDecoder;
    }

    public function test_prefers_converted_effect_object(): void
    {
        $bonuses = $this->decoder->decode(
            '{"strength":2,"vitality":1}',
            '[{"characteristic":15,"from":40,"to":50}]',
        );

        $this->assertSame(2.0, $bonuses['strength']);
        $this->assertSame(1.0, $bonuses['vitality']);
        $this->assertArrayNotHasKey('intelligence', $bonuses);
    }

    public function test_reads_flat_bonus_when_effect_empty(): void
    {
        $bonuses = $this->decoder->decode(null, '{"intelligence":3}');

        $this->assertSame(3.0, $bonuses['intelligence']);
    }

    public function test_reads_dofus_effects_array_from_bonus(): void
    {
        $bonuses = $this->decoder->decode(null, json_encode([
            ['from' => 21, 'to' => 29, 'characteristic' => 10, 'elementId' => 1, 'effectId' => 118],
            ['from' => 6, 'to' => 9, 'characteristic' => -1, 'elementId' => 2, 'effectId' => 100],
        ], JSON_THROW_ON_ERROR));

        $this->assertSame(25.0, $bonuses['strength']);
        $this->assertSame(7.5, $bonuses['fixed_damage_fire']);
    }

    public function test_merges_rarity_keyed_bonus_maps(): void
    {
        $bonuses = $this->decoder->decode(json_encode([
            '2' => ['strength' => 1],
            '3' => ['strength' => 2, 'vitality' => 4],
        ], JSON_THROW_ON_ERROR));

        $this->assertSame(3.0, $bonuses['strength']);
        $this->assertSame(4.0, $bonuses['vitality']);
    }

    public function test_returns_empty_for_invalid_json(): void
    {
        $this->assertSame([], $this->decoder->decode('not-json', '<p>html</p>'));
    }
}
