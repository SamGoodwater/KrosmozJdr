<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;

/**
 * Le catalogue de traits reste permanent et tient dans la limite de description.
 */
final class PlayableCreatureTraitCatalogTest extends TestCase
{
    public function test_traits_are_permanent_catalog_entries(): void
    {
        /** @var list<array{name: string, description: string, image: string}> $traits */
        $traits = require dirname(__DIR__, 3).'/database/seeders/data/entities/creature-traits/playable-catalog.php';

        $names = array_column($traits, 'name');
        $this->assertSame($names, array_values(array_unique($names)));
        $this->assertContains('Vif / Vive', $names);
        $this->assertContains('Rapide', $names);
        $this->assertNotContains('École prédestinée', $names);

        $low = 0;
        $high = 0;
        foreach ($traits as $trait) {
            $this->assertLessThanOrEqual(255, mb_strlen($trait['description']), $trait['name']);
            $this->assertStringStartsWith('/storage/images/icons/traits/', $trait['image']);
            $this->assertFileExists(dirname(__DIR__, 3).'/storage/app/public/'.substr($trait['image'], strlen('/storage/')), $trait['name']);
            $this->assertStringNotContainsString('dissip', mb_strtolower($trait['description']));
            $this->assertDoesNotMatchRegularExpression('/\b\d+\s+tours?\b/u', $trait['description']);
            $this->assertGreaterThanOrEqual(1, $trait['level'], $trait['name']);
            $this->assertLessThanOrEqual(20, $trait['level'], $trait['name']);
            $this->assertIsBool($trait['available_for_characters'], $trait['name']);
            if ($trait['level'] <= 4) {
                $low++;
            }
            if ($trait['level'] >= 12) {
                $high++;
            }
        }
        $this->assertGreaterThan($high, $low);

        $byName = array_column($traits, null, 'name');
        $this->assertSame('Tu commences le combat en premier, sans jet d’initiative.', $byName['Vif / Vive']['description']);
        $this->assertFalse($byName['Vif / Vive']['available_for_characters']);
        $this->assertSame(16, $byName['Vif / Vive']['level']);
        $this->assertStringContainsString('PM de base augmentent de 2', $byName['Rapide']['description']);
        $this->assertTrue($byName['Rapide']['available_for_characters']);
        $this->assertSame(12, $byName['Rapide']['level']);
        $this->assertSame(20, $byName['Inébranlable']['level']);
        $this->assertTrue($byName['Inébranlable']['available_for_characters']);
    }
}
