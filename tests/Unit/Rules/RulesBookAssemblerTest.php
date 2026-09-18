<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Services\Rules\RulesBookAssembler;
use Tests\TestCase;

class RulesBookAssemblerTest extends TestCase
{
    public function test_assembles_numbered_chapters_in_order_and_strips_krefs(): void
    {
        $root = sys_get_temp_dir().'/krosmoz-rules-'.uniqid('', true);
        mkdir($root.'/2-Deux', 0775, true);
        mkdir($root.'/1-Un', 0775, true);
        file_put_contents($root.'/Readme.md', '# ignoré');
        file_put_contents($root.'/2-Deux/2.1-beta.md', "# 2.1 Beta\n\nDeuxième.");
        file_put_contents(
            $root.'/1-Un/1.1.1-alpha.md',
            "# 1.1.1 Alpha\n\nVoir [[kref:page:foo|le chapitre]] et [lien interne](../x.md).\n\n[Discord](https://discord.gg/x)."
        );

        try {
            $assembler = new RulesBookAssembler($root);
            $markdown = $assembler->assemble();

            $this->assertStringContainsString('Krosmoz JDR — Livre de règles', $markdown);
            $this->assertStringContainsString('# 1. Introduction', $markdown);
            $this->assertStringContainsString('Bibliothèques', $markdown);
            $this->assertStringContainsString('## 1.1.1 Alpha', $markdown);
            $this->assertStringContainsString('Voir le chapitre', $markdown);
            $this->assertStringNotContainsString('[[kref:', $markdown);
            $this->assertStringNotContainsString('](../x.md)', $markdown);
            $this->assertStringContainsString('[Discord](https://discord.gg/x)', $markdown);
            $this->assertLessThan(
                strpos($markdown, 'Deuxième'),
                strpos($markdown, 'Voir le chapitre')
            );
            $this->assertCount(2, $assembler->chapterFiles());
        } finally {
            $this->removeDirectory($root);
        }
    }

    public function test_strips_print_noise_and_skips_design_annexes(): void
    {
        $root = sys_get_temp_dir().'/krosmoz-rules-'.uniqid('', true);
        mkdir($root, 0775, true);
        file_put_contents(
            $root.'/1.1.1-intro.md',
            "# 1.1.1 Intro\n\n**Description** : Phrase utile.\n\n## Contenu\n- a\n- b\n\n---\n\nCorps.\n\n**Pour plus de détails** :\n- [Section 2](../x.md)\n\n---\n\n## Sources\n\n## Source : Archive\n**Provenance** : foo.\n"
        );
        file_put_contents($root.'/6.1.3-decisions-de-design.md', "# 6.1.3 Design\n\nTrop long.\n");
        file_put_contents($root.'/6.1.1-chrono.md', "# 6.1.1 Chrono\n\nReste.\n");
        file_put_contents($root.'/5.2.3-sorts.md', "# 5.2.3 Sorts\n\nÉquilibrage MJ.\n");

        try {
            $assembler = new RulesBookAssembler($root);
            $numbers = array_column($assembler->chapterFiles(), 'number');
            $this->assertSame(['1.1.1'], $numbers);

            $markdown = $assembler->assemble();
            $this->assertStringContainsString('Phrase utile.', $markdown);
            $this->assertStringNotContainsString('**Description**', $markdown);
            $this->assertStringNotContainsString('## Contenu', $markdown);
            $this->assertStringNotContainsString('## Sources', $markdown);
            $this->assertStringNotContainsString('Provenance', $markdown);
            $this->assertStringNotContainsString('Pour plus de détails', $markdown);
            $this->assertStringContainsString('Corps.', $markdown);
            $this->assertStringNotContainsString('Trop long.', $markdown);
            $this->assertStringNotContainsString('Équilibrage MJ.', $markdown);
            $this->assertStringNotContainsString('# 5. Ressources et équilibrage', $markdown);
            $this->assertStringNotContainsString('Reste.', $markdown);

            $mj = $assembler->forAudience(RulesBookAssembler::AUDIENCE_MJ);
            $this->assertSame(['5.2.3'], array_column($mj->chapterFiles(), 'number'));
            $mjMarkdown = $mj->assemble();
            $this->assertStringContainsString('Krosmoz JDR — Atelier MJ', $mjMarkdown);
            $this->assertStringContainsString('Équilibrage MJ.', $mjMarkdown);
        } finally {
            $this->removeDirectory($root);
        }
    }

    private function removeDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($path);
    }
}
