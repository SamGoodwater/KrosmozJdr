<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Characteristics;

use App\Support\Characteristics\MonsterCharacteristicLimitExpander;
use Tests\TestCase;

class MonsterCharacteristicLimitExpanderTest extends TestCase
{
    public function test_expand_adds_half_span_each_side_and_can_go_negative(): void
    {
        $this->assertSame([-3, 33], MonsterCharacteristicLimitExpander::expand(6, 24));
        $this->assertSame([-5, 15], MonsterCharacteristicLimitExpander::expand(0, 10));
    }

    public function test_merge_looser_keeps_existing_monster_if_already_wider(): void
    {
        $this->assertSame([-100, 150], MonsterCharacteristicLimitExpander::mergeLooser(-50, 150, -100, 100));
        $this->assertSame([0, 20], MonsterCharacteristicLimitExpander::mergeLooser(3, 15, 0, 20));
    }

    public function test_skips_level_and_dice_stems(): void
    {
        $this->assertTrue(MonsterCharacteristicLimitExpander::shouldSkipStem('level'));
        $this->assertTrue(MonsterCharacteristicLimitExpander::shouldSkipStem('hit_dice'));
        $this->assertFalse(MonsterCharacteristicLimitExpander::shouldSkipStem('strength'));
    }
}
