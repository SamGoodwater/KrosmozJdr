<?php

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\RulesMarkdownInternalRulesLinkToPageKref;
use App\Support\Cms\RulesTocParser;
use App\Support\Cms\RulesTocSlugIndex;
use PHPUnit\Framework\TestCase;

class RulesMarkdownInternalRulesLinkToPageKrefTest extends TestCase
{
    public function test_parent_level2_from_three_part_number(): void
    {
        $this->assertSame('3.2', RulesMarkdownInternalRulesLinkToPageKref::parentLevel2NumberFromSectionNumber('3.2.4'));
    }

    public function test_parent_level2_from_two_part_number(): void
    {
        $this->assertSame('2.5', RulesMarkdownInternalRulesLinkToPageKref::parentLevel2NumberFromSectionNumber('2.5'));
    }

    public function test_replaces_relative_md_link_with_page_kref(): void
    {
        $base = sys_get_temp_dir().'/krosmoz_rules_kref_test_'.bin2hex(random_bytes(4));
        $this->createDirectory($base.'/2-Creer-un-personnage/2.2-les-caracteristiques');
        $this->createDirectory($base.'/3-Jouer/3.2-combat');
        file_put_contents($base.'/3-Jouer/3.2-combat/3.2.4-cible.md', "# Cible\n\nOK\n");

        $toc = <<<'MD'
## 3. Jouer

### 3.2 Combat

- **3.2.4** Cible

MD;
        file_put_contents($base.'/TABLE_DES_MATIERES.md', $toc);

        $current = $base.'/2-Creer-un-personnage/2.2-les-caracteristiques/2.2.1-source.md';
        file_put_contents($current, "Voir [la cible](../../3-Jouer/3.2-combat/3.2.4-cible.md).\n");

        $tree = RulesTocParser::parse($base.'/TABLE_DES_MATIERES.md');
        $index = RulesTocSlugIndex::fromTree($tree);
        $md = file_get_contents($current);
        $this->assertIsString($md);
        $out = RulesMarkdownInternalRulesLinkToPageKref::apply($md, $current, $base, $index);

        $this->assertStringContainsString('[[kref:pageSection:regles-3-2-combat@', $out);
        $this->assertStringContainsString('|la cible]]', $out);
        $this->assertStringNotContainsString('3.2.4-cible.md', $out);

        $this->removeDirectory($base);
    }

    private function createDirectory(string $path): void
    {
        if (! is_dir($path) && ! mkdir($path, 0777, true) && ! is_dir($path)) {
            $this->fail('mkdir '.$path);
        }
    }

    private function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $file) {
            $p = $file->getPathname();
            $file->isDir() ? @rmdir($p) : @unlink($p);
        }
        @rmdir($dir);
    }
}
