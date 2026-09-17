<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\RulesHtmlSectionSplitter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RulesHtmlSectionSplitterTest extends TestCase
{
    #[Test]
    public function it_splits_preamble_and_h2_sections(): void
    {
        $html = '<p><strong>Description</strong> : intro.</p>'
            .'<h2>1.2.1.1. Mécanique d20</h2><p>Corps A.</p>'
            .'<h2>1.2.1.2. Classes de difficulté</h2><p>Corps B.</p>';

        $chunks = RulesHtmlSectionSplitter::split($html);

        $this->assertCount(3, $chunks);
        $this->assertSame('Résumé', $chunks[0]['title']);
        $this->assertStringContainsString('intro', $chunks[0]['html']);
        $this->assertSame('Mécanique d20', $chunks[1]['title']);
        $this->assertSame('Classes de difficulté', $chunks[2]['title']);
    }

    #[Test]
    public function it_skips_contenu_and_sources_sections(): void
    {
        $html = '<h2>Contenu</h2><ul><li>item</li></ul>'
            .'<h2>Mécanique d20</h2><p>Corps.</p>'
            .'<h2>Sources</h2><p>Ref.</p>';

        $chunks = RulesHtmlSectionSplitter::split($html);

        $this->assertCount(1, $chunks);
        $this->assertSame('Mécanique d20', $chunks[0]['title']);
        $this->assertStringNotContainsString('Ref.', $chunks[0]['html']);
    }

    #[Test]
    public function it_peels_a_retenir_into_its_own_section(): void
    {
        $html = '<h2>Tests opposés</h2><p>Corps.</p>'
            .'<h3>À retenir</h3><ul><li>Point clé</li></ul>';

        $chunks = RulesHtmlSectionSplitter::split($html);

        $this->assertCount(2, $chunks);
        $this->assertSame('Tests opposés', $chunks[0]['title']);
        $this->assertSame('Corps.', trim(strip_tags($chunks[0]['html'])));
        $this->assertSame('À retenir', $chunks[1]['title']);
        $this->assertStringContainsString('Point clé', $chunks[1]['html']);
    }

    #[Test]
    public function it_returns_single_chunk_when_no_headings(): void
    {
        $html = '<p>Seul paragraphe.</p>';

        $chunks = RulesHtmlSectionSplitter::split($html);

        $this->assertCount(1, $chunks);
        $this->assertSame('Résumé', $chunks[0]['title']);
    }
}
