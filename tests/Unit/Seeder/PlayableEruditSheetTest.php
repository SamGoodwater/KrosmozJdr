<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use Database\Seeders\Entity\DraftSpecializationContentRenderer;
use Tests\TestCase;

final class PlayableEruditSheetTest extends TestCase
{
    public function test_erudit_follows_gabarit_with_three_aptitudes(): void
    {
        $spec = require database_path('seeders/data/playable-specializations/erudit.php');

        $this->assertTrue($spec['playable']);
        $this->assertSame('Érudit', $spec['name']);
        $this->assertSame([1, 3, 6, 9, 12, 15, 20], array_keys($spec['levels']));

        $aptitudes = [];
        foreach ($spec['levels'] as $level => $data) {
            $caps = $data['capacities'] ?? [];
            $this->assertNotEmpty($caps, 'Palier '.$level.' sans capacité.');
            $types = array_column($caps, 'type');
            $this->assertContains('garantie', $types, 'Palier '.$level.' sans garantie.');
            $this->assertContains('choix', $types, 'Palier '.$level.' sans option.');

            foreach ($data['aptitudes'] ?? [] as $aptitude) {
                $aptitudes[(int) $level] = $aptitude['name'];
            }
        }

        $this->assertSame([
            3 => 'Politicien',
            9 => 'Mémoire des formules',
            15 => 'Expertise en Wakfu',
        ], $aptitudes);
    }

    public function test_renderer_omits_draft_banner_for_playable_erudit(): void
    {
        $spec = require database_path('seeders/data/playable-specializations/erudit.php');
        $html = implode('', array_column((new DraftSpecializationContentRenderer)->sections($spec), 'content'));

        $this->assertStringNotContainsString('Brouillon', $html);
        $this->assertStringContainsString('Comment lire un palier', $html);
        $this->assertStringContainsString('choix entre X et Y', $html);
        $this->assertStringContainsString('jamais</strong> de points de caractéristique', $html);
        $this->assertStringNotContainsString('+2 points', $html);
        $this->assertStringNotContainsString('emplacement libre', $html);
        $this->assertStringNotContainsString('emplacement vide', $html);
        $this->assertStringContainsString('Choix entre [[kref:entity:capabilities:Identification|Identification]] et [[kref:entity:capabilities:Recherche approfondie|Recherche approfondie]]', $html);
        $this->assertStringContainsString('Choix entre [[kref:entity:capabilities:Compréhension des langues|Compréhension des langues]] et [[kref:entity:capabilities:Main du mage|Main du mage]]', $html);
        $this->assertStringContainsString('Choix entre [[kref:entity:capabilities:Zaap de poche|Zaap de poche]], [[kref:entity:capabilities:Télékinésie|Télékinésie]] et un trait', $html);
        $this->assertStringContainsString('<h2>Capacités</h2>', $html);
        $this->assertStringContainsString('[[kref:entity:capabilities:Politicien|Politicien]]', $html);
        $this->assertStringContainsString('[[kref:characteristic:wisdom_creature|Sagesse]]', $html);
        $this->assertStringContainsString('[[kref:characteristic:intelligence_creature|Intelligence]]', $html);
        $this->assertStringContainsString('[[kref:characteristic:arcana_creature|Arcanes]]', $html);
        $this->assertStringContainsString('Maîtrise des capacités', $html);
    }
}
