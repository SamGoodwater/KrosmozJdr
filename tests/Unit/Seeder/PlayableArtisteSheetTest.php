<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use Database\Seeders\Entity\DraftSpecializationContentRenderer;
use Tests\TestCase;

/**
 * L’Artiste est la fiche modèle avec archétypes (2.4.2.7).
 */
final class PlayableArtisteSheetTest extends TestCase
{
    public function test_artiste_defines_four_archetypes_and_signature(): void
    {
        $spec = require database_path('seeders/data/playable-specializations/artiste.php');

        $this->assertTrue($spec['playable']);
        $this->assertSame('Artiste', $spec['name']);
        $this->assertNotSame('', trim((string) ($spec['thread'] ?? '')));
        $this->assertSame('Applaudissement', $spec['signature']['name'] ?? null);

        $archetypes = $spec['archetypes'] ?? [];
        $this->assertSame(
            ['musicien', 'saltimbanque', 'troubadour', 'voyant'],
            array_keys($archetypes)
        );

        foreach ([3, 9, 15] as $level) {
            $variantCaps = array_values(array_filter(
                $spec['levels'][$level]['capacities'] ?? [],
                static fn (array $cap): bool => isset($cap['archetype_variants'])
            ));
            $this->assertCount(1, $variantCaps, 'palier '.$level.' : un numéro d’archétype attendu.');
            $this->assertSame(array_keys($archetypes), array_keys($variantCaps[0]['archetype_variants']));
        }
    }

    public function test_artiste_html_exposes_archetypes_and_variants(): void
    {
        $spec = require database_path('seeders/data/playable-specializations/artiste.php');
        $html = implode('', array_column((new DraftSpecializationContentRenderer)->sections($spec), 'content'));

        $this->assertStringContainsString('En bref', $html);
        $this->assertStringContainsString('Styles', $html);
        $this->assertStringContainsString('Musicien', $html);
        $this->assertStringContainsString('Contre-refrain', $html);
        $this->assertStringContainsString('Applaudissement', $html);
        $this->assertStringNotContainsString('tu n’ajoutes pas un grimoire', $html);
        $this->assertStringNotContainsString('Fil rouge', $html);
    }
}
