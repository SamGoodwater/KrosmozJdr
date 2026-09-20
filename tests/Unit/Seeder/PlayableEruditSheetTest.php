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
            $this->assertContains('emplacement libre', $types, 'Palier '.$level.' sans option.');

            foreach ($data['aptitudes'] ?? [] as $aptitude) {
                $aptitudes[(int) $level] = $aptitude['name'];
            }
        }

        $this->assertSame([
            3 => 'Politicien',
            9 => 'Façonneur de sorts',
            15 => 'Expertise en Wakfu',
        ], $aptitudes);
    }

    public function test_renderer_omits_draft_banner_for_playable_erudit(): void
    {
        $spec = require database_path('seeders/data/playable-specializations/erudit.php');
        $html = implode('', array_column((new DraftSpecializationContentRenderer)->sections($spec), 'content'));

        $this->assertStringNotContainsString('Brouillon', $html);
        $this->assertStringContainsString('<h2>Capacités</h2>', $html);
        $this->assertStringContainsString('Politicien', $html);
        $this->assertStringContainsString('Maîtrise des capacités', $html);
    }
}
