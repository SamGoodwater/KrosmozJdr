<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\RulesMarkdownInternalRulesLinkToPageKref;
use App\Support\Cms\RulesMarkdownPlainReferenceToKref;
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

        $this->assertStringContainsString('[[kref:pageSection:regles-3-2-combat@regle-3-2-4-cible|la cible]]', $out);
        $this->assertStringNotContainsString('3.2.4-cible.md', $out);

        $this->removeDirectory($base);
    }

    public function test_replaces_directory_link_with_page_kref(): void
    {
        $base = sys_get_temp_dir().'/krosmoz_rules_kref_dir_'.bin2hex(random_bytes(4));
        $this->createDirectory($base.'/2-Creer-un-personnage/2.1-intro');
        $this->createDirectory($base.'/2-Creer-un-personnage/2.3-choisir-sa-classe');

        $toc = <<<'MD'
## 2. Créer

### 2.3 Choisir sa classe

- **2.3.1** Généralités

MD;
        file_put_contents($base.'/TABLE_DES_MATIERES.md', $toc);

        $current = $base.'/2-Creer-un-personnage/2.1-intro/2.1.md';
        file_put_contents($current, "- [Section 2.3](../2.3-choisir-sa-classe/)\n");

        $tree = RulesTocParser::parse($base.'/TABLE_DES_MATIERES.md');
        $index = RulesTocSlugIndex::fromTree($tree);
        $md = file_get_contents($current);
        $this->assertIsString($md);
        $out = RulesMarkdownInternalRulesLinkToPageKref::apply($md, $current, $base, $index);

        $this->assertStringContainsString('[[kref:page:regles-2-3-choisir-sa-classe|Section 2.3]]', $out);

        $this->removeDirectory($base);
    }

    public function test_plain_section_reference_becomes_page_kref(): void
    {
        $base = sys_get_temp_dir().'/krosmoz_rules_plain_'.bin2hex(random_bytes(4));
        $this->createDirectory($base.'/2-Creer-un-personnage/2.1-intro');

        $toc = <<<'MD'
## 2. Créer

### 2.3 Choisir sa classe

- **2.3.1** Généralités

MD;
        file_put_contents($base.'/TABLE_DES_MATIERES.md', $toc);

        $current = $base.'/2-Creer-un-personnage/2.1-intro/2.1.md';
        file_put_contents($current, "Pour plus de détails, consulte la section **2.3. Choisir sa classe**.\n");

        $tree = RulesTocParser::parse($base.'/TABLE_DES_MATIERES.md');
        $index = RulesTocSlugIndex::fromTree($tree);
        $md = file_get_contents($current);
        $this->assertIsString($md);
        $out = RulesMarkdownPlainReferenceToKref::apply($md, $index);

        $this->assertStringContainsString('[[kref:page:regles-2-3-choisir-sa-classe|Choisir sa classe]]', $out);

        $this->removeDirectory($base);
    }

    public function test_plain_l3_section_reference_becomes_page_section_kref(): void
    {
        $base = sys_get_temp_dir().'/krosmoz_rules_plain_l3_'.bin2hex(random_bytes(4));
        $this->createDirectory($base.'/1-Introduction/1.2-concepts');

        $toc = <<<'MD'
## 3. Jouer

### 3.2 Combat

- **3.2.5** Traits et états

MD;
        file_put_contents($base.'/TABLE_DES_MATIERES.md', $toc);

        $current = $base.'/1-Introduction/1.2-concepts/1.2.4.md';
        file_put_contents($current, "Consulte la section **3.2.5. Traits et états**.\n");

        $tree = RulesTocParser::parse($base.'/TABLE_DES_MATIERES.md');
        $index = RulesTocSlugIndex::fromTree($tree);
        $out = RulesMarkdownPlainReferenceToKref::apply(file_get_contents($current) ?: '', $index);

        $this->assertStringContainsString('[[kref:pageSection:regles-3-2-combat@regle-3-2-5-traits-et-etats|Traits et états]]', $out);

        $this->removeDirectory($base);
    }

    public function test_plain_section_with_arrow_resolves_l3_keyword(): void
    {
        $base = sys_get_temp_dir().'/krosmoz_rules_arrow_'.bin2hex(random_bytes(4));
        $this->createDirectory($base.'/2-Creer/2.2-caracs');

        $toc = <<<'MD'
## 4. Monde

### 4.3 Métiers

- **4.3.4** Forgemagie

MD;
        file_put_contents($base.'/TABLE_DES_MATIERES.md', $toc);

        $current = $base.'/2-Creer/2.2-caracs/2.2.3.md';
        file_put_contents($current, "Voir section **4.3. Métiers → Forgemagie**.\n");

        $tree = RulesTocParser::parse($base.'/TABLE_DES_MATIERES.md');
        $index = RulesTocSlugIndex::fromTree($tree);
        $out = RulesMarkdownPlainReferenceToKref::apply(file_get_contents($current) ?: '', $index);

        $this->assertStringContainsString('[[kref:pageSection:regles-4-3-metiers@regle-4-3-4-forgemagie|Forgemagie]]', $out);

        $this->removeDirectory($base);
    }

    public function test_bibliotheque_reference_becomes_page_kref(): void
    {
        $md = 'Consulte la **Bibliothèque → Les classes** sur le site.';
        $out = RulesMarkdownPlainReferenceToKref::apply($md, RulesTocSlugIndex::fromTree([]));

        $this->assertStringContainsString('[[kref:page:bibliotheque-breed|Bibliothèque → Les classes]]', $out);
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
