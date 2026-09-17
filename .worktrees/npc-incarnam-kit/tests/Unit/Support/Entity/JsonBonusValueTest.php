<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Entity;

use App\Support\Entity\JsonBonusValue;
use PHPUnit\Framework\TestCase;

class JsonBonusValueTest extends TestCase
{
    public function test_numeric_for_flat_item_bonus(): void
    {
        $this->assertSame(3, JsonBonusValue::numericForKey('{"strength":3,"vitality":10}', 'strength'));
        $this->assertSame(10, JsonBonusValue::numericForKey(['vitality' => 10], 'vitality_object'));
        $this->assertNull(JsonBonusValue::numericForKey('{"strength":3}', 'vitality'));
    }

    public function test_numeric_sums_panoply_tiers(): void
    {
        $raw = json_encode(['2' => ['strength' => 1], '3' => ['strength' => 2, 'vitality' => 5]], JSON_THROW_ON_ERROR);
        $this->assertSame(3, JsonBonusValue::numericForKey($raw, 'strength', true));
        $this->assertSame(5, JsonBonusValue::numericForKey($raw, 'vitality', true));
        $this->assertNull(JsonBonusValue::numericForKey($raw, 'agility', true));
    }

    public function test_sanitize_and_meta_keys(): void
    {
        $this->assertSame('strength', JsonBonusValue::sanitizeKey('strength_object'));
        $this->assertNull(JsonBonusValue::sanitizeKey('foo-bar'));
        $this->assertTrue(JsonBonusValue::isMetaKey('name'));
        $this->assertTrue(JsonBonusValue::isMetaKey('level_object'));
        $this->assertFalse(JsonBonusValue::isMetaKey('strength'));
    }
}
