<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use PHPUnit\Framework\TestCase;

/**
 * Garde-fou : le seeder objet doit toujours synchroniser la pivot item_types (y compris liste vide).
 */
final class CharacteristicGroupSeederItemTypeSyncTest extends TestCase
{
    public function test_object_seeder_always_calls_sync_even_when_empty(): void
    {
        $path = dirname(__DIR__, 3).'/database/seeders/CharacteristicGroupSeeder.php';
        $source = (string) file_get_contents($path);

        $this->assertStringContainsString('allowedItemTypes()->sync($itemTypeIds)', $source);
        $this->assertStringContainsString('SeedMode::overwrite() || SeedMode::wasRecentlyCreated($model)', $source);
        $this->assertStringNotContainsString('if ($itemTypeIds !== [])', $source);
    }
}
