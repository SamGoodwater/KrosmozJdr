<?php

declare(strict_types=1);

namespace Tests\Unit\Cms;

use PHPUnit\Framework\TestCase;

/**
 * Garde-fous de l'intensification (3.3.6) et du gabarit de fiche (2.4.2.6).
 * Lit les Markdown : pas de base, pas de bootstrap Laravel.
 */
final class IntensificationCanonTest extends TestCase
{
    public function test_toc_lists_intensification_as_3_3_6(): void
    {
        $toc = file_get_contents(dirname(__DIR__, 3).'/private/game/rules/TABLE_DES_MATIERES.md');

        $this->assertIsString($toc);
        $this->assertStringContainsString('**3.3.6** Intensification', $toc);
    }

    public function test_intensification_uses_three_character_level_tiers(): void
    {
        $path = dirname(__DIR__, 3).'/private/game/rules/3-Jouer/3.3-sorts/3.3.6-intensification.md';
        $this->assertFileExists($path);

        $md = file_get_contents($path);
        $this->assertIsString($md);
        $this->assertStringContainsString('**13, 16 et 20**', $md);
        $this->assertStringContainsString('5d6+mod', $md);
        $this->assertStringContainsString('Format de fiche', $md);
        $this->assertStringContainsString('pas un 13', $md);
    }

    public function test_specialization_sheet_format_is_locked(): void
    {
        $path = dirname(__DIR__, 3).'/private/game/rules/2-Creer-un-personnage/2.4-choisir-sa-specialisation/2.4.2-systeme-de-specialisation.md';
        $md = file_get_contents($path);

        $this->assertIsString($md);
        $this->assertStringContainsString('## 2.4.2.6. Format de fiche', $md);
        $this->assertStringContainsString('Aptitude (automatique) : exactement 1', $md);
        $this->assertStringContainsString('2 seulement si la fiche', $md);
        $this->assertStringContainsString('playable-specializations/erudit.php', $md);
    }
}
