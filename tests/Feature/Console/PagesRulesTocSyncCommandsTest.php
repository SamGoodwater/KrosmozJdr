<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Page;
use App\Models\Section;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class PagesRulesTocSyncCommandsTest extends TestCase
{
    private string $fixtureRoot = '';

    protected function tearDown(): void
    {
        if ($this->fixtureRoot !== '' && is_dir($this->fixtureRoot)) {
            $this->removeDirectory($this->fixtureRoot);
        }

        parent::tearDown();
    }

    public function test_cms_edit_is_exported_then_markdown_edit_reimported(): void
    {
        $toc = $this->writeFixtureBook();

        $import = Artisan::call('pages:import-rules-toc', [
            'path' => $toc,
            '--force-content' => true,
        ]);
        $this->assertSame(0, $import);

        $page = Page::query()->where('slug', 'regles-1-1-presentation-du-jeu')->first();
        $this->assertNotNull($page);

        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('title', 'Objectifs du système')
            ->first();
        $this->assertNotNull($section);

        $payload = ['content' => '<p>Le système vise la <strong>clarté CMS</strong>.</p>'];
        $section->data = $payload;
        $section->params = $payload;
        $section->save();

        $export = Artisan::call('pages:export-rules-toc', ['path' => $toc]);
        $this->assertSame(0, $export);

        $mdPath = $this->fixtureRoot.'/1-Introduction/1.1-presentation-du-jeu/1.1.1-concept-general.md';
        $exported = (string) file_get_contents($mdPath);
        $this->assertStringContainsString('clarté CMS', $exported);
        $this->assertStringContainsString('[[kref:characteristic:action_points_creature|PA]]', $exported);

        file_put_contents($mdPath, str_replace('clarté CMS', 'clarté markdown', $exported));

        $reimport = Artisan::call('pages:import-rules-toc', [
            'path' => $toc,
            '--force-content' => true,
        ]);
        $this->assertSame(0, $reimport);

        $section->refresh();
        $html = (string) (($section->data['content'] ?? '') ?: '');
        $this->assertStringContainsString('clarté markdown', $html);
        $this->assertStringNotContainsString('clarté CMS', $html);
    }

    public function test_import_without_force_content_keeps_existing_cms_html(): void
    {
        $toc = $this->writeFixtureBook();

        Artisan::call('pages:import-rules-toc', [
            'path' => $toc,
            '--force-content' => true,
        ]);

        $section = Section::query()->where('title', 'Objectifs du système')->first();
        $this->assertNotNull($section);
        $section->data = ['content' => '<p>Version CMS conservée.</p>'];
        $section->params = ['content' => '<p>Version CMS conservée.</p>'];
        $section->save();

        file_put_contents(
            $this->fixtureRoot.'/1-Introduction/1.1-presentation-du-jeu/1.1.1-concept-general.md',
            "# 1.1.1. Concept général\n\n**Description** : ignore.\n\n## Objectifs du système\n\nTexte markdown écrasé.\n"
        );

        Artisan::call('pages:import-rules-toc', ['path' => $toc]);

        $section->refresh();
        $this->assertStringContainsString('Version CMS conservée', (string) ($section->data['content'] ?? ''));
        $this->assertStringNotContainsString('Texte markdown écrasé', (string) ($section->data['content'] ?? ''));
    }

    private function writeFixtureBook(): string
    {
        $this->fixtureRoot = sys_get_temp_dir().'/krosmoz-rules-'.uniqid('', true);
        $dir = $this->fixtureRoot.'/1-Introduction/1.1-presentation-du-jeu';
        mkdir($dir, 0775, true);

        $toc = $this->fixtureRoot.'/TABLE_DES_MATIERES.md';
        file_put_contents($toc, <<<'MD'
# Table des Matières

## 1. Introduction

### 1.1 Présentation du jeu

- **1.1.1** Concept général
MD);

        file_put_contents($dir.'/1.1.1-concept-general.md', <<<'MD'
# 1.1.1. Concept général

**Description** : Texte initial avec [[kref:characteristic:action_points_creature|PA]].

## Objectifs du système

Le système vise la clarté.

### À retenir

- Point clé
MD);

        return $toc;
    }

    private function removeDirectory(string $path): void
    {
        $items = scandir($path);
        if (! is_array($items)) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $full = $path.DIRECTORY_SEPARATOR.$item;
            if (is_dir($full)) {
                $this->removeDirectory($full);
            } else {
                unlink($full);
            }
        }
        rmdir($path);
    }
}
