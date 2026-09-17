<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\KrefEntityRegistry;
use Tests\TestCaseNoDatabase;

class KrefEntityRegistryTest extends TestCaseNoDatabase
{
    public function test_allowed_types_contains_expected_entities(): void
    {
        $types = KrefEntityRegistry::allowedTypes();

        $this->assertContains('spells', $types);
        $this->assertContains('monsters', $types);
        $this->assertContains('capabilities', $types);
        $this->assertNotContains('resource-types', $types);
    }

    public function test_is_allowed_type_checks_membership(): void
    {
        $this->assertTrue(KrefEntityRegistry::isAllowedType('items'));
        $this->assertFalse(KrefEntityRegistry::isAllowedType('unknown'));
    }
}
