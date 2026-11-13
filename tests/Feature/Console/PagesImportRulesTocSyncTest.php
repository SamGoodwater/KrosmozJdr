<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Section;
use App\Models\User;
use App\Support\Cms\SectionContentSync;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

class PagesImportRulesTocSyncTest extends TestCase
{
    use RefreshDatabase;

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(['email' => User::SYSTEM_USER_EMAIL, 'role' => User::ROLE_SUPER_ADMIN]);

        $this->dir = sys_get_temp_dir().'/krosmoz-sync-'.uniqid('', true);
        mkdir($this->dir, 0775, true);
        file_put_contents($this->dir.'/TABLE_DES_MATIERES.md', <<<'MD'
## 1. Introduction

### 1.1 Présentation du jeu

- **1.1.1** Concept général
MD);
        $this->writeRule('Première version.');
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->dir.'/*') ?: []);
        @rmdir($this->dir);

        parent::tearDown();
    }

    public function test_untouched_section_follows_markdown_and_cms_edit_is_kept(): void
    {
        $this->import()->assertSuccessful();
        $this->assertStringContainsString('Première version.', $this->content());

        $this->writeRule('Deuxième version.');
        $this->import()->expectsOutputToContain('Sections mises à jour depuis les .md : 1')->assertSuccessful();
        $this->assertStringContainsString('Deuxième version.', $this->content());

        $section = $this->section();
        $section->update([
            'data' => array_merge($section->data, ['content' => '<p>Édité dans le CMS.</p>']),
            'params' => array_merge($section->params, ['content' => '<p>Édité dans le CMS.</p>']),
        ]);

        $this->writeRule('Troisième version.');
        $this->import()->expectsOutputToContain('Sections en conflit (conservées) : 1')->assertSuccessful();
        $this->assertStringContainsString('Édité dans le CMS.', $this->content());

        $this->import(['--accept' => '1.1.1'])->assertSuccessful();
        $this->assertStringContainsString('Troisième version.', $this->content());
        $this->assertSame(SectionContentSync::SAME, SectionContentSync::plan($this->section(), $this->content()));
    }

    public function test_renamed_toc_title_keeps_cms_edit_instead_of_purging(): void
    {
        $this->import()->assertSuccessful();

        $section = $this->section();
        $originalId = (int) $section->id;
        $section->update([
            'data' => array_merge($section->data, ['content' => '<p>Édité dans le CMS.</p>']),
            'params' => array_merge($section->params, ['content' => '<p>Édité dans le CMS.</p>']),
        ]);

        file_put_contents($this->dir.'/TABLE_DES_MATIERES.md', <<<'MD'
## 1. Introduction

### 1.1 Présentation du jeu

- **1.1.1** Concept révisé
MD);

        $this->import()->expectsOutputToContain('Sections en conflit (conservées) : 1')->assertSuccessful();

        $section = $this->section();
        $this->assertSame($originalId, (int) $section->id);
        $this->assertSame('regle-1-1-1-concept-revise', $section->slug);
        $this->assertStringContainsString('Édité dans le CMS.', $this->content());
        $this->assertSame(1, Section::query()->count());
        $this->assertSame(0, Section::onlyTrashed()->count());
    }

    public function test_renamed_toc_title_without_cms_edit_follows_markdown(): void
    {
        $this->import()->assertSuccessful();
        $originalId = (int) $this->section()->id;

        file_put_contents($this->dir.'/TABLE_DES_MATIERES.md', <<<'MD'
## 1. Introduction

### 1.1 Présentation du jeu

- **1.1.1** Concept révisé
MD);
        $this->writeRule('Deuxième version.');

        $this->import()->expectsOutputToContain('Sections mises à jour depuis les .md : 1')->assertSuccessful();

        $section = $this->section();
        $this->assertSame($originalId, (int) $section->id);
        $this->assertSame('regle-1-1-1-concept-revise', $section->slug);
        $this->assertStringContainsString('Deuxième version.', $this->content());
        $this->assertSame(1, Section::query()->count());
    }

    public function test_dry_run_writes_nothing(): void
    {
        $this->import(['--dry-run' => true])
            ->expectsOutputToContain('Sections créées : 1')
            ->assertSuccessful();

        $this->assertSame(0, Section::query()->count());
    }

    private function writeRule(string $sentence): void
    {
        file_put_contents($this->dir.'/1.1.1-concept-general.md', "# 1.1.1. Concept général\n\n{$sentence}\n");
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function import(array $options = []): PendingCommand
    {
        return $this->artisan('pages:import-rules-toc', ['path' => $this->dir.'/TABLE_DES_MATIERES.md'] + $options);
    }

    private function section(): Section
    {
        return Section::query()->where('slug', 'like', 'regle-1-1-1-%')->orderBy('order')->firstOrFail();
    }

    private function content(): string
    {
        return SectionContentSync::currentContent($this->section());
    }
}
