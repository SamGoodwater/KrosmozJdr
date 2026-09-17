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
