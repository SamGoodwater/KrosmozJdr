<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use Database\Seeders\Entity\ClassSheetContentRenderer;
use Tests\TestCase;

/**
 * Crâ est l’essai d’enveloppe classe (sections par niveau + tableau récap).
 */
final class PlayableCraClassSheetTest extends TestCase
{
    public function test_cra_has_class_progression_envelope(): void
    {
        $breeds = require database_path('seeders/data/playable-breeds.php');
        $cra = collect($breeds)->firstWhere('name', 'Crâ');

        $this->assertIsArray($cra);
        $this->assertArrayHasKey('class_progression', $cra);
        $this->assertSame('d6', $cra['class_progression']['hit_die']);
        $this->assertTrue($cra['class_progression']['levels'][4]['carac_or_trait']);
        $this->assertCount(4, $cra['class_progression']['levels'][1]['spells'][0]);
        $this->assertSame('Flèche guidée par le Wakfu', $cra['class_progression']['levels'][3]['class_capability']);
    }

    public function test_cra_html_has_per_level_sections_and_recap(): void
    {
        $breeds = require database_path('seeders/data/playable-breeds.php');
        $cra = collect($breeds)->firstWhere('name', 'Crâ');
        $sections = (new ClassSheetContentRenderer)->sections($cra);

        $titles = array_column($sections, 'title');
        $this->assertContains('Texte', $titles);
        $this->assertContains('Niveau 1', $titles);
        $this->assertContains('Niveau 4', $titles);
        $this->assertContains('Niveau 16', $titles);

        $html = implode('', array_column($sections, 'content'));
        $this->assertStringContainsString('spécialisation', $html);
        $this->assertStringContainsString('data-breed-level-spells="4"', $html);
        $this->assertStringContainsString('cartes ci-dessous', $html);
        $this->assertStringContainsString('niveau × (nouveau mod. − ancien mod.)', $html);
        $this->assertStringNotContainsString('<table>', $html);
        $this->assertStringNotContainsString('palier de spé', $html);
    }
}
