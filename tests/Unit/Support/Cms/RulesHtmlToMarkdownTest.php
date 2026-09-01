<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\KrefShortcodeReplacer;
use App\Support\Cms\RulesHtmlToMarkdown;
use App\Support\Cms\RulesMarkdownFileAssembler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RulesHtmlToMarkdownTest extends TestCase
{
    #[Test]
    public function it_converts_paragraphs_emphasis_and_lists(): void
    {
        $html = '<p><strong>Description</strong> : intro.</p>'
            .'<ul><li><strong>Base</strong> : d20</li><li>Univers Dofus</li></ul>';

        $markdown = RulesHtmlToMarkdown::convert($html);

        $this->assertStringContainsString('**Description** : intro.', $markdown);
        $this->assertStringContainsString('- **Base** : d20', $markdown);
        $this->assertStringContainsString('- Univers Dofus', $markdown);
    }

    #[Test]
    public function it_converts_blockquote_and_table(): void
    {
        $html = '<blockquote><p><strong>Règles rapides</strong></p><ul><li>Point</li></ul></blockquote>'
            .'<table><thead><tr><th>DD</th><th>Difficulté</th></tr></thead>'
            .'<tbody><tr><td>15</td><td>Moyen</td></tr></tbody></table>';

        $markdown = RulesHtmlToMarkdown::convert($html);

        $this->assertStringContainsString('> **Règles rapides**', $markdown);
        $this->assertStringContainsString('> - Point', $markdown);
        $this->assertStringContainsString('| DD | Difficulté |', $markdown);
        $this->assertStringContainsString('| 15 | Moyen |', $markdown);
    }

    #[Test]
    public function it_converts_kref_spans_inside_html(): void
    {
        $inner = (new KrefShortcodeReplacer)->replace(
            '[[kref:characteristic:strength_creature|Force]]'
        );
        $html = '<p>Modificateur : '.$inner.'.</p>';

        $markdown = RulesHtmlToMarkdown::convert($html);

        $this->assertStringContainsString(
            '[[kref:characteristic:strength_creature|Force]]',
            $markdown
        );
        $this->assertStringNotContainsString('<span', $markdown);
    }

    #[Test]
    public function it_assembles_resume_body_and_preserves_sources(): void
    {
        $existing = "# 1.2.1. Jets\n\n## Sources\n\n## Source : Doc\n**Provenance** : `foo.md`\n";
        $markdown = RulesMarkdownFileAssembler::assemble('1.2.1', 'Jets de dés', [
            ['title' => 'Résumé', 'html' => '<p><strong>Description</strong> : intro.</p>'],
            ['title' => 'Mécanique d20', 'html' => '<p>Le d20 résout les actions.</p>'],
            ['title' => 'À retenir', 'html' => '<ul><li>Formule de base</li></ul>'],
        ], $existing);

        $this->assertStringContainsString('# 1.2.1. Jets de dés', $markdown);
        $this->assertStringContainsString('**Description** : intro.', $markdown);
        $this->assertStringContainsString('## Contenu', $markdown);
        $this->assertStringContainsString('- **Mécanique d20**', $markdown);
        $this->assertStringContainsString('## Mécanique d20', $markdown);
        $this->assertStringContainsString('### À retenir', $markdown);
        $this->assertStringContainsString('## Sources', $markdown);
        $this->assertStringContainsString('**Provenance** : `foo.md`', $markdown);
    }
}
