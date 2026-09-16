<?php

declare(strict_types=1);

namespace Tests\Unit\Cms;

use Tests\TestCase;

/**
 * Garde-fous des canons publiés dans L’Essentiel (seed `essential-pages.php`).
 */
final class EssentialPagesCanonTest extends TestCase
{
    /** @var array<string, array<string, mixed>> */
    private array $pages;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pages = require database_path('seeders/data/essential-pages.php');
    }

    public function test_publishes_eight_essentiel_pages(): void
    {
        $this->assertCount(8, $this->pages);
        $this->assertArrayHasKey('bien-demarrer', $this->pages);
        $this->assertArrayHasKey('creation', $this->pages);
        $this->assertArrayHasKey('combat', $this->pages);
        $this->assertArrayHasKey('sante-etats', $this->pages);
    }

    public function test_creation_uses_even_levels_and_level_one_cap(): void
    {
        $html = $this->flattenHtml($this->pages['creation']);

        $this->assertStringContainsString('niveaux pairs', $html);
        $this->assertStringContainsString('score <strong>13</strong>', $html);
        $this->assertStringContainsString('19 classes', $html);
        $this->assertStringContainsString('6 jouables', $html);
        $this->assertStringNotContainsString('Force 16', $html);
    }

    public function test_combat_keeps_single_resolution_table_and_crit_canon(): void
    {
        $combat = $this->flattenHtml($this->pages['combat']);
        $sorts = $this->flattenHtml($this->pages['sorts-aptitudes']);

        $this->assertStringContainsString('dés doublés', $combat);
        $this->assertStringContainsString('1d20 + mod (carac du sort)', $combat);
        $this->assertStringNotContainsString('1d20 + mod (carac du sort)', $sorts);
        $this->assertStringContainsString('Combat — Résoudre', $sorts);
    }

    public function test_health_formula_matches_life_points_creature_seed(): void
    {
        $html = $this->flattenHtml($this->pages['sante-etats']);

        $this->assertStringContainsString('max du dé de classe', $html);
        $this->assertStringContainsString('mod. Vitalité × niveau', $html);
        $this->assertStringNotContainsString('Vitalité × 10', $html);
        $this->assertStringNotContainsString('classe + niveau +', $html);
    }

    public function test_mj_bullets_and_world_anchor_are_present(): void
    {
        $start = $this->flattenHtml($this->pages['bien-demarrer']);

        $this->assertStringContainsString('Krosmoz', $start);
        $this->assertStringContainsString('Puces MJ', $this->pages['bien-demarrer']['sections'][1]['title'] ?? '');
        $this->assertStringContainsString('5.1.2', $start);
        $this->assertStringContainsString('−5 / +5', $start);
    }

    /**
     * @param  array<string, mixed>  $page
     */
    private function flattenHtml(array $page): string
    {
        $chunks = [(string) ($page['intro_html'] ?? '')];
        foreach ($page['sections'] ?? [] as $section) {
            $chunks[] = (string) ($section['html'] ?? '');
        }

        return implode("\n", $chunks);
    }
}
