<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use Database\Seeders\Entity\DraftSpecializationContentRenderer;
use Tests\TestCase;

/**
 * L’Artiste est l’essai d’enveloppe 2.4.6 (tableau + style au niveau 3).
 */
final class PlayableArtisteSheetTest extends TestCase
{
    public function test_artiste_follows_envelope_246(): void
    {
        $spec = require database_path('seeders/data/playable-specializations/artiste.php');

        $this->assertTrue($spec['playable']);
        $this->assertSame('2.4.6', $spec['envelope']);
        $this->assertSame('Artiste', $spec['name']);
        $this->assertSame('Applaudissement', $spec['signature']['name'] ?? null);
        $this->assertSame(3, $spec['styles_at']);
        $this->assertSame(
            ['musicien', 'saltimbanque', 'troubadour', 'voyant'],
            array_keys($spec['styles'])
        );
        $this->assertSame(2, $spec['levels'][1]['capacity_pick']);
        $this->assertSame(1, $spec['levels'][1]['aptitude_pick']);
        $this->assertTrue($spec['levels'][3]['unlock_style']);
    }

    public function test_artiste_html_starts_with_progression_table(): void
    {
        $spec = require database_path('seeders/data/playable-specializations/artiste.php');
        $html = implode('', array_column((new DraftSpecializationContentRenderer)->sections($spec), 'content'));

        $this->assertStringContainsString('Progression', $html);
        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('Styles (niveau 3)', $html);
        $this->assertStringContainsString('Musicien', $html);
        $this->assertStringContainsString('Contre-refrain', $html);
        $this->assertStringContainsString('Applaudissement', $html);
        $this->assertStringContainsString('Capacités — choisis-en 2', $html);
        $this->assertStringNotContainsString('Repères', $html);
        $this->assertStringNotContainsString('En bref', $html);
        $this->assertStringNotContainsString('Fil rouge', $html);
    }
}
